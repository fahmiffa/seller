<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center no-print">
                <a href="{{ route('laporans.index') }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    ← Kembali ke Menu Laporan
                </a>
                <button onclick="window.print()" class="bg-gray-800 dark:bg-gray-200 dark:text-gray-800 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 012-2H7a2 2 0 012 2v3a2 2 0 002 2zm0 0v-8a2 2 0 012-2h6a2 2 0 012 2v8"></path>
                    </svg>
                    Print Laporan
                </button>
            </div>

            <style>
                @media print {

                    .no-print,
                    nav,
                    header {
                        display: none !important;
                    }

                    body {
                        background-color: white !important;
                        color: black !important;
                    }

                    .bg-white,
                    .bg-gray-800 {
                        background-color: transparent !important;
                        box-shadow: none !important;
                        border: none !important;
                    }

                    .text-gray-900,
                    .dark\:text-gray-100,
                    .text-gray-600,
                    .dark\:text-gray-400 {
                        color: black !important;
                    }

                    .max-w-7xl {
                        max-width: 100% !important;
                        padding: 0 !important;
                        margin: 0 !important;
                    }

                    .py-12 {
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                    }

                    table {
                        width: 100% !important;
                        border-collapse: collapse !important;
                    }

                    th,
                    td {
                        border: 1px solid #ddd !important;
                        padding: 8px !important;
                    }
                }
            </style>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Periode Laporan</h3>
                    <p class="text-sm border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                        {{ \Carbon\Carbon::parse($request->tanggal_dari)->format('d F Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_sampai)->format('d F Y') }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Total Subtotal</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-gray-200">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/10 p-4 rounded-xl border border-red-100 dark:border-red-900/30">
                            <p class="text-xs text-red-500 uppercase font-bold tracking-wider mb-1">Total Diskon</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($totalDiskon, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/10 p-4 rounded-xl border border-green-100 dark:border-green-900/30">
                            <p class="text-xs text-green-500 uppercase font-bold tracking-wider mb-1">Total Penjualan (Nett)</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kasir</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">Metode</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diskon</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transaksis as $transaksi)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->customer ? $transaksi->customer->nama : 'Umum' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->user->name }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-[10px] leading-5 font-semibold rounded-full 
                                                {{ $transaksi->metode_pembayaran == 'tunai' ? 'bg-green-100 text-green-800' : ($transaksi->metode_pembayaran == 'transfer' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($transaksi->metode_pembayaran) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm">Rp {{ number_format($transaksi->subtotal ?? $transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm text-red-500">
                                        @if($transaksi->diskon > 0)
                                        -Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <ul class="text-sm">
                                            @foreach($transaksi->details as $detail)
                                            <li>{{ $detail->item->nama_item }} ({{ $detail->qty }}x)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data penjualan pada periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-right text-sm uppercase tracking-wider">Total Keseluruhan:</td>
                                    <td class="px-4 py-4 text-right text-sm">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-red-600">-Rp {{ number_format($totalDiskon, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-green-600 dark:text-green-400">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>