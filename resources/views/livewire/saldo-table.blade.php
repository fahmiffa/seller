<div>
    <div class="mb-6 md:flex md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex-1 min-w-0">
            <div class="relative max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 sm:text-sm shadow-sm" placeholder="Cari nama atau email...">
            </div>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="perPage" class="block w-24 pl-3 pr-10 py-2.5 text-sm border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 shadow-sm text-gray-600 dark:text-gray-400">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <a href="{{ route('saldos.history') }}" wire:navigate class="inline-flex items-center px-4 py-2.5 border border-indigo-200 dark:border-indigo-900 rounded-xl text-xs font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 transition-all active:scale-95 uppercase tracking-widest">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat Topup
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
                                PENGGUNA
                                @if($sortField === 'name')
                                @if($sortDirection === 'asc')
                                <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                </svg>
                                @else
                                <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                                @endif
                                @else
                                <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">SALDO SAAT INI</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-800">
                    @forelse($users as $user)
                    <tr wire:key="{{ $user->id }}" class="hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-md">
                                        <span class="text-white text-sm font-black uppercase">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $user->name }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-black text-indigo-600 dark:text-indigo-400">Rp{{ number_format($user->saldo ?? 0, 0, ',', '.') }}</div>
                            <div class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Limit: Rp{{ number_format($user->limit ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex justify-end gap-1.5 align-middle">
                                <button type="button" onclick="showTopupModal({{ $user->id }}, '{{ $user->name }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-md text-xs font-black text-white bg-indigo-600 hover:bg-indigo-700 transition-all active:scale-95 uppercase tracking-widest">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    TOPUP
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-full mb-4">
                                    <svg class="h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <p class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">DATA TIDAK DITEMUKAN</p>
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

    <!-- Topup Modal -->
    <div id="topup-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" aria-hidden="true" onclick="hideTopupModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-middle bg-white dark:bg-gray-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
                <form id="topup-form" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter uppercase" id="modal-title">Top Up Saldo</h3>
                            <button type="button" onclick="hideTopupModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Pengguna</label>
                                <p id="target-user-name" class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">-</p>
                            </div>

                            <div>
                                <label for="amount" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Jumlah Top Up (Rp)</label>
                                <input type="number" name="amount" id="amount" required min="0" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-0 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 transition duration-150 sm:text-sm font-black text-xl" placeholder="0">
                            </div>

                            <div>
                                <label for="description" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Keterangan (Opsional)</label>
                                <textarea name="description" id="description" rows="3" class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-900/50 border-0 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 transition duration-150 sm:text-sm font-medium" placeholder="Tambah catatan..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/40 p-8 flex justify-end gap-3">
                        <button type="button" onclick="hideTopupModal()" class="px-6 py-2.5 text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:text-gray-700 transition-colors">Batal</button>
                        <button type="submit" class="inline-flex items-center px-8 py-2.5 border border-transparent rounded-2xl shadow-lg text-xs font-black text-white bg-indigo-600 hover:bg-indigo-700 transition-all active:scale-95 uppercase tracking-widest">
                            KONFIRMASI TOPUP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTopupModal(userId, userName) {
            const modal = document.getElementById('topup-modal');
            const form = document.getElementById('topup-form');
            const nameEl = document.getElementById('target-user-name');

            nameEl.innerText = userName;
            form.action = `/saldos/topup/${userId}`;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function hideTopupModal() {
            const modal = document.getElementById('topup-modal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
</div>