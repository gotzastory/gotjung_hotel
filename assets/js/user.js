// Dropdown Menu
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.querySelector('.dropdown-button'); // ปุ่มเปิด Dropdown
    const dropdownMenu = document.querySelector('.dropdown-menu'); // เมนู Dropdown

    if (dropdownButton && dropdownMenu) {
        // แสดง/ซ่อน Dropdown เมื่อกดปุ่ม
        dropdownButton.addEventListener('click', (e) => {
            e.stopPropagation(); // หยุด Event ไม่ให้ลามไปยัง document
            dropdownMenu.classList.toggle('hidden'); // แสดงหรือซ่อนเมนู
        });

        // ซ่อน Dropdown เมื่อคลิกนอก Dropdown
        document.addEventListener('click', (e) => {
            if (!dropdownMenu.contains(e.target) && !dropdownButton.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    } else {
        console.error("Dropdown elements not found");
    }
});
