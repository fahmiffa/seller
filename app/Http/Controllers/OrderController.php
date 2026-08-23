<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Check if user has role 4 or 5
     */
    private function checkAccess()
    {
        $role = auth()->user()->role;
        if ($role != 4 && $role != 5) {
            abort(403, 'Akses ditolak. Menu Order hanya dapat diakses oleh Distributor (Role 4) dan Unit (Role 5).');
        }
    }

    /**
     * Get allowed unit IDs for current user
     */
    private function getAllowedUnitIds()
    {
        $user = auth()->user();
        if ($user->role == 5) {
            return Unit::where('user_id', $user->id)->pluck('id');
        } elseif ($user->role == 4) {
            return Unit::where('user_id', $user->id)
                ->orWhereHas('user', function ($q) use ($user) {
                    $q->where('parent_id', $user->id);
                })
                ->pluck('id');
        }
        return collect();
    }

    /**
     * Get the distributor ID for fetching suppliers & komoditas
     */
    private function getDistributorId()
    {
        $user = auth()->user();
        if ($user->role == 5) {
            return $user->parent_id ?: $user->id;
        }
        return $user->id;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAccess();
        $user = auth()->user();
        $distributorId = $this->getDistributorId();

        $komoditas = Komoditas::where('user_id', $distributorId)->get();
        $satuans = Satuan::where('user_id', $distributorId)->get();

        if ($user->role == 5) {
            // Unit mengajukan: Tidak pilih supplier & unit (otomatis unit miliknya)
            return view('orders.create', compact('komoditas', 'satuans'));
        }

        // Role 4 (Distributor): Pilih supplier, unit, dan tanggal PO
        $suppliers = Supplier::where('user_id', $distributorId)->get();
        $units = Unit::where('user_id', $user->id)
            ->orWhereHas('user', function ($q) use ($user) {
                $q->where('parent_id', $user->id);
            })
            ->get();

        return view('orders.create', compact('suppliers', 'komoditas', 'units', 'satuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAccess();
        $user = auth()->user();

        if ($user->role == 5) {
            // Unit role 5
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.komoditas_id' => 'required|exists:komoditas,id',
                'items.*.jumlah' => 'required|numeric|min:0.01',
                'items.*.satuan_id' => 'nullable|exists:satuans,satuan_id',
                'items.*.keterangan' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            // Ambil unit yang dimiliki oleh user role 5
            $unit = Unit::where('user_id', $user->id)->first();

            DB::transaction(function () use ($validated, $user, $unit) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'unit_id' => $unit ? $unit->id : null,
                    'supplier_id' => null,
                    'status' => 'pending',
                    'tanggal_po' => null,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                foreach ($validated['items'] as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => $item['satuan_id'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            return redirect()->route('orders.index')->with('success', 'Pengajuan order berhasil dikirim ke Distributor.');
        } else {
            // Distributor role 4
            $allowedUnitIds = $this->getAllowedUnitIds();

            $validated = $request->validate([
                'unit_id' => 'required|in:' . $allowedUnitIds->implode(','),
                'supplier_id' => 'required|exists:suppliers,supplier_id',
                'items' => 'required|array|min:1',
                'items.*.komoditas_id' => 'required|exists:komoditas,id',
                'items.*.jumlah' => 'required|numeric|min:0.01',
                'items.*.satuan_id' => 'nullable|exists:satuans,satuan_id',
                'items.*.harga_unit' => 'nullable|numeric|min:0',
                'items.*.harga_supplier' => 'nullable|numeric|min:0',
                'items.*.keterangan' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            $order = null;

            DB::transaction(function () use ($validated, $user, &$order) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'unit_id' => $validated['unit_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'status' => 'diproses',
                    'tanggal_po' => now(),
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                foreach ($validated['items'] as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => $item['satuan_id'] ?? null,
                        'harga_unit' => $item['harga_unit'] ?? null,
                        'harga_supplier' => $item['harga_supplier'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            $this->generatePo($order);

            return redirect()->route('orders.index')->with('success', 'Order berhasil dibuat dan PO di-generate.');
        }
    }

    private function generatePo(Order $order)
    {
        $month = $order->created_at ? $order->created_at->format('n') : now()->format('n');
        $year = $order->created_at ? $order->created_at->format('Y') : now()->format('Y');
        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $romanMonth = $romans[$month] ?? 'I';
        
        $poNumber = 'PO/' . str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/' . $romanMonth . '/' . $year;
        $order->po_number = $poNumber;

        $order->load('items.komoditas', 'items.satuan');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('orders.po_pdf', compact('order'));
        $fileName = 'po_' . str_replace('/', '_', $poNumber) . '.pdf';
        
        \Illuminate\Support\Facades\Storage::disk('public')->put('po/' . $fileName, $pdf->output());

        $order->po_document = 'po/' . $fileName;
        $order->save();
    }

    public function streamPo(Order $order)
    {
        $month = $order->created_at?->format('n') ?? now()->format('n');
        $year = $order->created_at?->format('Y') ?? now()->format('Y');

        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $poNumber = 'PO/' . str_pad($order->id, 3, '0', STR_PAD_LEFT)
            . '/' . ($romans[$month] ?? 'I')
            . '/' . $year;

        $order->po_number = $poNumber;
        $order->load('items.komoditas', 'items.satuan');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'orders.po_pdf',
            compact('order')
        );

        return $pdf->stream(
            'po_' . str_replace('/', '_', $poNumber) . '.pdf'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $this->checkAccess();
        $allowedUnitIds = $this->getAllowedUnitIds();

        if ($order->unit_id && !$allowedUnitIds->contains($order->unit_id)) {
            abort(403, 'Anda tidak memiliki akses ke order ini.');
        }

        $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $this->checkAccess();
        $allowedUnitIds = $this->getAllowedUnitIds();

        if ($order->unit_id && !$allowedUnitIds->contains($order->unit_id)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit order ini.');
        }

        $user = auth()->user();
        $distributorId = $this->getDistributorId();

        $suppliers = Supplier::where('user_id', $distributorId)->get();
        $komoditas = Komoditas::where('user_id', $distributorId)->get();

        $satuans = Satuan::where('user_id', $distributorId)->get();

        $units = Unit::where('user_id', $distributorId)
            ->orWhereHas('user', function ($q) use ($distributorId) {
                $q->where('parent_id', $distributorId);
            })
            ->get();

        $order->load('items.komoditas', 'items.satuan');

        return view('orders.edit', compact('order', 'suppliers', 'komoditas', 'units', 'satuans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->checkAccess();
        $user = auth()->user();
        $allowedUnitIds = $this->getAllowedUnitIds();

        if ($order->unit_id && !$allowedUnitIds->contains($order->unit_id)) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit order ini.');
        }

        if ($user->role == 5) {
            // Role 5 memverifikasi penerimaan (mengubah diproses menjadi selesai)
            if ($request->has('mark_selesai') && $order->status === 'diproses') {
                $order->update(['status' => 'selesai']);
                // Auto-generate invoice
                $this->generateInvoice($order);
                return redirect()->route('orders.index')->with('success', 'Order berhasil diverifikasi dan invoice dibuat.');
            }

            // Unit role 5 hanya bisa mengedit items jika status masih pending
            if ($order->status !== 'pending') {
                return redirect()->route('orders.index')->with('error', 'Order yang sudah diproses oleh Distributor tidak dapat diubah lagi.');
            }

            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.komoditas_id' => 'required|exists:komoditas,id',
                'items.*.jumlah' => 'required|numeric|min:0.01',
                'items.*.satuan_id' => 'nullable|exists:satuans,satuan_id',
                'items.*.keterangan' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            DB::transaction(function () use ($validated, $order) {
                $order->update([
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                // Delete existing items and re-create
                $order->items()->delete();

                foreach ($validated['items'] as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => $item['satuan_id'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            return redirect()->route('orders.index')->with('success', 'Pengajuan order berhasil diperbarui.');
        } else {
            // Distributor role 4: membatalkan order
            if ($request->has('mark_dibatalkan')) {
                $order->update(['status' => 'dibatalkan']);
                return redirect()->route('orders.index')->with('success', 'Order berhasil dibatalkan.');
            }

            // Distributor role 4: Verifikasi pilih supplier, unit, harga_unit, harga_supplier, status, dan tanggal PO
            $validated = $request->validate([
                'unit_id' => 'required|in:' . $allowedUnitIds->implode(','),
                'supplier_id' => 'required|exists:suppliers,supplier_id',
                'items' => 'required|array|min:1',
                'items.*.komoditas_id' => 'required|exists:komoditas,id',
                'items.*.jumlah' => 'required|numeric|min:0.01',
                'items.*.satuan_id' => 'nullable|exists:satuans,satuan_id',
                'items.*.harga_unit' => 'nullable|numeric|min:0',
                'items.*.harga_supplier' => 'nullable|numeric|min:0',
                'items.*.keterangan' => 'nullable|string',
                'status' => 'required|string|in:pending,diproses,selesai,dibatalkan',
                'tanggal_po' => 'nullable|date',
                'keterangan' => 'nullable|string',
            ]);

            $oldStatus = $order->status;

            DB::transaction(function () use ($validated, $order) {
                $order->update([
                    'unit_id' => $validated['unit_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'status' => $validated['status'],
                    'tanggal_po' => $validated['tanggal_po'] ?? null,
                    'keterangan' => $validated['keterangan'] ?? null,
                ]);

                // Delete existing items and re-create
                $order->items()->delete();

                foreach ($validated['items'] as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => $item['satuan_id'] ?? null,
                        'harga_unit' => $item['harga_unit'] ?? null,
                        'harga_supplier' => $item['harga_supplier'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            if ($oldStatus === 'pending' && $order->status === 'diproses' && !$order->po_number) {
                $this->generatePo($order);
            }

            return redirect()->route('orders.index')->with('success', 'Order dan verifikasi berhasil diperbarui.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $this->checkAccess();
        $allowedUnitIds = $this->getAllowedUnitIds();

        if ($order->unit_id && !$allowedUnitIds->contains($order->unit_id)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus order ini.');
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus.');
    }

    /**
     * Generate an invoice PDF after order is marked selesai.
     */
    private function generateInvoice(Order $order)
    {
        // Skip if invoice already exists
        if ($order->invoice) {
            return;
        }

        $order->load('items.komoditas', 'items.satuan', 'supplier', 'unit', 'user');

        $month = now()->format('n');
        $year  = now()->format('Y');
        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                   7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $invNumber = 'INV/' . str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/' . ($romans[$month] ?? 'I') . '/' . $year;

        // Calculate totals
        $subtotal = $order->items->sum(fn($item) => $item->jumlah * $item->harga_supplier);
        $ppn      = $subtotal * 0.11;
        $total    = $subtotal + $ppn;

        // Create invoice record first (so PDF can reference it)
        $invoice = Invoice::create([
            'order_id'       => $order->id,
            'inv_number'     => $invNumber,
            'subtotal'       => $subtotal,
            'ppn'            => $ppn,
            'total'          => $total,
            'payment_status' => 'unpaid',
            'inv_date'       => now()->toDateString(),
        ]);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.inv_pdf', compact('order', 'invoice'));
        $fileName = 'inv_' . str_replace('/', '_', $invNumber) . '.pdf';
        Storage::disk('public')->put('invoices/' . $fileName, $pdf->output());

        $invoice->inv_document = 'invoices/' . $fileName;
        $invoice->save();
    }
}
