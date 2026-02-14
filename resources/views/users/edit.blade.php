<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="space-y-8">
                            <!-- Profile Picture Section -->
                            <div class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="relative group">
                                    <div class="h-24 w-24 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg">
                                        @if($user->img)
                                        <img src="{{ asset('storage/' . $user->img) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                        @else
                                        <svg class="h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        @endif
                                    </div>
                                    <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <input type="file" name="img" onchange="previewImage(event)" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Foto Profil</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih foto terbaik untuk identitas user (Max 2MB)</p>
                                    @error('img')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Basic Information Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                    @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="email" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email Address</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                    @error('email') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="phone_number" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nomor Telepon/HP</label>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" placeholder="08xxxxxxxxxx">
                                    @error('phone_number') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="status" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Status Akun</label>
                                    <select name="status" id="status" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                        <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status', $user->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="trial" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tipe Layanan (Trial)</label>
                                    <select name="trial" id="trial" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                        <option value="0" {{ old('trial', $user->trial ?? 0) == 0 ? 'selected' : '' }}>Full / Premium</option>
                                        <option value="1" {{ old('trial', $user->trial ?? 0) == 1 ? 'selected' : '' }}>Trial (Uji Coba)</option>
                                    </select>
                                    @error('trial') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="space-y-2">
                                <label for="address" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="3" class="w-full px-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                @error('address') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            </div>

                            <!-- Password Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-orange-50 dark:bg-orange-900/10 rounded-xl">
                                <div class="space-y-2">
                                    <label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Ganti Password (Kosongkan jika tidak berubah)</label>
                                    <input type="password" name="password" id="password" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm">
                                    @error('password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label for="password_confirmation" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm">
                                </div>
                            </div>

                            <!-- Role & Access Section -->
                            <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl space-y-6">
                                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200">Akses & Privilese</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label for="role" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tipe User (Role)</label>
                                        <select name="role" id="role" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                            <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>User/Mitra</option>
                                            <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>Operator</option>
                                            <option value="0" {{ old('role', $user->role) == 0 ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        @error('role') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="saldo" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Initial Saldo (Rp)</label>
                                        <input type="number" name="saldo" id="saldo" value="{{ old('saldo', $user->saldo ?? 0) }}" step="0.01" min="0" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                        @error('saldo') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label for="limit" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Credit Limit (Rp)</label>
                                        <input type="number" name="limit" id="limit" value="{{ old('limit', $user->limit ?? 0) }}" step="0.01" min="0" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm" required>
                                        @error('limit') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="parent_id" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Parent / Owner</label>
                                    <select name="parent_id" id="parent_id" class="w-full h-11 px-4 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-sm">
                                        <option value="">Mandiri (Tanpa Parent)</option>
                                        @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $user->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }} ({{ $parent->email }})</option>
                                        @endforeach
                                    </select>
                                    @error('parent_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('users.index') }}" wire:navigate class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition duration-150 mr-6">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 transform hover:scale-[1.02]">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l5 5m0 0l5-5m-5 5V4" />
                                </svg>
                                Perbarui Data User
                            </button>
                        </div>
                    </form>

                    <script>
                        function previewImage(event) {
                            const reader = new FileReader();
                            reader.onload = function() {
                                const container = event.target.closest('.group').querySelector('.h-24');
                                container.innerHTML = `<img src="${reader.result}" class="h-full w-full object-cover">`;
                            }
                            reader.readAsDataURL(event.target.files[0]);
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>