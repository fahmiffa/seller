<?php

namespace App\Livewire\Dashboard;

use App\Models\Item;
use App\Models\Pembelian;
use App\Models\Transaksi;
use Carbon\Carbon;
use Livewire\Component;

class ChartStats extends Component
{
    public $dates = [];
    public $penjualanData = [];
    public $pembelianData = [];
    public $itemNames = [];
    public $itemStoks = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Get data for last 7 days
        $this->dates         = [];
        $this->penjualanData = [];
        $this->pembelianData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::now()->subDays($i)->format('Y-m-d');
            $this->dates[] = Carbon::now()->subDays($i)->format('d M');

            // Penjualan per hari
            $this->penjualanData[] = (float) Transaksi::where('user_id', auth()->user()->getOwnerId())
                ->whereDate('tanggal_transaksi', $date)
                ->sum('total_harga');

            // Pembelian per hari
            $this->pembelianData[] = (float) Pembelian::where('user_id', auth()->user()->getOwnerId())
                ->whereDate('tanggal_pembelian', $date)
                ->sum('total_pembelian');
        }

        // Stok barang (top 10 items)
        $stokItems = Item::where('tipe_item', 'barang')
            ->where('user_id', auth()->user()->getOwnerId())
            ->orderBy('stok', 'desc')
            ->limit(10)
            ->get();

        $this->itemNames = $stokItems->pluck('nama_item')->toArray();
        $this->itemStoks = $stokItems->pluck('stok')->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.chart-stats');
    }
}
