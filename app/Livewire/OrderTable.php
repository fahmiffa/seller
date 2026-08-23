<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $verifyModalOpen = false;
    public $verifyingOrder = null;
    public $suppliers = [];
    public $unit_id;
    public $tanggal_po;
    public $keterangan;
    public $items = [];

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

    public function openVerifyModal($orderId)
    {
        $order = Order::with('items.komoditas', 'items.satuan')->find($orderId);
        if (!$order) return;
        
        $this->verifyingOrder = $order->toArray();
        $this->unit_id = $order->unit_id;
        $this->tanggal_po = $order->tanggal_po ? $order->tanggal_po->format('Y-m-d') : date('Y-m-d');
        $this->keterangan = $order->keterangan;
        
        $this->items = [];
        foreach ($order->items as $item) {
            $this->items[] = [
                'komoditas_id' => $item->komoditas_id,
                'komoditas_name' => $item->komoditas ? $item->komoditas->name : '',
                'jumlah' => $item->jumlah,
                'satuan_id' => $item->satuan_id,
                'satuan_name' => $item->satuan ? $item->satuan->nama_satuan : '',
                'harga_unit' => $item->harga_unit,
                'harga_supplier' => $item->harga_supplier,
                'keterangan' => $item->keterangan,
            ];
        }

        $user = auth()->user();
        $this->suppliers = \App\Models\Supplier::where('user_id', $user->id)->get()->toArray();
        $this->verifyModalOpen = true;
    }

    public function closeVerifyModal()
    {
        $this->verifyModalOpen = false;
        $this->verifyingOrder = null;
    }

    public function render()
    {
        $user = auth()->user();

        // Query orders with relations
        $query = Order::query()->with(['supplier', 'unit', 'items.komoditas', 'items.satuan']);

        if ($user->role == 5) {
            // Role 5: Hanya melihat order miliknya (unit yang user_id = user->id)
            $unitIds = Unit::where('user_id', $user->id)->pluck('id');
            $query->whereIn('unit_id', $unitIds);
        } elseif ($user->role == 4) {
            // Role 4: Melihat semua order di unit-unit yang berada di bawah distributor ini
            $unitIds = Unit::where('user_id', $user->id)
                ->orWhereHas('user', function ($q) use ($user) {
                    $q->where('parent_id', $user->id);
                })
                ->pluck('id');
            $query->whereIn('unit_id', $unitIds);
        } else {
            // Role lain tidak dapat melihat
            $query->whereRaw('1 = 0');
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('keterangan', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($sq) {
                        $sq->where('nama_supplier', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('unit', function ($uq) {
                        $uq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('items.komoditas', function ($kq) {
                        $kq->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $orders = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('livewire.order-table', [
            'orders' => $orders
        ])->layout('layouts.app', [
            'header' => 'Daftar Order'
        ]);
    }
}
