// เปิด Modal การชำระเงิน
function openPaymentModal(bookingId) {
    const modal = document.getElementById("paymentModal");
    const bookingInput = document.getElementById("bookingId");
    bookingInput.value = bookingId; // ใส่ค่า bookingId ลงใน input hidden
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    console.log("Payment modal opened for booking ID:", bookingId);
}

// ปิด Modal การชำระเงิน
function closePaymentModal() {
    const modal = document.getElementById("paymentModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    console.log("Payment modal closed.");
}

// ปิด Modal เมื่อคลิกพื้นที่นอก Modal
window.onclick = function (event) {
    const modal = document.getElementById("paymentModal");
    if (event.target === modal) {
        closePaymentModal();
    }
};
