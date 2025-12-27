<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Laba Rugi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('laporans.index') }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                    ← Kembali ke Menu Laporan
                </a>
            </div>

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
