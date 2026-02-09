<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

use function Livewire\Volt\state;
use function Livewire\Volt\uses;

uses([WithFileUploads::class]);

state([
    'name' => fn() => auth()->user()->name,
    'email' => fn() => auth()->user()->email,
    'phone_number' => fn() => auth()->user()->phone_number,
    'address' => fn() => auth()->user()->address,
    'img' => null,
    'current_img' => fn() => auth()->user()->img,
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        'phone_number' => ['nullable', 'string', 'max:20'],
        'address' => ['nullable', 'string'],
        'img' => ['nullable', 'image', 'max:2048'],
    ]);

    if ($this->img) {
        // Delete old image
        if ($user->img) {
            Storage::disk('public')->delete($user->img);
        }
        $path = $this->img->store('users', 'public');
        $user->img = $path;
    }

    $user->fill([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone_number' => $validated['phone_number'],
        'address' => $validated['address'],
    ]);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    $this->current_img = $user->img;
    $this->img = null;

    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>

<section>
    <header>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Perbarui data diri, foto profil, dan alamat email Anda untuk menjaga keamanan akun.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-8 space-y-8">
        <!-- Profile Picture Section -->
        <div class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-600">
            <div class="relative group">
                <div class="h-24 w-24 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center overflow-hidden ring-4 ring-white dark:ring-gray-800 shadow-xl">
                    @if ($img)
                    <img src="{{ $img->temporaryUrl() }}" class="h-full w-full object-cover">
                    @elseif ($current_img)
                    <img src="{{ asset('storage/' . $current_img) }}" class="h-full w-full object-cover">
                    @else
                    <div class="h-full w-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                        <span class="text-white text-3xl font-bold uppercase">{{ substr($name, 0, 1) }}</span>
                    </div>
                    @endif
                </div>
                <label for="img" class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer backdrop-blur-[2px]">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </label>
                <input type="file" wire:model="img" id="img" class="hidden" />
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 italic">Foto Profil Sekarang</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Klik pada lingkaran untuk mengganti foto</p>
                <x-input-error class="mt-2" :messages="$errors->get('img')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <x-input-label for="name" :value="__('Nama Lengkap')" class="font-semibold text-gray-700 dark:text-gray-300" />
                <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full h-11 shadow-sm focus:ring-blue-500 focus:border-blue-500" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" :value="__('Alamat Email')" class="font-semibold text-gray-700 dark:text-gray-300" />
                <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full h-11 shadow-sm focus:ring-blue-500 focus:border-blue-500" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-amber-600 dark:text-amber-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Email belum terverifikasi.') }}
                    </p>
                    <button wire:click.prevent="sendVerification" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 font-medium">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                    @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-xs font-semibold text-green-600 dark:text-green-400">
                        {{ __('Link verifikasi baru telah dikirim.') }}
                    </p>
                    @endif
                </div>
                @endif
            </div>

            <div class="space-y-2">
                <x-input-label for="phone_number" :value="__('Nomor Telepon')" class="font-semibold text-gray-700 dark:text-gray-300" />
                <x-text-input wire:model="phone_number" id="phone_number" name="phone_number" type="text" class="mt-1 block w-full h-11 shadow-sm focus:ring-blue-500 focus:border-blue-500" autocomplete="tel" placeholder="08xxxxxxxxxx" />
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>
        </div>

        <div class="space-y-2">
            <x-input-label for="address" :value="__('Alamat Lengkap')" class="font-semibold text-gray-700 dark:text-gray-300" />
            <textarea wire:model="address" id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition-all" placeholder="Masukkan alamat lengkap Anda..."></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg shadow-blue-500/30">
                <svg wire:loading wire:target="updateProfileInformation" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Simpan Perubahan') }}</span>
                <span wire:loading wire:target="updateProfileInformation">{{ __('Menyimpan...') }}</span>
            </button>

            <x-action-message class="text-sm text-green-600 dark:text-green-400 font-medium" on="profile-updated">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Profil berhasil diperbarui.') }}
                </div>
            </x-action-message>
        </div>
    </form>
</section>