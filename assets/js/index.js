// navbar-dropdown-list-menu
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (dropdownButton && dropdownMenu) {
        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside of it
        document.addEventListener('click', (event) => {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }
});

// AOS initialization
document.addEventListener('DOMContentLoaded', function () {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000, // Animation duration in milliseconds
            once: true, // Animation runs only once
        });
    } else {
        console.error("AOS library is not loaded. Please check the script inclusion.");
    }
});

// ฟังก์ชันอัพเดทราคา โดยรับค่าจาก input และแสดงผลที่ element ที่มี id="price-display"
// โดยรับค่า value มาจาก input และแสดงผลที่ element ที่มี id="price-display"
// ฟังก์ชันนี้จะถูกเรียกใช้เมื่อมีการเปลี่ยนแปลงค่าใน input
// จะอยู่ในไฟล์ room.php
function updatePrice(value) {
    document.getElementById('price-display').textContent = value;
}

// กำหนดค่าเริ่มต้นเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', () => {
    const priceInput = document.getElementById('price');
    updatePrice(priceInput.value);
});