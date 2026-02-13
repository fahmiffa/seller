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
    public $showSuccessModal = false;
    public $last_transaction_id;
    public $last_items = [];
    public $last_total = 0;
    public $last_customer_name = 'Umum';
    public $diskon = 0;
    public $last_diskon = 0;
    public $last_subtotal = 0;

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
                $this->dispatch('alert', message: 'Stok habis!');
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

    public function scanResult($decodedText)
    {
        $parts = explode('-', $decodedText);
        if (count($parts) < 1) {
            $this->dispatch('alert', message: 'Format QR Code tidak sesuai!');
            return;
        }

        $itemId = $parts[0];
        $item = Item::where('item_id', $itemId)->first();

        // Optional: check user_id if present in QR code
        if (isset($parts[1]) && $item && $item->user_id != $parts[1]) {
            $item = null;
        }

        if (!$item) {
            $this->dispatch('alert', message: 'Produk tidak ditemukan!');
            return;
        }

        $this->addToCart($item->item_id);
    }

    public function incrementQty($index)
    {
        $item = Item::find($this->items_list[$index]['item_id']);
        if ($item->tipe_item === 'barang' && $item->stok <= $this->items_list[$index]['qty']) {
            $this->dispatch('alert', message: 'Stok tidak mencukupi!');
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

    public function restoreCart($items, $customerId, $metode)
    {
        $this->items_list = $items;
        $this->customer_id = $customerId;
        $this->metode_pembayaran = $metode;
    }

    public function getSubtotalProperty()
    {
        return collect($this->items_list)->sum('subtotal');
    }

    public function getTotalProperty()
    {
        $subtotal = $this->subtotal;
        $diskon = max(0, (float) $this->diskon);
        return max(0, $subtotal - $diskon);
    }

    public function save()
    {
        if (Auth::user()->saldo <= Auth::user()->limit) {
            $this->dispatch('alert', message: 'Saldo limit, tidak bisa melakukan transaksi!');
            return;
        }

        $this->validate([
            'tanggal_transaksi' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer,kredit',
            'items_list' => 'required|array|min:1',
        ]);

        $transaksiId = null;
        DB::transaction(function () use (&$transaksiId) {
            $transaksi = Transaksi::create([
                'customer_id' => $this->customer_id,
                'user_id' => Auth::id(),
                'tanggal_transaksi' => $this->tanggal_transaksi,
                'subtotal' => $this->subtotal,
                'diskon' => $this->diskon,
                'total_harga' => $this->total,
                'metode_pembayaran' => $this->metode_pembayaran,
            ]);

            $transaksiId = $transaksi->transaksi_id;

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

        // Store data for printing before potentially clearing or moving away
        $this->last_items = $this->items_list;
        $this->last_subtotal = $this->subtotal;
        $this->last_diskon = $this->diskon;
        $this->last_total = $this->total;
        $this->last_transaction_id = $transaksiId;

        if ($this->customer_id) {
            $customer = Customer::find($this->customer_id);
            $this->last_customer_name = $customer ? $customer->nama : 'Umum';
        } else {
            $this->last_customer_name = 'Umum';
        }

        $this->clearCart();
        $this->diskon = 0;
        $this->showSuccessModal = true;

        session()->flash('success', 'Transaksi berhasil disimpan.');
    }

    public function resetTransaction()
    {
        $this->showSuccessModal = false;
        $this->customer_id = null;
        $this->last_items = [];
        $this->last_total = 0;
        $this->last_subtotal = 0;
        $this->last_diskon = 0;
        $this->last_transaction_id = null;
    }

    public function updatedItemsList($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) === 2 && $parts[1] === 'qty') {
            $index = $parts[0];
            $qty = $this->items_list[$index]['qty'];

            if (!is_numeric($qty) || $qty < 1) {
                $this->removeItem($index);
                return;
            }

            $item = Item::find($this->items_list[$index]['item_id']);
            if ($item && $item->tipe_item === 'barang' && $item->stok < $qty) {
                $this->items_list[$index]['qty'] = $item->stok;
                $this->dispatch('alert', message: 'Stok tidak mencukupi!');
            }

            $this->items_list[$index]['subtotal'] = $this->items_list[$index]['qty'] * $this->items_list[$index]['harga_satuan'];
        }
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
