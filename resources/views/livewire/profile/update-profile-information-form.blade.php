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
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="img" :value="__('Profile Picture')" />
            <div class="mt-2 flex items-center gap-4">
                @if ($img)
                <img src="{{ $img->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-full">
                @elseif ($current_img)
                <img src="{{ asset('storage/' . $current_img) }}" class="h-20 w-20 object-cover rounded-full">
                @else
                <div class="h-20 w-20 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <span class="text-gray-500 text-2xl font-bold">{{ substr($name, 0, 1) }}</span>
                </div>
                @endif

                <input type="file" wire:model="img" id="img" class="text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 shadow-sm" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('img')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    {{ __('Your email address is unverified.') }}

                    <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ __('A new verification link has been sent to your email address.') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input wire:model="phone_number" id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <textarea wire:model="address" id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>