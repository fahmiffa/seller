<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php $user = auth()->user(); @endphp

            {{-- ============================================================ --}}
            {{-- ROLE 4: DISTRIBUTOR --}}
            {{-- ============================================================ --}}
            @if($user->role == 4)

                {{-- Row 1: 6 Summary Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">

                    {{-- Supplier --}}
                    <a href="{{ route('suppliers.index') }}" wire:navigate
                       class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Supplier</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $supplierCount }}</dd>
                        </div>
                    </a>

                    {{-- Komoditas --}}
                    <a href="{{ route('komoditas.index') }}" wire:navigate
                       class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Komoditas</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $komoditasCount }}</dd>
                        </div>
                    </a>

                    {{-- Unit --}}
                    <a href="{{ route('units.index') }}" wire:navigate
                       class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Unit</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $unitCount }}</dd>
                        </div>
                    </a>

                    {{-- Satuan --}}
                    <a href="{{ route('satuans.index') }}" wire:navigate
                       class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Satuan</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $satuanCount }}</dd>
                        </div>
                    </a>

                    {{-- Total Order --}}
                    <a href="{{ route('orders.index') }}" wire:navigate
                       class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Order</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $orderTotal }}</dd>
                        </div>
                    </a>

                    {{-- Total Invoice --}}
                    <a href="{{ route('invoices.index') }}" wire:navigate
                       class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Invoice</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $invoiceTotal }}</dd>
                        </div>
                    </a>
                </div>

                {{-- Row 2: Breakdown Panels --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Ringkasan Order by Status --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Ringkasan Order</h3>
                                <a href="{{ route('orders.index') }}" wire:navigate
                                   class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Lihat Semua â†’
                                </a>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 flex-shrink-0"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pending</span>
                                    </div>
                                    <span class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $orderPending }}</span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Diproses</span>
                                    </div>
                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $orderDiproses }}</span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-400 flex-shrink-0"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Selesai</span>
                                    </div>
                                    <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ $orderSelesai }}</span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dibatalkan</span>
                                    </div>
                                    <span class="text-xl font-bold text-red-600 dark:text-red-400">{{ $orderDibatalkan }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Invoice --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Ringkasan Invoice</h3>
                                <a href="{{ route('invoices.index') }}" wire:navigate
                                   class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Lihat Semua â†’
                                </a>
                            </div>
                            {{-- Total nilai invoice --}}
                            <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg mb-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Nilai Invoice</dt>
                                <dd class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-0.5">
                                    Rp {{ number_format($invoiceTotalAmount, 0, ',', '.') }}
                                </dd>
                            </div>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between px-4 py-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum Dibayar</span>
                                    </div>
                                    <span class="text-xl font-bold text-red-600 dark:text-red-400">{{ $invoiceUnpaid }}</span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-400 flex-shrink-0"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lunas</span>
                                    </div>
                                    <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ $invoicePaid }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- ============================================================ --}}
            {{-- ROLE 5: UNIT --}}
            {{-- ============================================================ --}}
            @elseif($user->role == 5)

                {{-- Status Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

                    {{-- Total --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-5">
                            <div class="flex-shrink-0 bg-gray-500 rounded-md p-2.5 w-fit mb-3">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Order</dt>
                            <dd class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $orderTotal }}</dd>
                        </div>
                    </div>

                    {{-- Pending --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-400">
                        <div class="p-5">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Pending</dt>
                            <dd class="text-3xl font-bold text-yellow-500 dark:text-yellow-400">{{ $orderPending }}</dd>
                            @if($orderTotal > 0)
                                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                                    {{ round($orderPending / $orderTotal * 100) }}% dari total
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Diproses --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-400">
                        <div class="p-5">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Diproses</dt>
                            <dd class="text-3xl font-bold text-blue-500 dark:text-blue-400">{{ $orderDiproses }}</dd>
                            @if($orderTotal > 0)
                                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                                    {{ round($orderDiproses / $orderTotal * 100) }}% dari total
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Selesai --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-400">
                        <div class="p-5">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Selesai</dt>
                            <dd class="text-3xl font-bold text-green-500 dark:text-green-400">{{ $orderSelesai }}</dd>
                            @if($orderTotal > 0)
                                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                                    {{ round($orderSelesai / $orderTotal * 100) }}% dari total
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Dibatalkan --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-400">
                        <div class="p-5">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dibatalkan</dt>
                            <dd class="text-3xl font-bold text-red-500 dark:text-red-400">{{ $orderDibatalkan }}</dd>
                            @if($orderTotal > 0)
                                <span class="text-xs text-gray-400 dark:text-gray-500 mt-1 block">
                                    {{ round($orderDibatalkan / $orderTotal * 100) }}% dari total
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Progress Bar / Distribusi Status --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Distribusi Status Order</h3>
                            <a href="{{ route('orders.index') }}" wire:navigate
                               class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                Lihat Semua â†’
                            </a>
                        </div>
                        @if($orderTotal > 0)
                            <div class="flex h-5 rounded-full overflow-hidden gap-0.5 mb-3">
                                @if($orderPending > 0)
                                    <div class="bg-yellow-400" style="width: {{ ($orderPending / $orderTotal) * 100 }}%"
                                         title="Pending: {{ $orderPending }}"></div>
                                @endif
                                @if($orderDiproses > 0)
                                    <div class="bg-blue-400" style="width: {{ ($orderDiproses / $orderTotal) * 100 }}%"
                                         title="Diproses: {{ $orderDiproses }}"></div>
                                @endif
                                @if($orderSelesai > 0)
                                    <div class="bg-green-400" style="width: {{ ($orderSelesai / $orderTotal) * 100 }}%"
                                         title="Selesai: {{ $orderSelesai }}"></div>
                                @endif
                                @if($orderDibatalkan > 0)
                                    <div class="bg-red-400" style="width: {{ ($orderDibatalkan / $orderTotal) * 100 }}%"
                                         title="Dibatalkan: {{ $orderDibatalkan }}"></div>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-4 mt-2">
                                <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>Pending ({{ $orderPending }})
                                </span>
                                <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>Diproses ({{ $orderDiproses }})
                                </span>
                                <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>Selesai ({{ $orderSelesai }})
                                </span>
                                <span class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                                    <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Dibatalkan ({{ $orderDibatalkan }})
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">Belum ada data order.</p>
                        @endif
                    </div>
                </div>

            {{-- ============================================================ --}}
            {{-- ROLE 0, 1, 2, 3: DASHBOARD STANDAR --}}
            {{-- ============================================================ --}}
            @else
                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    {{-- Total Penjualan Bulan Ini --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Penjualan Bulan Ini</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Pembelian Bulan Ini --}}
                    @if($user->role != 3)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pembelian Bulan Ini</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalPembelianBulanIni, 0, ',', '.') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Total Transaksi --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Transaksi Bulan Ini</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $totalTransaksiBulanIni }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stok Menipis --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Stok Menipis</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stokMenipis }} Item</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Saldo Saat Ini --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Saldo Saat Ini</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format(auth()->user()->saldo ?? 0, 0, ',', '.') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Access Cards (role 0 only) --}}
                @if($user->role == 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- User Management --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">User Management</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola pengguna sistem</p>
                                    </div>
                                </div>
                                <a href="{{ route('users.index') }}" wire:navigate class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Kelola
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Saldo Management --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Saldo Management</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola riwayat &amp; top up saldo</p>
                                    </div>
                                </div>
                                <a href="{{ route('saldos.index') }}" wire:navigate class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Kelola
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Livewire Dashboard Charts --}}
                <livewire:dashboard.chart-stats />

            @endif

        </div>
    </div>
</x-app-layout>
