export default function transaksiPOS() {
    return {
        pendingList: JSON.parse(localStorage.getItem("pending_trans") || "[]"),
        printType: "58",
        showPendingModal: false,
        barcodeBuffer: "",
        lastKeyTime: 0,

        async savePending() {
            // Wait for Livewire to sync and get the most recent state
            const items = await this.$wire.get("items_list");
            const customerId = await this.$wire.get("customer_id");
            const metode = await this.$wire.get("metode_pembayaran");
            const diskon = await this.$wire.get("diskon");
            const total = await this.$wire.get("total");

            if (!items || items.length === 0) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Keranjang masih kosong!",
                });
                return;
            }

            const newTrans = {
                id: Date.now(),
                name:
                    "Transaksi #" +
                    (this.pendingList.length + 1) +
                    " (" +
                    new Date().toLocaleTimeString() +
                    ")",
                items: JSON.parse(JSON.stringify(items)),
                customer_id: customerId,
                metode: metode,
                diskon: diskon,
                total: total,
            };

            this.pendingList.push(newTrans);
            localStorage.setItem(
                "pending_trans",
                JSON.stringify(this.pendingList),
            );
            await this.$wire.clearCart();
            this.$wire.set("diskon", 0);
        },

        async restorePending(id) {
            const index = this.pendingList.findIndex((item) => item.id == id);
            if (index === -1) return;

            const trans = this.pendingList[index];

            // Set each property individually for proper Livewire sync
            this.$wire.set("items_list", trans.items);
            this.$wire.set("customer_id", trans.customer_id);
            this.$wire.set("metode_pembayaran", trans.metode);
            this.$wire.set("diskon", trans.diskon || 0);

            // Force component refresh
            await this.$wire.$refresh();

            this.pendingList.splice(index, 1);
            localStorage.setItem(
                "pending_trans",
                JSON.stringify(this.pendingList),
            );
        },

        async removePending(id) {
            const result = await Swal.fire({
                title: "Hapus transaksi pending ini?",
                text: "Anda tidak dapat mengembalikan data ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            });

            if (result.isConfirmed) {
                const index = this.pendingList.findIndex(
                    (item) => item.id == id,
                );
                if (index === -1) return;

                this.pendingList.splice(index, 1);
                localStorage.setItem(
                    "pending_trans",
                    JSON.stringify(this.pendingList),
                );
            }
        },

        printReceipt(type, fromModal = false) {
            this.printType = type;
            if (!fromModal) {
                const items = this.$wire.get("items_list");
                if (
                    this.pendingList.length === 0 &&
                    (!items || items.length === 0)
                ) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Keranjang masih kosong!",
                    });
                    return;
                }
            }

            // Set paper size dynamically
            const printSize = type === "58" ? "58mm auto" : "A4 portrait";
            document.documentElement.style.setProperty(
                "--print-size",
                printSize,
            );

            this.$nextTick(() => {
                window.print();
            });
        },

        init() {
            this.$wire.on("print-receipt", (event) => {
                this.printReceipt(event.type, true);
            });

            // USB Barcode Scanner Support
            window.addEventListener("keydown", (e) => {
                const currentTime = new Date().getTime();

                // If the user is typing in an input field other than the search input, ignore
                // But often scanners just type into whatever is active.
                // We capture global keys to make it "magical"

                if (currentTime - this.lastKeyTime > 50) {
                    this.barcodeBuffer = "";
                }

                if (e.key === "Enter") {
                    if (this.barcodeBuffer.length > 0) {
                        this.$wire.scanResult(this.barcodeBuffer);
                        this.barcodeBuffer = "";
                        // If user was typing in an input (like search), clear it
                        if (e.target.tagName === "INPUT") {
                            e.target.value = "";
                            // Force Livewire sync if needed, or just let it be
                            this.$wire.set("search", "");
                        }
                        e.preventDefault();
                    }
                } else if (e.key.length === 1) {
                    this.barcodeBuffer += e.key;
                }

                this.lastKeyTime = currentTime;
            });
        },
    };
}
