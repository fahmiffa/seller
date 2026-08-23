<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search'       => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $user = auth()->user();

        if ($user->role != 4) {
            abort(403);
        }

        // Get distributor's allowed unit IDs (same logic as OrderController)
        $allowedUnitIds = \App\Models\Unit::where('user_id', $user->id)
            ->orWhereHas('user', fn($q) => $q->where('parent_id', $user->id))
            ->pluck('id');

        $invoices = Invoice::with(['order.unit', 'order.supplier', 'order.items.komoditas'])
            ->whereHas('order', function ($q) use ($allowedUnitIds) {
                $q->whereIn('unit_id', $allowedUnitIds);

                if ($this->search) {
                    $q->where(function ($sq) {
                        $sq->whereHas('supplier', fn($s) => $s->where('nama_supplier', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('unit', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                            ->orWhereHas('items.komoditas', fn($k) => $k->where('name', 'like', '%' . $this->search . '%'));
                    });
                }
            })
            ->when($this->statusFilter, fn($q) => $q->where('payment_status', $this->statusFilter))
            ->when($this->search, function ($q) {
                $q->orWhere('inv_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.invoice-table', compact('invoices'));
    }
}
