<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Penjualan Bulan Ini -->
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

                <!-- Total Pembelian Bulan Ini -->
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

                <!-- Total Transaksi -->
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

                <!-- Stok Menipis -->
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

                <!-- Saldo Saat Ini -->
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

            <!-- Quick Access Cards -->
            @if(auth()->user()->role == 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- User Management -->
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

                <!-- Saldo Management -->
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
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Kelola riwayat & top up saldo</p>
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

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Grafik Penjualan & Pembelian -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Penjualan & Pembelian (7 Hari Terakhir)</h3>
                        <canvas id="salesPurchaseChart"></canvas>
                    </div>
                </div>

                <!-- Grafik Stok -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top 10 Stok Barang</h3>
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            let salesPurchaseChart = null;
            let stockChart = null;

            function initCharts() {
                // Check if Chart is defined, if not wait a bit
                if (typeof Chart === 'undefined') {
                    setTimeout(initCharts, 100);
                    return;
                }

                // Destroy existing charts if they exist
                if (salesPurchaseChart) {
                    salesPurchaseChart.destroy();
                    salesPurchaseChart = null;
                }
                if (stockChart) {
                    stockChart.destroy();
                    stockChart = null;
                }

                // Penjualan & Pembelian Chart
                const salesPurchaseCtx = document.getElementById('salesPurchaseChart');
                if (salesPurchaseCtx) {
                    salesPurchaseChart = new Chart(salesPurchaseCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode($dates); ?>,
                            datasets: [{
                                label: 'Penjualan',
                                data: <?php echo json_encode($penjualanData); ?>,
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Pembelian',
                                data: <?php echo json_encode($pembelianData); ?>,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Stok Chart
                const stockCtx = document.getElementById('stockChart');
                if (stockCtx) {
                    stockChart = new Chart(stockCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($itemNames); ?>,
                            datasets: [{
                                label: 'Stok',
                                data: <?php echo json_encode($itemStoks); ?>,
                                backgroundColor: 'rgba(234, 179, 8, 0.8)',
                                borderColor: 'rgb(234, 179, 8)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            // Initialize charts on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCharts);
            } else {
                initCharts();
            }

            // Re-initialize charts after Livewire navigation
            document.addEventListener('livewire:navigated', initCharts);
        })();
    </script>
    @endpush
</x-app-layout>