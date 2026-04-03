<div>
    <div class="mb-6 md:flex md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex-1 min-w-0">
            <div class="relative max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 sm:text-sm shadow-sm" placeholder="Cari nama, email, atau telepon...">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="perPage" class="block w-24 pl-3 pr-10 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 shadow-sm text-gray-600 dark:text-gray-400">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <a href="{{ route('users.create') }}" wire:navigate class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all active:scale-95">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah User
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 transition-all duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/40">
                        <th scope="col" class="px-6 py-4 text-left">
                            <button wire:click="sortBy('name')" class="flex items-center gap-2 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider group">
                                USER
                                @if($sortField === 'name')
                                @if($sortDirection === 'asc')
                                <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                                @else
                                <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                @endif
                                @else
                                <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">KONTAK</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">ROLE & PARENT</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">KEUANGAN</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">STATUS</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-800">
                    @forelse($users as $user)
                    <tr wire:key="{{ $user->id }}" class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 relative">
                                    @if($user->img)
                                    <img src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="h-10 w-10 object-cover rounded-2xl ring-2 ring-white dark:ring-gray-700 shadow-md">
                                    @else
                                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                                        <span class="text-white text-sm font-black uppercase">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 h-3.5 w-3.5 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-black text-gray-900 dark:text-white flex items-center gap-2">
                                        {{ $user->name }}
                                        @if($user->trial)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-lg text-[10px] font-black bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 border border-orange-200 dark:border-orange-800 uppercase tracking-widest">Trial</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->phone_number ?? '-' }}</div>
                            <div class="text-[11px] font-medium text-gray-400 dark:text-gray-500 truncate max-w-[150px]">{{ $user->address ?? 'Alamat belum diatur' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex flex-col gap-1.5">
                                @if($user->role == 0)
                                <span class="w-fit px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/50">Admin</span>
                                @elseif($user->role == 3)
                                <span class="w-fit px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 border border-purple-100 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-900/50">Operator</span>
                                @else
                                <span class="w-fit px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-600 border border-gray-100 dark:bg-gray-700/30 dark:text-gray-300 dark:border-gray-700">Mitra</span>
                                @endif
                                <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Parent: {{ $user->parent->name ?? 'None' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-black text-gray-900 dark:text-white">Rp{{ number_format($user->saldo ?? 0, 0, ',', '.') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Limit: {{ number_format($user->limit ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex flex-col gap-1.5">
                                <span class="w-fit px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-widest border {{ ($user->status ?? 'active') == 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/50' : 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50' }}">
                                    {{ ($user->status ?? 'active') == 'active' ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex justify-end gap-1.5 align-middle">
                                <a href="{{ route('users.show', $user) }}" wire:navigate class="p-2 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all active:scale-90 shadow-sm border border-gray-100 dark:border-gray-700" title="Informasi">
                                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" wire:navigate class="p-2 text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-xl transition-all active:scale-90 shadow-sm border border-gray-100 dark:border-gray-700" title="Ubah Data">
                                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button type="button" onclick="confirmDelete('{{ route('users.destroy', $user) }}')" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all active:scale-90 shadow-sm border border-gray-100 dark:border-gray-700" title="Hapus Data">
                                    <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-full mb-4">
                                    <svg class="h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">DATA TIDAK DITEMUKAN</p>
                                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-1">Gunakan kata kunci pencarian yang berbeda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-6 bg-gray-50 dark:bg-gray-900/40 border-t border-gray-100 dark:border-gray-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal (Native Form Trick) -->
    <form id="delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete(url) {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                const form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
</div>