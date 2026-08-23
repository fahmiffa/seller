<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf

                        @if(auth()->user()->role == 4)
                            <!-- Distributor (Role 4): Pilih Unit -->
                            <div class="mb-4">
                                <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Pengaju <span class="text-red-500">*</span></label>
                                <select name="unit_id" id="unit_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('unit_id') border-red-500 @enderror" required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} {{ $unit->user ? '(' . $unit->user->email . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Distributor (Role 4): Pilih Supplier -->
                            <div class="mb-4">
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier <span class="text-red-500">*</span></label>
                                <select name="supplier_id" id="supplier_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('supplier_id') border-red-500 @enderror" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" {{ old('supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }} {{ $supplier->telepon ? '(' . $supplier->telepon . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Daftar Komoditas (Multi-item) -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Daftar Komoditas <span class="text-red-500">*</span></label>
                                <button type="button" onclick="addItem()" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Komoditas
                                </button>
                            </div>

                            @if($errors->has('items'))
                                <p class="text-red-500 text-xs mb-2">{{ $errors->first('items') }}</p>
                            @endif

                            <div id="items-container" class="space-y-4">
                                <!-- Item rows will be added here by JS -->
                            </div>
                        </div>

                        @if(auth()->user()->role == 4)
                            <!-- Distributor (Role 4): Tanggal PO -->
                            <div class="mb-4">
                                <label for="tanggal_po" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal PO</label>
                                <input type="date" name="tanggal_po" id="tanggal_po" value="{{ old('tanggal_po', date('Y-m-d')) }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('tanggal_po') border-red-500 @enderror">
                                @error('tanggal_po')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Keterangan (Keduanya) -->
                        <div class="mb-6">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan / Catatan Umum</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('keterangan') border-red-500 @enderror"
                                placeholder="Catatan tambahan order...">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('orders.index') }}" wire:navigate class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition text-sm">
                                {{ auth()->user()->role == 5 ? 'Ajukan Order' : 'Simpan Order' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var isDistributor = {{ auth()->user()->role == 4 ? 'true' : 'false' }};
        var itemIndex = 0;

        var komoditasOptions = @json($komoditas);
        var satuanOptions = @json($satuans);

        function addItem(data = null) {
            const container = document.getElementById('items-container');
            const idx = itemIndex++;

            let komoditasSelect = `<select name="items[${idx}][komoditas_id]" class="item-komoditas w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                <option value="">-- Pilih Komoditas --</option>`;
            komoditasOptions.forEach(k => {
                const selected = data && data.komoditas_id == k.id ? 'selected' : '';
                komoditasSelect += `<option value="${k.id}" ${selected}>${k.name}</option>`;
            });
            komoditasSelect += `</select>`;

            let satuanSelect = `<select name="items[${idx}][satuan_id]" class="item-satuan w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">-- Satuan --</option>`;
            satuanOptions.forEach(s => {
                const selected = data && data.satuan_id == s.satuan_id ? 'selected' : '';
                satuanSelect += `<option value="${s.satuan_id}" ${selected}>${s.nama_satuan}</option>`;
            });
            satuanSelect += `</select>`;

            let hargaFields = '';
            if (isDistributor) {
                hargaFields = `
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Harga Unit</label>
                            <input type="number" step="any" min="0" name="items[${idx}][harga_unit]" value="${data?.harga_unit || ''}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Rp">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Harga Supplier</label>
                            <input type="number" step="any" min="0" name="items[${idx}][harga_supplier]" value="${data?.harga_supplier || ''}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Rp">
                        </div>
                    </div>`;
            }

            const html = `
                <div class="item-row bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-index="${idx}">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">Komoditas #<span class="item-number">${container.children.length + 1}</span></span>
                        <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700 text-sm font-medium transition ${container.children.length === 0 && !data ? 'hidden' : ''}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Komoditas <span class="text-red-500">*</span></label>
                            ${komoditasSelect}
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" step="any" min="0.01" name="items[${idx}][jumlah]" value="${data?.jumlah || ''}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Qty" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Satuan</label>
                            ${satuanSelect}
                        </div>
                    </div>
                    ${hargaFields}
                    <div class="mt-2">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Keterangan Item</label>
                        <input type="text" name="items[${idx}][keterangan]" value="${data?.keterangan || ''}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            placeholder="Spesifikasi / catatan komoditas">
                    </div>
                </div>`;

            container.insertAdjacentHTML('beforeend', html);
            renumberItems();

            // Re-init select2 on the new row
            const row = container.lastElementChild;
            $(row).find('select').select2({ width: '100%', placeholder: '-- Pilih --', allowClear: true });
        }

        function removeItem(button) {
            const row = button.closest('.item-row');
            row.remove();
            renumberItems();
        }

        function renumberItems() {
            const rows = document.querySelectorAll('.item-row');
            rows.forEach((row, i) => {
                row.querySelector('.item-number').textContent = i + 1;
                // Show/hide delete button (allow delete if more than 1)
                const deleteBtn = row.querySelector('button[onclick="removeItem(this)"]');
                if (deleteBtn) {
                    deleteBtn.classList.toggle('hidden', rows.length <= 1);
                }
            });
        }

        function initSelect2() {
            $('#unit_id, #supplier_id').select2({
                width: '100%',
                placeholder: '-- Pilih --',
                allowClear: true
            });
        }

        function initItems() {
            const container = document.getElementById('items-container');
            container.innerHTML = '';
            itemIndex = 0;
            addItem();
        }

        $(document).ready(function() {
            initSelect2();
            initItems();
        });

        document.addEventListener('livewire:navigated', function() {
            initSelect2();
            initItems();
        });
    </script>
    <style>
        /* Menyesuaikan sedikit tampilan select2 dengan tailwind */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #d1d5db !important;
            border-radius: 0.375rem !important;
            display: flex !important;
            align-items: center !important;
        }
        .dark .select2-container .select2-selection--single {
            background-color: #111827 !important;
            border-color: #374151 !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #d1d5db !important;
        }
        .dark .select2-container--default .select2-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #111827 !important;
            border-color: #374151 !important;
            color: #d1d5db !important;
        }
        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #374151 !important;
        }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
        }
    </style>
    @endpush
</x-app-layout>
