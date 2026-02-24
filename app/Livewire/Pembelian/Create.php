<?php

namespace App\Livewire\Pembelian;

use Livewire\Component;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $supplier_id;
    public $tanggal_pembelian;
    public $items_list = []; // To store items added to the purchase

    public $search_item = '';
    public $show_items = false;

    public $item_temp_id;
    public $item_temp_nama;
    public $qty_temp = 1;
    public $harga_temp;

    public function mount()
    {
        $this->tanggal_pembelian = date('Y-m-d');
        // Initial row is empty
    }

    public function addItem()
    {
        $this->validate([
            'item_temp_id' => 'required|exists:items,item_id',
            'qty_temp' => 'required|integer|min:1',
            'harga_temp' => 'required|numeric|min:0',
        ]);

        $item = Item::find($this->item_temp_id);

        $this->items_list[] = [
            'item_id' => $item->item_id,
            'nama_item' => $item->nama_item,
            'qty' => $this->qty_temp,
            'harga_beli' => $this->harga_temp,
            'subtotal' => $this->qty_temp * $this->harga_temp,
        ];

        // Reset temp
        $this->item_temp_id = null;
        $this->item_temp_nama = '';
        $this->search_item = '';
        $this->qty_temp = 1;
        $this->harga_temp = null;
        $this->show_items = false;
    }

    public function removeItem($index)
    {
        unset($this->items_list[$index]);
        $this->items_list = array_values($this->items_list);
    }

    public function getTotalProperty()
    {
        return collect($this->items_list)->sum('subtotal');
    }

    public function save()
    {
        if (Auth::user()->saldo <= Auth::user()->limit) {
            $this->dispatch('alert', message: 'Saldo limit, tidak bisa melakukan transaksi!');
            return;
        }

        $this->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'tanggal_pembelian' => 'required|date',
            'items_list' => 'required|array|min:1',
        ]);

        DB::transaction(function () {
            $pembelian = Pembelian::create([
                'supplier_id' => $this->supplier_id,
                'user_id' => Auth::id(),
                'tanggal_pembelian' => $this->tanggal_pembelian,
                'total_pembelian' => $this->total,
            ]);

            foreach ($this->items_list as $item) {
                DetailPembelian::create([
                    'pembelian_id' => $pembelian->pembelian_id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update stock if it's goods
                $product = Item::find($item['item_id']);
                if ($product->tipe_item === 'barang') {
                    $product->increment('stok', $item['qty']);
                }
            }
        });

        session()->flash('success', 'Pembelian berhasil disimpan.');
        return $this->redirect(route('pembelians.index'), navigate: true);
    }

    public function selectItem($id, $nama)
    {
        $this->item_temp_id = $id;
        $this->item_temp_nama = $nama;
        $this->search_item = $nama;
        $this->show_items = false;

        $item = Item::find($id);
        if ($item) {
            $this->harga_temp = $item->harga_beli;
        }
    }

    public function updatedSearchItem($value)
    {
        if ($value) {
            $this->show_items = true;
        } else {
            $this->show_items = false;
            $this->item_temp_id = null;
            $this->item_temp_nama = '';
        }
    }

    public function render()
    {
        $items = Item::where('user_id', Auth::id())
            ->where('tipe_item', 'barang')
            ->when($this->search_item, function ($query) {
                $query->where('nama_item', 'like', '%' . $this->search_item . '%');
            })
            ->get();

        return view('livewire.pembelian.create', [
            'suppliers' => Supplier::where('user_id', Auth::id())->get(),
            'items' => $items, // Only goods can be purchased
        ]);
    }
}
