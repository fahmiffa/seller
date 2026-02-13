export default function transaksiPOS() {
    return {
        pendingList: JSON.parse(localStorage.getItem("pending_trans") || "[]"),
        printType: "58",
        showPendingModal: false,
        showScannerModal: false,
        html5QrCode: null,

        async startScanner() {
            this.showScannerModal = true;
            this.$nextTick(() => {
                this.html5QrCode = new Html5Qrcode("reader");
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    this.$wire.scanResult(decodedText);
                    this.stopScanner();
                };
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    qrCodeSuccessCallback,
                );
            });
        },

        async stopScanner() {
            if (this.html5QrCode) {
                try {
                    await this.html5QrCode.stop();
                } catch (err) {
                    console.error("Failed to stop scanner", err);
                }
                this.html5QrCode = null;
            }
            this.showScannerModal = false;
        },

        async savePending() {
            // Wait for Livewire to sync and get the most recent state
            const items = await this.$wire.get("items_list");
            const customerId = await this.$wire.get("customer_id");
            const metode = await this.$wire.get("metode_pembayaran");
            const diskon = await this.$wire.get("diskon");
            const total = await this.$wire.get("total");

            if (!items || items.length === 0) {
                alert("Keranjang masih kosong!");
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

        removePending(id) {
            if (confirm("Hapus transaksi pending ini?")) {
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
                    alert("Keranjang masih kosong!");
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
        },
    };
}
