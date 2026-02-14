<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail User: ') }} <span class="text-blue-600">{{ $user->name }}</span>
            </h2>
            <a href="{{ route('users.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-lg font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- User Profile Card -->
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="relative h-32 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                <div class="px-6 pb-6">
                    <div class="relative flex justify-between items-end -mt-16 mb-4">
                        <div class="flex items-end">
                            <div class="h-32 w-32 rounded-2xl border-4 border-white dark:border-gray-800 bg-white dark:bg-gray-700 overflow-hidden shadow-lg">
                                @if($user->img)
                                <img src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                @else
                                <div class="h-full w-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-4xl font-bold uppercase">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="ml-6 mb-2">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($user->status ?? 'active') == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ($user->status ?? 'active') == 'active' ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 opacity-75">|</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($user->role == 0) Admin @elseif($user->role == 3) Operator @else User/Mitra @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('users.edit', $user) }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition duration-150">
                                Edit Profil
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <!-- Contact Info -->
                        <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Informasi Kontak</h4>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Email</p>
                                    <p class="text-sm dark:text-gray-200">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Telepon</p>
                                    <p class="text-sm dark:text-gray-200">{{ $user->phone_number ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Alamat</p>
                                    <p class="text-sm dark:text-gray-200">{{ $user->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Financial/Role Info -->
                        <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Keuangan & Struktur</h4>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-indigo-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Saldo Saat Ini</p>
                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">Rp{{ number_format($user->saldo ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Limit Saldo</p>
                                    <p class="text-sm dark:text-gray-200 uppercase">Rp{{ number_format($user->limit ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-semibold">Parent User</p>
                                    <p class="text-sm dark:text-gray-200">{{ $user->parent->name ?? 'None (Root)' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Summary -->
                        <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                            <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Ringkasan Aktivitas</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-600 shadow-sm">
                                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase">Produk</p>
                                    <p class="text-xl font-bold dark:text-gray-100">{{ $user->items()->count() }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-600 shadow-sm">
                                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase">Transaksi</p>
                                    <p class="text-xl font-bold dark:text-gray-100">{{ $user->transaksis()->count() }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-600 col-span-2 shadow-sm">
                                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase">Terdaftar Sejak</p>
                                    <p class="text-sm font-bold dark:text-gray-100">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section for Data -->
            <div x-data="{ activeTab: 'items' }" class="space-y-4">
                <div class="flex border-b border-gray-200 dark:border-gray-700 gap-8">
                    <button @click="activeTab = 'items'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'items', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'items' }"
                        class="pb-4 px-2 font-bold text-sm uppercase tracking-widest border-b-2 transition-all duration-200">
                        Produk & Jasa
                    </button>
                    <button @click="activeTab = 'transactions'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'transactions', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'transactions' }"
                        class="pb-4 px-2 font-bold text-sm uppercase tracking-widest border-b-2 transition-all duration-200">
                        Riwayat Transaksi
                    </button>
                </div>

                <!-- Products Table -->
                <div x-show="activeTab === 'items'" class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden animate-fade-in border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga Jual</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" class="h-10 w-10 rounded-lg object-cover mr-3">
                                            @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $item->nama_item }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->satuan->nama_satuan ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->tipe_item == 'barang' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($item->tipe_item) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium {{ $item->stok <= 5 ? 'text-red-500' : 'text-gray-600 dark:text-gray-300' }}">
                                            {{ $item->tipe_item == 'barang' ? $item->stok : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('items.edit', $item) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 uppercase text-xs font-bold tracking-widest">Tidak ada produk ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                        {{ $items->appends(['transaksis_page' => $transaksis->currentPage()])->links() }}
                    </div>
                </div>

                <!-- Transactions Table -->
                <div x-show="activeTab === 'transactions'" class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden animate-fade-in border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID / Waktu</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($transaksis as $transaksi)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">#{{ $transaksi->transaksi_id }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d M Y, H:i') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">{{ $transaksi->customer->nama_customer ?? 'Umum' }}</td>
                                    <td class="px-6 py-4 font-bold text-blue-600 dark:text-blue-400">Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $transaksi->metode_pembayaran == 'tunai' ? 'border-green-500 text-green-500' : 'border-blue-500 text-blue-500' }}">
                                            {{ $transaksi->metode_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('transaksis.show', $transaksi) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Invoice</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 uppercase text-xs font-bold tracking-widest">Belum ada riwayat transaksi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
                        {{ $transaksis->appends(['items_page' => $items->currentPage()])->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>