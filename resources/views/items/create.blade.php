<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Produk / Jasa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('items.store') }}" method="POST" id="item-form" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4">
                                <label for="tipe_item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Item</label>
                                <select name="tipe_item" id="tipe_item" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required onchange="toggleFields()">
                                    <option value="barang" {{ old('tipe_item') == 'barang' ? 'selected' : '' }}>Barang</option>
                                    <option value="jasa" {{ old('tipe_item') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                </select>
                                @error('tipe_item')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="nama_item" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Item</label>
                                <input type="text" name="nama_item" id="nama_item" value="{{ old('nama_item') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @error('nama_item')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="mb-4">
                                <label for="satuan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                                <select name="satuan_id" id="satuan_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Pilih Satuan</option>
                                    @foreach($satuans as $satuan)
                                    <option value="{{ $satuan->satuan_id }}" {{ old('satuan_id') == $satuan->satuan_id ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
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
                                    <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>{{ $supplier->nama_supplier }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="harga_beli_section">
                                <label for="harga_beli" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Beli</label>
                                <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" oninput="calculateHargaJual()">
                                @error('harga_beli')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="margin_section">
                                <label for="margin_persen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Margin Keuntungan (%) <span class="text-xs text-gray-500">(Opsional)</span></label>
                                <input type="number" id="margin_persen" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Contoh: 20" oninput="calculateHargaJual()">
                            </div>

                            <div class="mb-4">
                                <label for="harga_jual" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Jual</label>
                                <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required oninput="validateHargaJual()">
                                <p id="error-harga-jual" class="text-red-500 text-xs mt-1 hidden">Harga jual harus lebih besar dari harga beli.</p>
                                @error('harga_jual')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="stok_section">
                                <label for="stok" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stok</label>
                                <input type="number" name="stok" id="stok" value="{{ old('stok') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('stok')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="expired_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expired Date <span class="text-xs text-gray-500">(Opsional)</span></label>
                                <input type="date" name="expired_at" id="expired_at" value="{{ old('expired_at') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @error('expired_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gambar Produk</label>
                                <input type="file" name="image" id="image" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" accept="image/*" onchange="previewImage(event)">
                                <div class="mt-2">
                                    <img id="image-preview" src="#" alt="Preview Gambar" class="hidden h-32 w-auto rounded shadow-sm">
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
                                Simpan
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
            const marginSection = document.getElementById('margin_section');
            const stokSection = document.getElementById('stok_section');

            if (tipe === 'jasa') {
                if (supplierSection) supplierSection.classList.add('hidden');
                if (hargaBeliSection) hargaBeliSection.classList.add('hidden');
                if (marginSection) marginSection.classList.add('hidden');
                if (stokSection) stokSection.classList.add('hidden');
            } else {
                if (supplierSection) supplierSection.classList.remove('hidden');
                if (hargaBeliSection) hargaBeliSection.classList.remove('hidden');
                if (marginSection) marginSection.classList.remove('hidden');
                if (stokSection) stokSection.classList.remove('hidden');
            }
            validateHargaJual();
        }

        function calculateHargaJual() {
            const hargaBeli = parseFloat(document.getElementById('harga_beli').value) || 0;
            const marginPersen = parseFloat(document.getElementById('margin_persen').value) || 0;
            const hargaJualInput = document.getElementById('harga_jual');

            if (marginPersen > 0 && hargaBeli > 0) {
                const untung = (hargaBeli * marginPersen) / 100;
                hargaJualInput.value = Math.round(hargaBeli + untung);
            }
            validateHargaJual();
        }

        function validateHargaJual() {
            const tipe = document.getElementById('tipe_item').value;
            const hargaBeli = parseFloat(document.getElementById('harga_beli').value) || 0;
            const hargaJual = parseFloat(document.getElementById('harga_jual').value) || 0;
            const errorElement = document.getElementById('error-harga-jual');
            const submitBtn = document.querySelector('button[type="submit"]');

            if (tipe === 'barang' && hargaBeli > 0 && hargaJual > 0 && hargaJual <= hargaBeli) {
                errorElement.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                errorElement.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
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