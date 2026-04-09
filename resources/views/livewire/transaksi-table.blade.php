<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-auto flex items-center gap-4">
            <div class="flex items-center">
                <label for="metode_pembayaran" class="mr-2 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">Filter Metode:</label>
                <select wire:model.live="metode_pembayaran" id="metode_pembayaran" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                    <option value="">Semua</option>
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="qris">QRIS</option>
                    <option value="kredit">Kredit</option>
                </select>
            </div>
            <div class="md:w-64">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID atau Customer..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <a href="{{ route('transaksis.create') }}" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150 text-sm w-full md:w-auto text-center">
            Tambah Transaksi
        </a>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diskon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transaksis as $transaksi)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->customer ? $transaksi->customer->nama : 'Umum' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $transaksi->metode_pembayaran == 'cash' ? 'bg-green-100 text-green-800' : ($transaksi->metode_pembayaran == 'transfer' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($transaksi->metode_pembayaran) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($transaksi->subtotal ?? $transaksi->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-red-600 dark:text-red-400">
                                @if($transaksi->diskon > 0)
                                - Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('transaksis.show', $transaksi) }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3 text-xs">Detail</a>
                                <button type="button" onclick="confirmDelete('{{ route('transaksis.destroy', $transaksi) }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-xs">Hapus</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 text-sm">Tidak ada data transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $transaksis->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(url) {
            if (confirm('Apakah Anda yakin?')) {
                const form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
</div>