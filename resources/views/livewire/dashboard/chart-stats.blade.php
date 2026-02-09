<div>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Define function globally so it's accessible but with protected logic
        window.initDashboardCharts = function() {
            if (typeof Chart === 'undefined') {
                setTimeout(window.initDashboardCharts, 100);
                return;
            }

            // Cleanup existing charts by ID to avoid "Canvas is already in use" error
            ['salesPurchaseChart', 'stockChart'].forEach(id => {
                const canvas = document.getElementById(id);
                if (canvas) {
                    const existingChart = Chart.getChart(id);
                    if (existingChart) {
                        existingChart.destroy();
                    }
                }
            });

            const salesPurchaseCtx = document.getElementById('salesPurchaseChart');
            if (salesPurchaseCtx) {
                new Chart(salesPurchaseCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($dates),
                        datasets: [{
                            label: 'Penjualan',
                            data: @json($penjualanData),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Pembelian',
                            data: @json($pembelianData),
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

            const stockCtx = document.getElementById('stockChart');
            if (stockCtx) {
                new Chart(stockCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: @json($itemNames),
                        datasets: [{
                            label: 'Stok',
                            data: @json($itemStoks),
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
        };

        // Re-run on every navigation
        document.addEventListener('livewire:navigated', window.initDashboardCharts, {
            once: true
        });

        // Final fallback init
        window.initDashboardCharts();
    </script>
    @endpush
</div>