<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Produk / Jasa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('items.update', $item) }}" method="POST" id="item-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label for="tipe_item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Item</label>
                                <select name="tipe_item" id="tipe_item" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required onchange="toggleFields()">
                                    <option value="barang" {{ old('tipe_item', $item->tipe_item) == 'barang' ? 'selected' : '' }}>Barang</option>
                                    <option value="jasa" {{ old('tipe_item', $item->tipe_item) == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                </select>
                                @error('tipe_item')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="nama_item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Item</label>
                                <input type="text" name="nama_item" id="nama_item" value="{{ old('nama_item', $item->nama_item) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @error('nama_item')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="mb-4">
                                <label for="satuan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                                <select name="satuan_id" id="satuan_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Pilih Satuan</option>
                                    @foreach($satuans as $satuan)
                                    <option value="{{ $satuan->satuan_id }}" {{ old('satuan_id', $item->satuan_id) == $satuan->satuan_id ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
                                    @endforeach
                                </select>
                                @error('satuan_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="supplier_section">
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="harga_beli_section">
                                <label for="harga_beli" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Beli</label>
                                <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', $item->harga_beli) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('harga_beli')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="harga_jual" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Jual</label>
                                <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', $item->harga_jual) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @error('harga_jual')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="stok_section">
                                <label for="stok" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok</label>
                                <input type="number" name="stok" id="stok" value="{{ old('stok', $item->stok) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('stok')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gambar Produk</label>
                                <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" accept="image/*" onchange="previewImage(event)">
                                <div class="mt-2">
                                    @if($item->image)
                                    <img id="image-preview" src="{{ asset('storage/' . $item->image) }}" alt="Preview Gambar" class="h-32 w-auto rounded shadow-sm">
                                    @else
                                    <img id="image-preview" src="#" alt="Preview Gambar" class="hidden h-32 w-auto rounded shadow-sm">
                                    @endif
                                </div>
                                @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('items.index') }}" wire:navigate class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition ease-in-out duration-150">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const tipe = document.getElementById('tipe_item').value;
            const supplierSection = document.getElementById('supplier_section');
            const hargaBeliSection = document.getElementById('harga_beli_section');
            const stokSection = document.getElementById('stok_section');

            if (tipe === 'jasa') {
                if (supplierSection) supplierSection.classList.add('hidden');
                if (hargaBeliSection) hargaBeliSection.classList.add('hidden');
                if (stokSection) stokSection.classList.add('hidden');
            } else {
                if (supplierSection) supplierSection.classList.remove('hidden');
                if (hargaBeliSection) hargaBeliSection.classList.remove('hidden');
                if (stokSection) stokSection.classList.remove('hidden');
            }
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('image-preview');
                preview.src = reader.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            toggleFields();
        });

        // For Livewire navigate support
        document.addEventListener('livewire:navigated', () => {
            toggleFields();
        });
    </script>
</x-app-layout>