<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-1/2 flex flex-col sm:flex-row gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Invoice (No. INV / Unit / Supplier / Komoditas)..."
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">Semua Status</option>
                <option value="unpaid">Belum Lunas</option>
                <option value="paid">Lunas</option>
            </select>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-sm" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer whitespace-nowrap" wire:click="sortBy('inv_number')">
                                <div class="flex items-center gap-1">
                                    No. Invoice
                                    @if($sortField === 'inv_number') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">No. PO Referensi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Unit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Supplier</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Ringkasan Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Subtotal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Total (+ PPN)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer whitespace-nowrap" wire:click="sortBy('inv_date')">
                                <div class="flex items-center gap-1">
                                    Tgl Invoice
                                    @if($sortField === 'inv_date') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Status Bayar</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($invoices as $index => $invoice)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $invoices->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $invoice->inv_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $invoice->order->po_number ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $invoice->order->unit ? $invoice->order->unit->name : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $invoice->order->supplier ? $invoice->order->supplier->nama_supplier : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <div class="space-y-1">
                                        @foreach($invoice->order->items ?? [] as $item)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center justify-between gap-4 whitespace-nowrap">
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item->komoditas ? $item->komoditas->name : '-' }}</span>
                                                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan ? $item->satuan->nama_satuan : '' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($invoice->total, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $invoice->inv_date ? $invoice->inv_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($invoice->payment_status === 'paid')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Lunas</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($invoice->inv_document)
                                            <a href="{{ route('invoices.stream', $invoice) }}" target="_blank"
                                                class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800 transition" title="Lihat Invoice">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                INV
                                            </a>
                                        @endif
                                        @if($invoice->payment_status === 'unpaid')
                                            <button type="button" onclick="confirmPaid('{{ route('invoices.mark-paid', $invoice) }}')"
                                                class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 hover:bg-green-100 rounded border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 transition">
                                                Verifikasi Lunas
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-8 text-center text-gray-500 text-sm">Tidak ada data invoice.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>

    <!-- Paid Form -->
    <form id="paid-form" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

    <script>
        function confirmPaid(url) {
            Swal.fire({
                title: 'Verifikasi Pembayaran?',
                text: "Invoice ini akan ditandai sebagai LUNAS.",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Lunas!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('paid-form');
                    form.action = url;
                    form.submit();
                }
            });
        }
    </script>
</div>
