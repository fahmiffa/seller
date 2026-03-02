import "./bootstrap";
import Swal from "sweetalert2";

window.Swal = Swal;
import transaksiPOS from "./transaksi-pos";

// Register Alpine.js components
document.addEventListener("alpine:init", () => {
    Alpine.data("transaksiPOS", transaksiPOS);
});
