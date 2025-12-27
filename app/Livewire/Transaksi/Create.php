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
    public $items_list = []; // To store items added to the transaction

    public $item_temp_id;
    public $qty_temp = 1;
    public $harga_temp;

    public function mount()
    {
        $this->tanggal_transaksi = date('Y-m-d');
    }

    public function addItem()
    {
        $this->validate([
            'item_temp_id' => 'required|exists:items,item_id',
            'qty_temp' => 'required|integer|min:1',
            'harga_temp' => 'required|numeric|min:0',
        ]);

        $item = Item::find($this->item_temp_id);

        // Check stock for goods
        if ($item->tipe_item === 'barang' && $item->stok < $this->qty_temp) {
            session()->flash('error', 'Stok tidak mencukupi! Stok tersedia: ' . $item->stok);
            return;
        }

        $this->items_list[] = [
            'item_id' => $item->item_id,
            'nama_item' => $item->nama_item,
            'tipe_item' => $item->tipe_item,
            'qty' => $this->qty_temp,
            'harga_satuan' => $this->harga_temp,
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

                // Update stock if it's goods (decrease stock)
                $product = Item::find($item['item_id']);
                if ($product->tipe_item === 'barang') {
                    $product->decrement('stok', $item['qty']);
                }
            }
        });

        session()->flash('success', 'Transaksi berhasil disimpan.');
        return $this->redirect(route('transaksis.index'), navigate: true);
    }

    public function updatedItemTempId($value)
    {
        if ($value) {
            $item = Item::find($value);
            $this->harga_temp = $item->harga_jual;
        }
    }

    public function render()
    {
        return view('livewire.transaksi.create', [
            'customers' => Customer::all(),
            'items' => Item::all(), // Both goods and services can be sold
        ]);
    }
}
