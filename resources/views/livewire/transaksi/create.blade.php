<div x-data="transaksiPOS" class="flex flex-col gap-6">
    <style>
        @media print {
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .print-area {
                display: block !important;
            }

            .receipt-58 {
                width: 58mm;
                margin: 0 auto;
                padding: 10px;
                font-family: 'monospace';
                font-size: 10px;
                line-height: 1.2;
                color: black;
            }

            .receipt-a4 {
                width: 210mm;
                margin: 0 auto;
                padding: 20mm;
                background: white;
                font-family: sans-serif;
            }
        }

        .print-area {
            display: none;
        }
    </style>

    <div class="no-print">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Left Side: Product Selection -->
            <div class="md:col-span-8 space-y-4">
                <!-- Search Bar -->
                <div class="relative">
                    <input type="text" wire:model.live="search" placeholder="Cari Items"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-lg shadow-sm py-3 px-4">
                </div>

                <div class="flex justify-center">
                    @if(!$showAll)
                    <button wire:click="$set('showAll', true)" class="text-blue-600 font-semibold hover:underline">
                        Lihat Semua Produk
                    </button>
                    @else
                    <button wire:click="$set('showAll', false)" class="text-blue-600 font-semibold hover:underline">
                        Sembunyikan Sebagian
                    </button>
                    @endif
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($items as $item)
                    <div wire:click="addToCart({{ $item->item_id }})"
                        class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition cursor-pointer flex flex-col items-center text-center relative overflow-hidden">
                        <div
                            class="absolute top-2 right-2 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded text-xs font-bold text-gray-600 dark:text-gray-300">
                            {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </div>

                        @if($item->tipe_item === 'barang')
                        <div
                            class="absolute top-2 left-2 {{ $item->stok > 0 ? ($item->stok < 10 ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600') : 'bg-red-50 text-red-600' }} px-2 py-1 rounded text-[10px] font-bold">
                            Stok: {{ $item->stok }}
                        </div>
                        @endif

                        <div
                            class="w-24 h-24 mb-4 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
                            @if ($item->image && file_exists(public_path('storage/' . $item->image)))
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->nama_item }}"
                                class="max-w-full max-h-full object-contain">
                            @else
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            @endif
                        </div>

                        <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-2">
                            {{ $item->nama_item }}
                        </h4>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Side: Transaction/Cart -->
            <div class="md:col-span-4 flex flex-col gap-4 sticky top-4 h-[calc(100vh-2rem)]">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex-1 min-h-0 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">Transaksi</h3>
                        <div class="flex gap-2">
                            <button type="button" @click="showPendingModal = true"
                                class="col-span-1 bg-slate-800 hover:bg-slate-900 text-white p-2 rounded-lg flex items-center justify-center transition relative"
                                title="Pending List">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-icon lucide-list">
                                    <path d="M3 5h.01" />
                                    <path d="M3 12h.01" />
                                    <path d="M3 19h.01" />
                                    <path d="M8 5h13" />
                                    <path d="M8 12h13" />
                                    <path d="M8 19h13" />
                                </svg>
                                <span x-show="pendingList.length > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center" x-text="pendingList.length"></span>
                            </button>
                            <button type="button" wire:click="clearCart"
                                class="col-span-1 bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg flex items-center justify-center transition"
                                title="Clear">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Cart Items Scrollable -->
                    <div class="flex-grow space-y-6 mb-6 pr-2">
                        @forelse($items_list as $index => $item)
                        <div wire:key="cart-item-{{ $item['item_id'] }}-{{ $index }}" class="flex items-start justify-between gap-4">
                            <div class="flex-grow">
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $item['nama_item'] }}
                                </h5>
                                <div class="text-xs text-gray-500 mb-2">
                                    {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                                </div>

                                <div class="flex items-center gap-2">
                                    <button wire:click="decrementQty({{ $index }})"
                                        class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-200 focus:outline-none">-</button>
                                    <input type="number" wire:model.blur="items_list.{{ $index }}.qty"
                                        class="w-12 h-8 text-center border-gray-200 dark:border-gray-600 dark:bg-gray-700 rounded p-0 text-sm">
                                    <button wire:click="incrementQty({{ $index }})"
                                        class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center hover:bg-gray-200 focus:outline-none">+</button>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-4">
                                <span class="text-sm font-bold">Rp
                                    {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                <button wire:click="removeItem({{ $index }})"
                                    class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 text-gray-400">
                            <p>Belum ada produk dipilih</p>
                        </div>
                        @endforelse
                    </div>

                    <hr class="border-gray-100 dark:border-gray-700 mb-4">

                    <!-- Subtotal, Diskon, Total -->
                    <div class="space-y-3 mb-6">
                        <!-- Subtotal -->
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Rp
                                {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>

                        <!-- Diskon Input -->
                        <div class="flex justify-between items-center gap-4">
                            <label class="text-sm text-gray-600 dark:text-gray-400">Diskon</label>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500">Rp</span>
                                <input type="number" wire:model.live="diskon" min="0"
                                    class="w-28 text-right border-gray-200 dark:border-gray-600 dark:bg-gray-700 rounded-md py-1.5 px-3 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0">
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-600">
                            <span class="text-lg font-bold text-gray-800 dark:text-gray-200">Total</span>
                            <span class="text-xl font-black text-gray-900 dark:text-white">Rp
                                {{ number_format($this->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Additional settings hidden in a small card -->
                    <div
                        class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Customer</label>
                                <select wire:model="customer_id"
                                    class="w-full text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900 rounded-md py-1">
                                    <option value="">Umum</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->customer_id }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metode</label>
                                <select wire:model="metode_pembayaran"
                                    class="w-full text-sm border-gray-200 dark:border-gray-700 dark:bg-gray-900 rounded-md py-1">
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">Qris</option>
                                    <option value="kredit">Kredit</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="grid grid-cols-5 gap-2">
                        <button type="button" @click="savePending()"
                            class="col-span-1 bg-slate-800 hover:bg-slate-900 text-white p-3 rounded-lg flex flex-col items-center justify-center transition text-[10px]"
                            title="Pending">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pending
                        </button>
                        <button type="button" @click="showPendingModal = true"
                            class="col-span-1 bg-slate-800 hover:bg-slate-900 text-white p-3 rounded-lg flex flex-col items-center justify-center transition text-[10px]"
                            title="List Pending">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            List
                        </button>
                        <button type="button" wire:click="save"
                            class="col-span-3 bg-blue-500 hover:bg-blue-600 text-white font-bold p-3 rounded-xl flex items-center justify-center transition shadow-lg shadow-blue-500/30">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Pending Modal -->
    <div x-show="showPendingModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 no-print"
        @keydown.escape.window="showPendingModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] flex flex-col"
            @click.away="showPendingModal = false">
            <!-- Modal Header -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 rounded-t-2xl flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Pending Transaksi
                    <span class="text-sm font-normal text-gray-500">(<span x-text="pendingList.length"></span>)</span>
                </h3>
                <button @click="showPendingModal = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto flex-1 p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">No.</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Items</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(trans, index) in pendingList" :key="trans.id">
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100" x-text="trans.name"></td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="item in trans.items">
                                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-[10px]" x-text="item.qty + 'x ' + item.nama_item"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                        <button type="button" @click="restorePending(trans.id); showPendingModal = false" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2">Restore</button>
                                        <button type="button" @click="removePending(trans.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="pendingList.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 italic">Tidak ada transaksi tertunda</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 dark:border-gray-600 rounded-b-2xl flex justify-end gap-2">
                <button x-show="pendingList.length > 0" @click="if(confirm('Hapus semua transaksi pending?')) { pendingList = []; localStorage.removeItem('pending_trans'); }"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition">
                    Hapus Semua
                </button>
                <button @click="showPendingModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 text-sm rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccessModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 no-print">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-8 text-center animate-in fade-in zoom-in duration-300">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Transaksi Berhasil!</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">Data transaksi telah disimpan dengan ID #{{ $last_transaction_id }}</p>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <button @click="printReceipt('58', true)" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-xl transition border border-gray-100 dark:border-gray-600">
                    <svg class="w-8 h-8 mb-2 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 012-2H7a2 2 0 012 2v3a2 2 0 002 2zm0 0v-8a2 2 0 012-2h6a2 2 0 012 2v8"></path>
                    </svg>
                    <span class="text-xs font-bold uppercase">Cetak 58mm</span>
                </button>
                <button @click="printReceipt('A4', true)" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-xl transition border border-gray-100 dark:border-gray-600">
                    <svg class="w-8 h-8 mb-2 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-xs font-bold uppercase">Cetak A4</span>
                </button>
            </div>

            <div class="flex flex-col gap-3">
                <button wire:click="resetTransaction" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg shadow-blue-500/30">
                    Transaksi Baru
                </button>
                <a href="{{ route('transaksis.index') }}" wire:navigate class="w-full py-3 bg-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium transition">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Print Area -->
    <div class="print-area">
        @php
        $printItems = $showSuccessModal ? $last_items : $items_list;
        $printSubtotal = $showSuccessModal ? $last_subtotal : $this->subtotal;
        $printDiskon = $showSuccessModal ? $last_diskon : $diskon;
        $printTotal = $showSuccessModal ? $last_total : $this->total;
        $printCustomer = $showSuccessModal ? $last_customer_name : 'Umum';
        $printNo = $showSuccessModal ? 'INV-'.$last_transaction_id : 'PROFORMA';
        @endphp
        <!-- 58mm Receipt -->
        <div x-show="printType === '58'" class="receipt-58">
            <div style="text-align: center; margin-bottom: 10px;">
                <h2 style="margin: 0; font-size: 14px;">{{ auth()->user()->name }}</h2>
                <p style="margin: 0;">{{ date('d/m/Y H:i') }}</p>
                <p style="margin: 0;">No: {{ $printNo }}</p>
                <p style="margin: 0;">Customer: {{ $printCustomer }}</p>
            </div>

            <div style="border-bottom: 1px dashed #000; margin-bottom: 5px;"></div>

            @foreach($printItems as $item)
            <div style="margin-bottom: 5px;">
                <div>{{ $item['nama_item'] }}</div>
                <div style="display: flex; justify-content: space-between;">
                    <span>{{ $item['qty'] }} x {{ number_format($item['harga_satuan'], 0, ',', '.') }}</span>
                    <span>{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach

            <div style="border-bottom: 1px dashed #000; margin-bottom: 5px; margin-top: 5px;"></div>

            <div style="display: flex; justify-content: space-between; font-size: 10px;">
                <span>Subtotal</span>
                <span>Rp {{ number_format($printSubtotal, 0, ',', '.') }}</span>
            </div>
            @if($printDiskon > 0)
            <div style="display: flex; justify-content: space-between; font-size: 10px; color: #666;">
                <span>Diskon</span>
                <span>- Rp {{ number_format($printDiskon, 0, ',', '.') }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; margin-top: 5px;">
                <span>TOTAL</span>
                <span>Rp {{ number_format($printTotal, 0, ',', '.') }}</span>
            </div>

            <div style="text-align: center; margin-top: 15px;">
                <p style="margin: 0;">Terima Kasih</p>
                <p style="margin: 0;">Selamat Belanja Kembali</p>
            </div>
        </div>

        <!-- A4 Receipt -->
        <div x-show="printType === 'A4'" class="receipt-a4">
            <div style="display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                <div>
                    <h1 style="margin: 0; font-size: 24px;">{{ auth()->user()->name }}</h1>
                    <p style="margin: 0; color: #666;">Invoice: {{ $printNo }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0;"><b>Tanggal:</b> {{ date('d F Y') }}</p>
                    <p style="margin: 0;"><b>Customer:</b> {{ $printCustomer }}</p>
                    <p style="margin: 0;"><b>Status:</b> Lunas</p>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background: #f4f4f4; text-align: left;">
                        <th style="padding: 10px; border: 1px solid #ddd;">Item</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Qty</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Harga</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($printItems as $item)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item['nama_item'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $item['qty'] }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right;">Subtotal</td>
                        <td style="padding: 10px; text-align: right;">Rp {{ number_format($printSubtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($printDiskon > 0)
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right; color: #666;">Diskon</td>
                        <td style="padding: 10px; text-align: right; color: #666;">- Rp {{ number_format($printDiskon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold; font-size: 18px;">TOTAL</td>
                        <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 18px; background: #eee;">Rp {{ number_format($printTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 50px;">
                <p style="margin: 0; border-top: 1px solid #ddd; display: inline-block; width: 200px; text-align: center; padding-top: 10px;">Kasir</p>
            </div>
        </div>
    </div>
</div>