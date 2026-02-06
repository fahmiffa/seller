import "./bootstrap";
import transaksiPOS from "./transaksi-pos";

// Register Alpine.js components
document.addEventListener("alpine:init", () => {
    Alpine.data("transaksiPOS", transaksiPOS);
});
