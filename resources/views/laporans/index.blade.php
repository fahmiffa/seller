<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Laporan Penjualan -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Laporan Penjualan</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Lihat laporan transaksi penjualan berdasarkan periode tertentu</p>
                        <form action="{{ route('laporans.penjualan') }}" method="GET">
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" value="{{ date('Y-m-01') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                Lihat Laporan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Laporan Pembelian -->
                @if(auth()->user()->role != 3)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Laporan Pembelian</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Lihat laporan pembelian barang berdasarkan periode tertentu</p>
                        <form action="{{ route('laporans.pembelian') }}" method="GET">
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" value="{{ date('Y-m-01') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                Lihat Laporan
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Laporan Stok -->
                @if(auth()->user()->role != 3)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Laporan Stok</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Lihat laporan stok barang saat ini</p>
                        <form action="{{ route('laporans.stok') }}" method="GET">
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" value="{{ date('Y-m-01') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                Lihat Laporan
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Laporan Laba Rugi -->
                @if(auth()->user()->role != 3)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold">Laporan Laba Rugi</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Lihat laporan laba rugi berdasarkan periode tertentu</p>
                        <form action="{{ route('laporans.laba-rugi') }}" method="GET">
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" value="{{ date('Y-m-01') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium mb-1">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <button type="submit" class="w-full bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                Lihat Laporan
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>