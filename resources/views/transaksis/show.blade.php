<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Transaksi Penjualan') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .print-area {
                display: block !important;
            }

            .receipt-58 {
                width: 58mm;
                margin: 0 auto;
                padding: 10px;
                font-family: 'monospace';
                font-size: 10px;
                line-height: 1.2;
                color: black;
            }

            .receipt-a4 {
                width: 210mm;
                margin: 0 auto;
                padding: 20mm;
                background: white;
                font-family: sans-serif;
            }
        }

        .print-area {
            display: none;
        }
    </style>

    <div x-data="{ printType: '58' }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 no-print">
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('transaksis.index') }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    ← Kembali
                </a>
                <div class="flex gap-2">
                    <button @click="printType = '58'; $nextTick(() => window.print())"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm rounded-lg flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak 58mm
                    </button>
                    <button @click="printType = 'A4'; $nextTick(() => window.print())"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Cetak A4
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Informasi Transaksi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Transaksi</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Customer</p>
                            <p class="font-medium">{{ $transaksi->customer ? $transaksi->customer->nama : 'Umum' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Kasir</p>
                            <p class="font-medium">{{ $transaksi->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Metode Pembayaran</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $transaksi->metode_pembayaran == 'tunai' ? 'bg-green-100 text-green-800' : ($transaksi->metode_pembayaran == 'transfer' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($transaksi->metode_pembayaran) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal</p>
                            <p class="font-medium">Rp {{ number_format($transaksi->subtotal ?? $transaksi->total_harga, 0, ',', '.') }}</p>
                        </div>
                        @if($transaksi->diskon > 0)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Diskon</p>
                            <p class="font-medium text-red-600 dark:text-red-400">- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Harga</p>
                            <p class="font-medium text-lg text-blue-600 dark:text-blue-400">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Detail Item</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($transaksi->details as $detail)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium">{{ $detail->item->nama_item }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $detail->item->tipe_item == 'barang' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($detail->item->tipe_item) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $detail->qty }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right text-sm">Subtotal:</td>
                                    <td class="px-6 py-3 text-sm">Rp {{ number_format($transaksi->subtotal ?? $transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                @if($transaksi->diskon > 0)
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right text-sm text-red-600 dark:text-red-400">Diskon:</td>
                                    <td class="px-6 py-3 text-sm text-red-600 dark:text-red-400">- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td colspan="4" class="px-6 py-4 text-right font-bold text-lg">Total:</td>
                                    <td class="px-6 py-4 font-bold text-lg text-blue-600 dark:text-blue-400">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Area -->
        <div class="print-area">
            <!-- 58mm Receipt -->
            <div x-show="printType === '58'" class="receipt-58">
                <div style="text-align: center; margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 14px;">{{ $transaksi->user->name }}</h2>
                    <p style="margin: 0;">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y') }}</p>
                    <p style="margin: 0;">No: INV-{{ $transaksi->transaksi_id }}</p>
                    <p style="margin: 0;">Customer: {{ $transaksi->customer ? $transaksi->customer->nama : 'Umum' }}</p>
                </div>

                <div style="border-bottom: 1px dashed #000; margin-bottom: 5px;"></div>

                @foreach($transaksi->details as $detail)
                <div style="margin-bottom: 5px;">
                    <div>{{ $detail->item->nama_item }}</div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>{{ $detail->qty }} x {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                        <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach

                <div style="border-bottom: 1px dashed #000; margin-bottom: 5px; margin-top: 5px;"></div>

                <div style="display: flex; justify-content: space-between; font-size: 10px;">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaksi->subtotal ?? $transaksi->total_harga, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->diskon > 0)
                <div style="display: flex; justify-content: space-between; font-size: 10px; color: #666;">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; margin-top: 5px;">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                </div>

                <div style="text-align: center; margin-top: 15px;">
                    <p style="margin: 0;">Terima Kasih</p>
                    <p style="margin: 0;">Selamat Belanja Kembali</p>
                </div>
            </div>

            <!-- A4 Receipt -->
            <div x-show="printType === 'A4'" class="receipt-a4">
                <div style="display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                    <div>
                        <h1 style="margin: 0; font-size: 24px;">{{ $transaksi->user->name }}</h1>
                        <p style="margin: 0; color: #666;">Invoice: INV-{{ $transaksi->transaksi_id }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0;"><b>Tanggal:</b> {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d F Y') }}</p>
                        <p style="margin: 0;"><b>Customer:</b> {{ $transaksi->customer ? $transaksi->customer->nama : 'Umum' }}</p>
                        <p style="margin: 0;"><b>Status:</b> Lunas</p>
                    </div>
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                    <thead>
                        <tr style="background: #f4f4f4; text-align: left;">
                            <th style="padding: 10px; border: 1px solid #ddd;">Item</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Qty</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Harga</th>
                            <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi->details as $detail)
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">{{ $detail->item->nama_item }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $detail->qty }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: right;">Subtotal</td>
                            <td style="padding: 10px; text-align: right;">Rp {{ number_format($transaksi->subtotal ?? $transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaksi->diskon > 0)
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: right; color: #666;">Diskon</td>
                            <td style="padding: 10px; text-align: right; color: #666;">- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold; font-size: 18px;">TOTAL</td>
                            <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 18px; background: #eee;">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div style="margin-top: 50px;">
                    <p style="margin: 0; border-top: 1px solid #ddd; display: inline-block; width: 200px; text-align: center; padding-top: 10px;">Kasir</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>