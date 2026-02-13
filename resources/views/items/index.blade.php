<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Produk & Jasa Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        selectedIds: JSON.parse(localStorage.getItem('selected_qr_ids') || '[]'),
        toggleId(id) {
            id = String(id);
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
            this.save();
        },
        toggleAll(checked, pageIds) {
            pageIds.forEach(id => {
                id = String(id);
                if (checked) {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                } else {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                }
            });
            this.save();
        },
        save() {
            localStorage.setItem('selected_qr_ids', JSON.stringify(this.selectedIds));
        },
        clearAll() {
            if(confirm('Batalkan semua pilihan?')) {
                this.selectedIds = [];
                this.save();
            }
        },
        printSelected() {
            if (this.selectedIds.length === 0) {
                alert('Silakan pilih item yang ingin dicetak terlebih dahulu.');
                return;
            }
            const url = '{{ route('items.print-qrcode') }}?ids=' + this.selectedIds.join(',');
            window.open(url, '_blank');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <form action="{{ route('items.index') }}" method="GET" class="w-full md:w-1/2 flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama produk atau jasa..."
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="bg-gray-800 dark:bg-gray-200 dark:text-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 dark:hover:bg-gray-300 transition">
                        Cari
                    </button>
                    @if($search)
                    <a href="{{ route('items.index') }}" class="flex items-center text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        Reset
                    </a>
                    @endif
                </form>
                <div class="flex flex-wrap gap-2 w-full md:w-auto items-center">
                    <!-- Counter Badge with Clear -->
                    <div x-show="selectedIds.length > 0"
                        class="flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900/50 px-3 py-2 rounded-lg border border-indigo-200 dark:border-indigo-800 transition-all duration-300">
                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300">
                            <span x-text="selectedIds.length"></span> Produk terpilih
                        </span>
                        <button type="button" @click="clearAll()" class="p-1 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-full text-indigo-400 hover:text-red-500 transition-colors" title="Batalkan pilihan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="printSelected()" x-show="selectedIds.length > 0"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md shadow-indigo-500/20 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 012-2H7a2 2 0 012 2v3a2 2 0 002 2zm0 0v-8a2 2 0 012-2h6a2 2 0 012 2v8"></path>
                            </svg>
                            <span>Print Selected</span>
                        </button>

                        <a href="{{ route('items.print-qrcode', ['search' => $search]) }}" target="_blank"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <span>Print Semua</span>
                        </a>

                        <a href="{{ route('items.create') }}" wire:navigate
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md shadow-blue-500/20 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Tambah Baru</span>
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            :checked="selectedIds.length > 0 && [{{ $items->pluck('item_id')->implode(',') }}].every(id => selectedIds.includes(String(id)))"
                                            @change="toggleAll($el.checked, [{{ $items->pluck('item_id')->implode(',') }}])">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gambar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">QR Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Jual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $item->item_id }}"
                                            class="item-checkbox rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            :checked="selectedIds.includes('{{ $item->item_id }}')"
                                            @change="toggleId('{{ $item->item_id }}')">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->nama_item }}" class="h-10 w-10 rounded-full object-cover">
                                        @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 text-xs">
                                            No Image
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="bg-white p-1 inline-block rounded shadow-sm">
                                            {!! QrCode::size(50)->generate($item->item_id . '-' . $item->user_id) !!}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium">{{ $item->nama_item }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->satuan->nama_satuan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->tipe_item == 'barang' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($item->tipe_item) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->tipe_item == 'barang')
                                        {{ $item->stok }}
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('items.edit', $item) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data produk/jasa.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>