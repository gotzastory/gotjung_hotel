let currentImageIndex = 0;

// เปิด Modal รูปภาพ
function openModal(index) {
    currentImageIndex = index;
    updateModalImage();
    highlightActiveThumbnail();
    const modal = document.getElementById("galleryModal");
    modal.classList.remove("hidden");
    modal.classList.add("opacity-100");
}

// ปิด Modal รูปภาพ
function closeModal() {
    const modal = document.getElementById("galleryModal");
    modal.classList.add("hidden");
    modal.classList.remove("opacity-100");
}

// อัปเดตรูปภาพใน Modal
function updateModalImage() {
    const modalImage = document.getElementById("modalImage");
    modalImage.src = galleryImages[currentImageIndex];
}

// ไปยังภาพก่อนหน้า
function prevImage() {
    if (currentImageIndex > 0) {
        currentImageIndex--;
        updateModalImage();
        highlightActiveThumbnail();
    }
}

// ไปยังภาพถัดไป
function nextImage() {
    if (currentImageIndex < galleryImages.length - 1) {
        currentImageIndex++;
        updateModalImage();
        highlightActiveThumbnail();
    }
}

// เน้น Thumbnail ที่ถูกเลือก
function highlightActiveThumbnail() {
    const thumbnails = document.querySelectorAll(".thumbnail");
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.classList.toggle("border-orange-500", index === currentImageIndex);
    });
}

// เปิด Modal จองห้องพัก
function openBookingModal() {
    const modal = document.getElementById("bookingModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

// ปิด Modal จองห้องพัก
function closeBookingModal() {
    const modal = document.getElementById("bookingModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

// คำนวณจำนวนคืนและราคาทั้งหมด
function calculateBooking() {
    const checkInDate = new Date(document.getElementById("checkInDate").value);
    const checkOutDate = new Date(document.getElementById("checkOutDate").value);

    if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
        const timeDifference = checkOutDate - checkInDate;
        const numberOfNights = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
        const totalPrice = numberOfNights * roomPricePerNight;

        document.getElementById("numberOfNights").textContent = numberOfNights;
        document.getElementById("totalPrice").textContent = totalPrice.toFixed(2);

        document.getElementById("nightsInput").value = numberOfNights;
        document.getElementById("totalPriceInput").value = totalPrice.toFixed(2);
    } else {
        document.getElementById("numberOfNights").textContent = 0;
        document.getElementById("totalPrice").textContent = 0;

        document.getElementById("nightsInput").value = 0;
        document.getElementById("totalPriceInput").value = 0;
    }
}

// เพิ่ม Event Listener เพื่อคำนวณราคาทันทีที่ผู้ใช้เปลี่ยนวันที่
document.getElementById("checkInDate").addEventListener("change", calculateBooking);
document.getElementById("checkOutDate").addEventListener("change", calculateBooking);

// เพิ่ม Event Listener สำหรับการปิด Modal เมื่อคลิกด้านนอก
document.addEventListener("click", function (event) {
    const modal = document.getElementById("bookingModal");
    const modalContainer = event.target.closest(".bg-white");
    if (!modalContainer && !modal.classList.contains("hidden")) {
        closeBookingModal();
    }
});
