<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    private function checkAccess()
    {
        $role = auth()->user()->role;
        return ($role == 4 || $role == 0);
    }

    private function getAllowedUnitIds()
    {
        $user = auth()->user();
        if ($user->role == 4) {
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
                'message' => 'Akses ditolak. Hanya Distributor yang dapat mengakses data Invoice.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();

        $query = Invoice::with(['order.unit', 'order.supplier', 'order.items.komoditas', 'order.items.satuan'])
            ->whereHas('order', function ($q) use ($allowedUnitIds) {
                $q->whereIn('unit_id', $allowedUnitIds);
            });

        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inv_number', 'like', "%{$search}%")
                  ->orWhereHas('order.supplier', fn($s) => $s->where('nama_supplier', 'like', "%{$search}%"))
                  ->orWhereHas('order.unit', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('order.items.komoditas', fn($k) => $k->where('name', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Daftar invoice',
            'data' => $invoices
        ], 200);
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

        $invoice = Invoice::with(['order.unit', 'order.supplier', 'order.items.komoditas', 'order.items.satuan'])
            ->whereHas('order', function ($q) use ($allowedUnitIds) {
                $q->whereIn('unit_id', $allowedUnitIds);
            })
            ->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail invoice',
            'data' => $invoice
        ], 200);
    }

    public function markPaid($id)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();

        $invoice = Invoice::whereHas('order', function ($q) use ($allowedUnitIds) {
                $q->whereIn('unit_id', $allowedUnitIds);
            })
            ->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        $invoice->update(['payment_status' => 'paid']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice ' . $invoice->inv_number . ' telah ditandai lunas.',
            'data' => $invoice
        ], 200);
    }

    public function stream($id)
    {
        if (!$this->checkAccess()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $allowedUnitIds = $this->getAllowedUnitIds();

        $invoice = Invoice::whereHas('order', function ($q) use ($allowedUnitIds) {
                $q->whereIn('unit_id', $allowedUnitIds);
            })
            ->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }

        $path = $invoice->inv_document ? Storage::disk('public')->path($invoice->inv_document) : null;

        if (!$path || !file_exists($path)) {
            $invoice->load('order.items.komoditas', 'order.items.satuan', 'order.supplier', 'order.unit', 'order.user');
            $order = $invoice->order;

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.inv_pdf', compact('order', 'invoice'));
            return $pdf->stream('inv_' . str_replace('/', '_', $invoice->inv_number) . '.pdf');
        }

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
