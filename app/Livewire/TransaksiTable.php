<?php

namespace App\Livewire;

use App\Models\Transaksi;
use Livewire\Component;
use Livewire\WithPagination;

class TransaksiTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $metode_pembayaran = '';

    protected $queryString = ['search', 'sortField', 'sortDirection', 'metode_pembayaran'];

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

    public function updatingMetodePembayaran()
    {
        $this->resetPage();
    }

    public function render()
    {
        $transaksis = Transaksi::query()
            ->where('user_id', auth()->user()->getOwnerId())
            ->where(function ($query) {
                $query->where('transaksi_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('nama', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->metode_pembayaran, function ($query) {
                $query->where('metode_pembayaran', $this->metode_pembayaran);
            })
            ->with(['customer', 'user'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.transaksi-table', [
            'transaksis' => $transaksis
        ]);
    }
}
