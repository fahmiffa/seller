<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    private function checkAccess()
    {
        $role = auth()->user()->role;
        return ($role == 4 || $role == 5 || $role == 0);
    }

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
        return Unit::pluck('id');
    }

    public function index(Request $request)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $user = auth()->user();
        $allowedUnitIds = $this->getAllowedUnitIds();

        $query = Order::with(['supplier', 'unit', 'items.komoditas', 'items.satuan', 'invoice'])
            ->whereIn('unit_id', $allowedUnitIds);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('nama_supplier', 'like', "%{$search}%"))
                  ->orWhereHas('unit', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('items.komoditas', fn($k) => $k->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Daftar order',
            'data' => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $user = auth()->user();

        if ($user->role == 5) {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.komoditas_id' => 'required|exists:komoditas,id',
                'items.*.jumlah' => 'required|numeric|min:0.01',
                'items.*.satuan_id' => 'nullable|exists:satuans,satuan_id',
                'items.*.keterangan' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $unit = Unit::where('user_id', $user->id)->first();
            $order = null;

            DB::transaction(function () use ($request, $user, $unit, &$order) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'unit_id' => $unit ? $unit->id : null,
                    'supplier_id' => null,
                    'status' => 'pending',
                    'tanggal_po' => null,
                    'keterangan' => $request->keterangan ?? null,
                ]);

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => !empty($item['satuan_id']) ? $item['satuan_id'] : null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan']);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan order berhasil dikirim ke Distributor',
                'data' => $order
            ], 201);
        } else {
            // Role 4 & 0
            $allowedUnitIds = $this->getAllowedUnitIds();

            $validator = Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = null;

            DB::transaction(function () use ($request, $user, &$order) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'unit_id' => $request->unit_id,
                    'supplier_id' => $request->supplier_id,
                    'status' => 'diproses',
                    'tanggal_po' => now(),
                    'keterangan' => $request->keterangan ?? null,
                ]);

                foreach ($request->items as $item) {
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
            $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan']);

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat dan PO di-generate',
                'data' => $order
            ], 201);
        }
    }

    public function show($id)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();
        $order = Order::with(['supplier', 'unit', 'items.komoditas', 'items.satuan', 'invoice'])
            ->whereIn('unit_id', $allowedUnitIds)
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail order',
            'data' => $order
        ], 200);
    }

    public function update(Request $request, $id)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();
        $order = Order::whereIn('unit_id', $allowedUnitIds)->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $user = auth()->user();

        if ($user->role == 5) {
            if ($request->has('mark_selesai') && $order->status === 'diproses') {
                $order->update(['status' => 'selesai']);
                $this->generateInvoice($order);

                $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan', 'invoice']);
                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil diverifikasi selesai dan invoice dibuat.',
                    'data' => $order
                ], 200);
            }

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order yang sudah diproses tidak dapat diubah lagi.'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.komoditas_id' => 'required|exists:komoditas,id',
                'items.*.jumlah' => 'required|numeric|min:0.01',
                'items.*.satuan_id' => 'nullable|exists:satuans,satuan_id',
                'items.*.keterangan' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($request, $order) {
                $order->update([
                    'keterangan' => $request->keterangan ?? null,
                ]);

                $order->items()->delete();

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => $item['satuan_id'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan']);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan order berhasil diperbarui',
                'data' => $order
            ], 200);
        } else {
            // Role 4 & 0
            if ($request->has('mark_dibatalkan')) {
                $order->update(['status' => 'dibatalkan']);
                $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan']);
                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil dibatalkan',
                    'data' => $order
                ], 200);
            }

            $validator = Validator::make($request->all(), [
                'unit_id' => 'nullable|in:' . $allowedUnitIds->implode(','),
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldStatus = $order->status;

            DB::transaction(function () use ($request, $order) {
                $order->update([
                    'unit_id' => $request->unit_id ?? $order->unit_id,
                    'supplier_id' => $request->supplier_id,
                    'status' => $request->status,
                    'tanggal_po' => $request->tanggal_po ?? ($request->status === 'diproses' ? now() : $order->tanggal_po),
                    'keterangan' => $request->keterangan ?? $order->keterangan,
                ]);

                $order->items()->delete();

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'komoditas_id' => $item['komoditas_id'],
                        'jumlah' => $item['jumlah'],
                        'satuan_id' => !empty($item['satuan_id']) ? $item['satuan_id'] : null,
                        'harga_unit' => $item['harga_unit'] ?? null,
                        'harga_supplier' => $item['harga_supplier'] ?? null,
                        'keterangan' => $item['keterangan'] ?? null,
                    ]);
                }
            });

            if ($oldStatus === 'pending' && $order->status === 'diproses' && !$order->po_number) {
                $this->generatePo($order);
            }

            $order->load(['supplier', 'unit', 'items.komoditas', 'items.satuan']);

            return response()->json([
                'success' => true,
                'message' => 'Order dan verifikasi berhasil diperbarui',
                'data' => $order
            ], 200);
        }
    }

    public function destroy($id)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();
        $order = Order::whereIn('unit_id', $allowedUnitIds)->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus'
        ], 200);
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
        
        Storage::disk('public')->put('po/' . $fileName, $pdf->output());

        $order->po_document = 'po/' . $fileName;
        $order->save();
    }

    private function generateInvoice(Order $order)
    {
        if ($order->invoice) {
            return;
        }

        $order->load('items.komoditas', 'items.satuan', 'supplier', 'unit', 'user');

        $month = now()->format('n');
        $year  = now()->format('Y');
        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                   7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $invNumber = 'INV/' . str_pad($order->id, 3, '0', STR_PAD_LEFT) . '/' . ($romans[$month] ?? 'I') . '/' . $year;

        $subtotal = $order->items->sum(fn($item) => $item->jumlah * $item->harga_supplier);
        $ppn      = $subtotal * 0.11;
        $total    = $subtotal + $ppn;

        $invoice = Invoice::create([
            'order_id'       => $order->id,
            'inv_number'     => $invNumber,
            'subtotal'       => $subtotal,
            'ppn'            => $ppn,
            'total'          => $total,
            'payment_status' => 'unpaid',
            'inv_date'       => now()->toDateString(),
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.inv_pdf', compact('order', 'invoice'));
        $fileName = 'inv_' . str_replace('/', '_', $invNumber) . '.pdf';
        Storage::disk('public')->put('invoices/' . $fileName, $pdf->output());

        $invoice->inv_document = 'invoices/' . $fileName;
        $invoice->save();
    }
}
