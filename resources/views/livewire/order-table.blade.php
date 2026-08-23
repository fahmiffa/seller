<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-1/2 flex flex-col sm:flex-row gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Order (Supplier/Unit/Komoditas/Ket)..."
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <select wire:model.live="statusFilter" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="dibatalkan">Dibatalkan</option>
            </select>
        </div>
        <div class="flex justify-end w-full md:w-auto">
            <a href="{{ route('orders.create') }}" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Order
            </a>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Unit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Supplier</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Item Komoditas (Qty & Harga)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Tanggal PO</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer whitespace-nowrap" wire:click="sortBy('created_at')">
                                <div class="flex items-center gap-1">
                                    Tanggal Dibuat
                                    @if($sortField === 'created_at')
                                        <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($orders as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $orders->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $item->unit ? $item->unit->name : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @if($item->supplier)
                                        {{ $item->supplier->nama_supplier }}
                                    @else
                                        <span class="text-xs italic text-gray-400">Belum diverifikasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <div class="space-y-1">
                                    @foreach($item->items as $orderItem)
                                        <div class="flex flex-col border-b border-gray-100 dark:border-gray-700 last:border-0 pb-1.5 last:pb-0 mb-1.5 last:mb-0">
                                            <div class="flex items-center justify-between gap-4 text-xs whitespace-nowrap">
                                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $orderItem->komoditas ? $orderItem->komoditas->name : '-' }}</span>
                                                <span class="text-gray-600 dark:text-gray-300 font-semibold">{{ number_format($orderItem->jumlah, 0, ',', '.') }} {{ $orderItem->satuan ? $orderItem->satuan->nama_satuan : '' }}</span>
                                            </div>
                                            @if(auth()->user()->role == 4)
                                            <div class="text-[10px] mt-0.5 flex items-center justify-between gap-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                <span><span class="text-gray-400">Unit:</span> Rp {{ number_format($orderItem->harga_unit, 0, ',', '.') }}</span>
                                                <span><span class="text-gray-400">Supp:</span> Rp {{ number_format($orderItem->harga_supplier, 0, ',', '.') }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($item->status == 'pending')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300">Pending</span>
                                    @elseif($item->status == 'diproses')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">Diproses</span>
                                    @elseif($item->status == 'selesai')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">Selesai</span>
                                    @elseif($item->status == 'dibatalkan')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">Dibatalkan</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->tanggal_po ? $item->tanggal_po->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-2">
                                        @if(auth()->user()->role == 4 && $item->po_document)
                                            <a href="{{ route('orders.stream', $item) }}" target="_blank" class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 hover:bg-green-100 rounded border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 transition" title="Unduh PO">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                PO
                                            </a>
                                        @endif
                                        @if(auth()->user()->role == 4 && $item->status == 'pending')
                                            <button type="button" wire:click="openVerifyModal({{ $item->id }})" class="inline-flex items-center px-2 py-1 bg-yellow-50 text-yellow-700 hover:bg-yellow-100 rounded border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800 transition">
                                                Verifikasi
                                            </button>
                                            <button type="button" onclick="confirmBatal('{{ route('orders.update', $item) }}')" class="inline-flex items-center px-2 py-1 bg-gray-50 text-gray-700 hover:bg-gray-100 rounded border border-gray-200 dark:bg-gray-900/30 dark:text-gray-400 dark:border-gray-800 transition">
                                                Batal
                                            </button>
                                        @endif
                                        @if(auth()->user()->role == 5 && $item->status == 'diproses')
                                            <button type="button" onclick="confirmSelesai('{{ route('orders.update', $item) }}')" class="inline-flex items-center px-2 py-1 bg-green-50 text-green-700 hover:bg-green-100 rounded border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 transition">
                                                Verifikasi
                                            </button>
                                        @endif
                                        @if($item->status == 'pending')
                                            <a href="{{ route('orders.edit', $item) }}" wire:navigate class="inline-flex items-center px-2 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800 transition">
                                                Edit
                                            </a>
                                            <button type="button" onclick="confirmDelete('{{ route('orders.destroy', $item) }}')" class="inline-flex items-center px-2 py-1 bg-red-50 text-red-700 hover:bg-red-100 rounded border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800 transition">
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role == 4 ? 10 : 9 }}" class="px-6 py-8 text-center text-gray-500 text-sm">Tidak ada data order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <!-- Action Form -->
    <form id="action-form" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="mark_selesai" id="input-mark-selesai" disabled>
        <input type="hidden" name="mark_dibatalkan" id="input-mark-dibatalkan" disabled>
    </form>
    
    <!-- Delete Form -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Order ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = url;
                    form.submit();
                }
            });
        }

        function confirmBatal(url) {
            Swal.fire({
                title: 'Batalkan Order?',
                text: "Status order akan diubah menjadi dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('action-form');
                    form.action = url;
                    document.getElementById('input-mark-dibatalkan').disabled = false;
                    form.submit();
                }
            });
        }

        function confirmSelesai(url) {
            Swal.fire({
                title: 'Verifikasi Penerimaan?',
                text: "Status order akan diubah menjadi selesai.",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('action-form');
                    form.action = url;
                    document.getElementById('input-mark-selesai').disabled = false;
                    form.submit();
                }
            });
        }

        function confirmVerifikasiModal(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Simpan Verifikasi?',
                text: "Pastikan data supplier dan harga sudah benar. Status akan menjadi diproses.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <!-- Modal Verifikasi -->
    @if($verifyModalOpen && $verifyingOrder)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeVerifyModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form action="{{ route('orders.update', $verifyingOrder['id']) }}" method="POST" onsubmit="confirmVerifikasiModal(event, this)">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="status" value="diproses">
                    <input type="hidden" name="unit_id" value="{{ $unit_id }}">
                    <input type="hidden" name="keterangan" value="{{ $keterangan }}">

                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                            Verifikasi Order #{{ $verifyingOrder['id'] }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier <span class="text-red-500">*</span></label>
                                <select name="supplier_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier['supplier_id'] }}">{{ $supplier['nama_supplier'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal PO</label>
                                <input type="date" name="tanggal_po" value="{{ $tanggal_po }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Daftar Komoditas</h4>
                            @foreach($items as $index => $item)
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-md border border-gray-200 dark:border-gray-600">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item['komoditas_name'] }}</span>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $item['jumlah'] }} {{ $item['satuan_name'] }}</span>
                                    </div>
                                    <input type="hidden" name="items[{{ $index }}][komoditas_id]" value="{{ $item['komoditas_id'] }}">
                                    <input type="hidden" name="items[{{ $index }}][jumlah]" value="{{ $item['jumlah'] }}">
                                    <input type="hidden" name="items[{{ $index }}][satuan_id]" value="{{ $item['satuan_id'] }}">
                                    <input type="hidden" name="items[{{ $index }}][keterangan]" value="{{ $item['keterangan'] }}">
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Harga Unit <span class="text-red-500">*</span></label>
                                            <input type="number" step="any" min="0" name="items[{{ $index }}][harga_unit]" value="{{ $item['harga_unit'] }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Rp" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Harga Supplier <span class="text-red-500">*</span></label>
                                            <input type="number" step="any" min="0" name="items[{{ $index }}][harga_supplier]" value="{{ $item['harga_supplier'] }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Rp" required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200 dark:border-gray-600">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Verifikasi
                        </button>
                        <button type="button" wire:click="closeVerifyModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
