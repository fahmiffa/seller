<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Komoditas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Komoditas</h3>
                        <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $komoditas->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Pada</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $komoditas->created_at ? $komoditas->created_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                        <a href="{{ route('komoditas.edit', $komoditas) }}" wire:navigate class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                            Edit
                        </a>
                        <a href="{{ route('komoditas.index') }}" wire:navigate class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-300 dark:hover:bg-gray-600">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
