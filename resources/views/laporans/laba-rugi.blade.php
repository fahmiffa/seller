<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Laba Rugi') }}
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
                    <p class="text-sm">{{ \Carbon\Carbon::parse($request->tanggal_dari)->format('d F Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_sampai)->format('d F Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Pendapatan -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Total Penjualan</h3>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($penjualan, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Pengeluaran -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Total Pembelian</h3>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($pembelian, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-6">Rincian Laba Rugi</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium">Pendapatan (Penjualan)</span>
                            <span class="text-lg font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($penjualan, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700">
                            <span class="font-medium">Harga Pokok Penjualan (HPP)</span>
                            <span class="text-lg font-semibold text-red-600 dark:text-red-400">(Rp {{ number_format($hpp, 0, ',', '.') }})</span>
                        </div>

                        <div class="flex justify-between items-center pb-4 border-b-2 border-gray-300 dark:border-gray-600">
                            <span class="font-bold text-lg">Laba Kotor</span>
                            <span class="text-xl font-bold {{ $laba_kotor >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                Rp {{ number_format($laba_kotor, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pt-4">
                            <span class="font-bold text-xl">Laba Bersih</span>
                            <span class="text-2xl font-bold {{ $laba_bersih >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                Rp {{ number_format($laba_bersih, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($penjualan > 0)
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Margin Laba:
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format(($laba_bersih / $penjualan) * 100, 2) }}%
                                </span>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>