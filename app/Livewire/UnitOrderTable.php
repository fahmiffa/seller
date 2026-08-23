<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class UnitOrderTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'statusFilter', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        // Ambil unit milik user role 5
        $unitIds = Unit::where('user_id', $user->id)->pluck('id');

        $query = Order::query()
            ->with(['items.komoditas', 'items.satuan'])
            ->whereIn('unit_id', $unitIds);

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('keterangan', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhereHas('items.komoditas', function ($kq) {
                        $kq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $orders = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.unit-order-table', [
            'orders' => $orders
        ])->layout('layouts.app', [
            'header' => 'Daftar Pengajuan Order'
        ]);
    }
}
