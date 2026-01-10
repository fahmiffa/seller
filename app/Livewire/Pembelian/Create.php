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

    public $item_temp_id;
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
        $this->qty_temp = 1;
        $this->harga_temp = null;
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

    public function updatedItemTempId($value)
    {
        if ($value) {
            $item = Item::find($value);
            $this->harga_temp = $item->harga_beli;
        }
    }

    public function render()
    {
        return view('livewire.pembelian.create', [
            'suppliers' => Supplier::where('user_id', Auth::id())->get(),
            'items' => Item::where('user_id', Auth::id())->where('tipe_item', 'barang')->get(), // Only goods can be purchased
        ]);
    }
}
