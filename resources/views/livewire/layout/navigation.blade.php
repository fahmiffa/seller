<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                        {{ __('Customer') }}
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire:navigate>
                        {{ __('Supplier') }}
                    </x-nav-link>
                    <x-nav-link :href="route('satuans.index')" :active="request()->routeIs('satuans.*')" wire:navigate>
                        {{ __('Satuan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')" wire:navigate>
                        {{ __('Produk') }}
                    </x-nav-link>
                    <x-nav-link :href="route('pembelians.index')" :active="request()->routeIs('pembelians.*')" wire:navigate>
                        {{ __('Pembelian') }}
                    </x-nav-link>
                    <x-nav-link :href="route('transaksis.index')" :active="request()->routeIs('transaksis.*')" wire:navigate>
                        {{ __('Penjualan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('laporans.index')" :active="request()->routeIs('laporans.*')" wire:navigate>
                        {{ __('Laporan') }}
                    </x-nav-link>
                    @if(auth()->user()->role == 0)
                    <x-nav-link :href="route('saldos.index')" :active="request()->routeIs('saldos.*')" wire:navigate>
                        {{ __('Saldo Management') }}
                    </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 me-2">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <!-- Dark Mode Toggle Mobile -->
                <button id="theme-toggle-mobile" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 me-2">
                    <svg id="theme-toggle-dark-icon-mobile" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon-mobile" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" wire:navigate>
                {{ __('Customer') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" wire:navigate>
                {{ __('Supplier') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('satuans.index')" :active="request()->routeIs('satuans.*')" wire:navigate>
                {{ __('Satuan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')" wire:navigate>
                {{ __('Produk') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pembelians.index')" :active="request()->routeIs('pembelians.*')" wire:navigate>
                {{ __('Pembelian') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('transaksis.index')" :active="request()->routeIs('transaksis.*')" wire:navigate>
                {{ __('Penjualan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('laporans.index')" :active="request()->routeIs('laporans.*')" wire:navigate>
                {{ __('Laporan') }}
            </x-responsive-nav-link>
            @if(auth()->user()->role == 0)
            <x-responsive-nav-link :href="route('saldos.index')" :active="request()->routeIs('saldos.*')" wire:navigate>
                {{ __('Saldo Management') }}
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>

<script>
    (function() {
        let isInitialized = false;

        function updateIcons() {
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
            const themeToggleDarkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
            const themeToggleLightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');

            const isDark = localStorage.getItem('color-theme') === 'dark' ||
                (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                // Dark mode active - show sun icon (to switch to light)
                themeToggleLightIcon?.classList.remove('hidden');
                themeToggleDarkIcon?.classList.add('hidden');
                themeToggleLightIconMobile?.classList.remove('hidden');
                themeToggleDarkIconMobile?.classList.add('hidden');
            } else {
                // Light mode active - show moon icon (to switch to dark)
                themeToggleDarkIcon?.classList.remove('hidden');
                themeToggleLightIcon?.classList.add('hidden');
                themeToggleDarkIconMobile?.classList.remove('hidden');
                themeToggleLightIconMobile?.classList.add('hidden');
            }
        }

        function toggleTheme(e) {
            e.preventDefault();

            const isDark = document.documentElement.classList.contains('dark');

            if (isDark) {
                // Switch to light mode
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                // Switch to dark mode
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }

            // Update icons after toggle
            updateIcons();
        }

        function setupThemeToggle() {
            // Update icons based on current theme
            updateIcons();

            // Remove old listeners and add new ones
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');

            if (themeToggleBtn) {
                // Clone to remove all event listeners
                const newBtn = themeToggleBtn.cloneNode(true);
                themeToggleBtn.parentNode.replaceChild(newBtn, themeToggleBtn);
                newBtn.addEventListener('click', toggleTheme);
            }

            if (themeToggleBtnMobile) {
                // Clone to remove all event listeners
                const newBtnMobile = themeToggleBtnMobile.cloneNode(true);
                themeToggleBtnMobile.parentNode.replaceChild(newBtnMobile, themeToggleBtnMobile);
                newBtnMobile.addEventListener('click', toggleTheme);
            }
        }

        // Run on initial load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupThemeToggle);
        } else {
            setupThemeToggle();
        }

        // Run after Livewire navigation (only bind once)
        if (!isInitialized) {
            document.addEventListener('livewire:navigated', setupThemeToggle);
            isInitialized = true;
        }
    })();
</script>