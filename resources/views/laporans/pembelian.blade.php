<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Pembelian') }}
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
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">Total: Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($pembelians as $pembelian)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pembelian->supplier->nama_supplier }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $pembelian->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <ul class="text-sm">
                                            @foreach($pembelian->details as $detail)
                                            <li>{{ $detail->item->nama_item }} ({{ $detail->qty }}x)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pembelian pada periode ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>