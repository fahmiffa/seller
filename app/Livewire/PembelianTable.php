<?php

namespace App\Livewire;

use App\Models\Pembelian;
use Livewire\Component;
use Livewire\WithPagination;

class PembelianTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

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

    public function render()
    {
        $pembelians = Pembelian::query()
            ->where('user_id', auth()->user()->getOwnerId())
            ->where(function ($query) {
                $query->where('pembelian_id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('nama_supplier', 'like', '%' . $this->search . '%');
                    });
            })
            ->with(['supplier', 'user'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.pembelian-table', [
            'pembelians' => $pembelians
        ]);
    }
}
