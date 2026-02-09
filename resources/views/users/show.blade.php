<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
            <a href="{{ route('users.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            {{ __('Detail User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                        <div class="px-6 pb-8">
                            <div class="relative flex justify-center">
                                <div class="absolute -top-16">
                                    <div class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 overflow-hidden shadow-2xl bg-white dark:bg-gray-700">
                                        @if($user->img)
                                        <img src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                        @else
                                        <div class="h-full w-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-white text-5xl font-bold uppercase">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-20 text-center">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                <div class="mt-4 flex justify-center">
                                    @if($user->role == 0)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Administrator</span>
                                    @elseif($user->role == 3)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Operator</span>
                                    @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">User/Mitra</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-8 space-y-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Status Akun</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ ($user->status ?? 'active') == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ ($user->status ?? 'active') == 'active' ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Bergabung Sejak</span>
                                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Terakhir Update</span>
                                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-3">
                                <a href="{{ route('users.edit', $user) }}" wire:navigate class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-150 shadow-lg shadow-blue-500/30">
                                    Edit Profil
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-none" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl transition-colors border border-red-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 p-8">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Informasi Kontak & Alamat
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Telepon</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 font-medium">{{ $user->phone_number ?? 'Tidak tersedia' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Lengkap</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100 font-medium leading-relaxed">{{ $user->address ?? 'Alamat belum diatur' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Financial & Access -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 p-8">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Keuangan & Akses
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-900">
                                <label class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Saldo Tersedia</label>
                                <p class="mt-2 text-2xl font-black text-blue-700 dark:text-blue-300">Rp{{ number_format($user->saldo ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-2xl border border-orange-100 dark:border-orange-900/50">
                                <label class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wider">Batas Kredit (Limit)</label>
                                <p class="mt-2 text-2xl font-black text-orange-700 dark:text-orange-300">Rp{{ number_format($user->limit ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Parent / Owner</label>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-500 uppercase">{{ substr($user->parent->name ?? 'N', 0, 1) }}</span>
                                    </div>
                                    <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->parent->name ?? 'Mandiri (Tanpa Parent)' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>