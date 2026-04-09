<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="w-full md:w-1/3 flex gap-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari User..."
                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="flex justify-end w-full md:w-auto">
                <a href="{{ route('users.create') }}" wire:navigate class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150 text-sm">
                    Tambah User Baru
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6">
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kontak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role & Parent</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keuangan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->img)
                                        <img src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="h-10 w-10 object-cover rounded-full ring-2 ring-gray-100 dark:ring-gray-600 shadow-sm">
                                        @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
                                            <span class="text-white text-sm font-bold uppercase">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100 flex flex-wrap items-center gap-1">
                                            {{ $user->name }}
                                            @if($user->trial)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 border border-orange-200 dark:border-orange-800 uppercase tracking-tighter">Trial</span>
                                            @endif
                                            @if($user->tipe == 1)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800 uppercase tracking-tighter">Premium</span>
                                            @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 border border-gray-200 dark:border-gray-800 uppercase tracking-tighter">Basic</span>
                                            @endif

                                            @if($user->is_login)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 uppercase tracking-tighter">Online</span>
                                            @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800 uppercase tracking-tighter">Offline</span>
                                            @endif

                                            @if($user->tipe == 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $user->transaction_count >= 10 ? 'bg-red-500 text-white' : 'bg-blue-100 text-blue-700' }} border border-gray-200 uppercase tracking-tighter">
                                                {{ $user->transaction_count }}/10
                                            </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->phone_number ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[150px]">{{ $user->address ?? 'Alamat belum diatur' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    @if($user->role == 0)
                                    <span class="inline-flex items-center w-px-fit px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Admin
                                    </span>
                                    @elseif($user->role == 3)
                                    <span class="inline-flex items-center w-px-fit px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        Operator
                                    </span>
                                    @else
                                    <span class="inline-flex items-center w-px-fit px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        User/Mitra
                                    </span>
                                    @endif
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400">Parent: {{ $user->parent->name ?? 'None' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">Rp{{ number_format($user->saldo ?? 0, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Limit: {{ number_format($user->limit ?? 0, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($user->status ?? 'active') == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ ($user->status ?? 'active') == 'active' ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Detail
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        Edit
                                    </a>
                                    <button type="button" @click="confirmDelete('{{ route('users.destroy', $user) }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/50">
                                <p class="text-lg font-medium">Tidak ada data user.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script shadow>
        function confirmDelete(url) {
            if (confirm('Apakah Anda yakin?')) {
                const form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
</div>