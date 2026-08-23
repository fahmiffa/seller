<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    private function checkAccess()
    {
        if (auth()->user()->role != 4) {
            abort(403, 'Akses ditolak. Menu Invoice hanya dapat diakses oleh Distributor (Role 4).');
        }
    }

    /**
     * Display listing of invoices (handled by Livewire).
     */
    public function index()
    {
        $this->checkAccess();
    }

    /**
     * Mark invoice as paid.
     */
    public function markPaid(Invoice $invoice)
    {
        $this->checkAccess();

        // Make sure the invoice belongs to this distributor's orders
        $invoice->load('order.unit.user');

        $invoice->update(['payment_status' => 'paid']);

        return redirect()->route('invoices.index')->with('success', 'Invoice ' . $invoice->inv_number . ' telah ditandai lunas.');
    }

    /**
     * Stream/view the invoice PDF.
     */
    public function stream(Invoice $invoice)
    {
        $this->checkAccess();

        if (!$invoice->inv_document) {
            abort(404, 'Dokumen invoice tidak tersedia.');
        }

        $path = Storage::disk('public')->path($invoice->inv_document);

        if (!file_exists($path)) {
            // Regenerate PDF if file is missing
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
