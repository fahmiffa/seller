<?php

namespace App\Livewire\Transaksi;

use Livewire\Component;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $customer_id;
    public $tanggal_transaksi;
    public $metode_pembayaran = 'tunai';
    public $items_list = [];
    public $search = '';
    public $showAll = false;

    public function mount()
    {
        $this->tanggal_transaksi = date('Y-m-d');
    }

    public function addToCart($itemId)
    {
        $item = Item::find($itemId);
        
        // Find if already in list
        $existingPos = -1;
        foreach ($this->items_list as $index => $listItem) {
            if ($listItem['item_id'] == $itemId) {
                $existingPos = $index;
                break;
            }
        }

        if ($existingPos !== -1) {
            $this->incrementQty($existingPos);
        } else {
            // Check stock for goods
            if ($item->tipe_item === 'barang' && $item->stok < 1) {
                session()->flash('error', 'Stok tidak mencukupi!');
                return;
            }

            $this->items_list[] = [
                'item_id' => $item->item_id,
                'nama_item' => $item->nama_item,
                'tipe_item' => $item->tipe_item,
                'qty' => 1,
                'harga_satuan' => $item->harga_jual,
                'subtotal' => $item->harga_jual,
            ];
        }
    }

    public function incrementQty($index)
    {
        $item = Item::find($this->items_list[$index]['item_id']);
        if ($item->tipe_item === 'barang' && $item->stok <= $this->items_list[$index]['qty']) {
            session()->flash('error', 'Stok terbatas!');
            return;
        }

        $this->items_list[$index]['qty']++;
        $this->items_list[$index]['subtotal'] = $this->items_list[$index]['qty'] * $this->items_list[$index]['harga_satuan'];
    }

    public function decrementQty($index)
    {
        if ($this->items_list[$index]['qty'] > 1) {
            $this->items_list[$index]['qty']--;
            $this->items_list[$index]['subtotal'] = $this->items_list[$index]['qty'] * $this->items_list[$index]['harga_satuan'];
        } else {
            $this->removeItem($index);
        }
    }

    public function clearCart()
    {
        $this->items_list = [];
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
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,kredit',
            'items_list' => 'required|array|min:1',
        ]);

        DB::transaction(function () {
            $transaksi = Transaksi::create([
                'customer_id' => $this->customer_id,
                'user_id' => Auth::id(),
                'tanggal_transaksi' => $this->tanggal_transaksi,
                'total_harga' => $this->total,
                'metode_pembayaran' => $this->metode_pembayaran,
            ]);

            foreach ($this->items_list as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product = Item::find($item['item_id']);
                if ($product->tipe_item === 'barang') {
                    $product->decrement('stok', $item['qty']);
                }
            }
        });

        session()->flash('success', 'Transaksi berhasil disimpan.');
        return $this->redirect(route('transaksis.index'));
    }

    public function render()
    {
        $query = Item::where('user_id', Auth::id())
            ->where('nama_item', 'like', '%' . $this->search . '%');

        if (!$this->showAll && empty($this->search)) {
            $query->limit(12);
        }

        $items = $query->get();

        return view('livewire.transaksi.create', [
            'customers' => Customer::where('user_id', Auth::id())->get(),
            'items' => $items,
        ]);
    }
}
