<?php

namespace App\Livewire;

use App\Models\Komoditas;
use Livewire\Component;
use Livewire\WithPagination;

class KomoditasTable extends Component
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
        $komoditasList = Komoditas::query()
            ->where('user_id', auth()->user()->getOwnerId())
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.komoditas-table', [
            'komoditasList' => $komoditasList
        ])->layout('layouts.app', [
            'header' => 'Daftar Komoditas'
        ]);
    }
}
