<div x-data="{ 
    pendingList: JSON.parse(localStorage.getItem('pending_trans') || '[]'),
    savePending() {
        // Use $wire to get the most recent state from Livewire
        const items = $wire.items_list;
        if (!items || items.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        
        const newTrans = {
            id: Date.now(),
            name: 'Transaksi #' + (this.pendingList.length + 1) + ' (' + new Date().toLocaleTimeString() + ')',
            items: JSON.parse(JSON.stringify(items)),
            customer_id: $wire.customer_id,
            metode: $wire.metode_pembayaran,
            total: $wire.total
        };
        
        this.pendingList.push(newTrans);
        localStorage.setItem('pending_trans', JSON.stringify(this.pendingList));
        $wire.clearCart();
    },
    restorePending(index) {
        const trans = this.pendingList[index];
        $wire.set('items_list', trans.items);
        $wire.set('customer_id', trans.customer_id);
        $wire.set('metode_pembayaran', trans.metode);
        
        this.pendingList.splice(index, 1);
        localStorage.setItem('pending_trans', JSON.stringify(this.pendingList));
    },
    removePending(index) {
        if(confirm('Hapus transaksi pending ini?')) {
            this.pendingList.splice(index, 1);
            localStorage.setItem('pending_trans', JSON.stringify(this.pendingList));
        }
    },
    printType: '58',
    printReceipt(type, fromModal = false) {
        this.printType = type;
        if (!fromModal) {
            const items = @js($items_list);
            if (this.pendingList.length === 0 && (!items || items.length === 0)) {
                alert('Keranjang masih kosong!');
                return;
            }
        }
        this.$nextTick(() => {
            window.print();
        });
    }
}" class="flex flex-col gap-6">
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
            <div class="md:col-span-4 flex flex-col gap-4">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-full flex flex-col">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">Transaksi</h3>

                    <!-- Cart Items Scrollable -->
                    <div class="flex-grow overflow-y-auto space-y-6 mb-6 pr-2" style="max-height: 500px;">
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

                    <!-- Total -->
                    <div class="flex justify-between items-center mb-8">
                        <span class="text-xl font-bold text-gray-800 dark:text-gray-200">Total</span>
                        <span class="text-2xl font-black text-gray-900 dark:text-white">Rp
                            {{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="grid grid-cols-5 gap-2">
                        <button type="button" wire:click="clearCart"
                            class="col-span-1 bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg flex items-center justify-center transition"
                            title="Clear">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                        <button type="button" @click="savePending()"
                            class="col-span-1 bg-slate-800 hover:bg-slate-900 text-white p-3 rounded-lg flex flex-col items-center justify-center transition text-[10px]"
                            title="Pending">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pending
                        </button>
                        <button type="button" @click="printReceipt('58')" class="col-span-1 bg-gray-200 hover:bg-gray-300 text-gray-700 p-3 rounded-lg flex flex-col items-center justify-center transition text-[10px]"
                            title="Print 58mm">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 012-2H7a2 2 0 012 2v3a2 2 0 002 2zm0 0v-8a2 2 0 012-2h6a2 2 0 012 2v8">
                                </path>
                            </svg>
                            58
                        </button>
                        <button type="button" @click="printReceipt('A4')" class="col-span-1 bg-gray-200 hover:bg-gray-300 text-gray-700 p-3 rounded-lg flex flex-col items-center justify-center transition text-[10px]"
                            title="Print A4">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            A4
                        </button>
                        <button type="button" wire:click="save"
                            class="col-span-1 bg-blue-500 hover:bg-blue-600 text-white font-bold p-3 rounded-lg flex items-center justify-center transition">
                            Bayar
                        </button>
                    </div>
                </div>

                <!-- Additional settings hidden in a small card -->
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
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
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="kredit">Kredit</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Transaksi Table -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Pending Transaksi
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                No.</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Items</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(trans, index) in pendingList" :key="trans.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100" x-text="trans.name"></td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="item in trans.items">
                                            <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-[10px]" x-text="item.qty + 'x ' + item.nama_item"></span>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button type="button" @click="restorePending(index)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Restore</button>
                                    <button type="button" @click="removePending(index)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="pendingList.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 italic">Tidak ada transaksi tertunda</td>
                        </tr>
                    </tbody>
                </table>
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

            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px;">
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