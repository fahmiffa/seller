<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
    <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-1/3 flex gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID atau Supplier..."
                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
        <div class="flex justify-end w-full md:w-auto">
            <a href="{{ route('pembelians.create') }}" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150 text-sm">
                Tambah Pembelian
            </a>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($pembelians as $pembelian)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $pembelian->supplier->nama_supplier ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $pembelian->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('pembelians.show', $pembelian) }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">Detail</a>
                                    <button type="button" onclick="confirmDelete('{{ route('pembelians.destroy', $pembelian) }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 text-sm">Tidak ada data pembelian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pembelians->links() }}
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