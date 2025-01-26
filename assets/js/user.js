// Dropdown Menu
document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (dropdownButton && dropdownMenu) {
        // แสดง/ซ่อน Dropdown เมื่อคลิกปุ่ม
        dropdownButton.addEventListener('click', (e) => {
            e.stopPropagation(); // หยุด Event ไม่ให้ลามไปยัง document
            dropdownMenu.classList.toggle('show'); // ใช้ class "show" แทน "hidden"
        });

        // ซ่อน Dropdown เมื่อคลิกนอกเมนู
        document.addEventListener('click', (e) => {
            if (!dropdownMenu.contains(e.target) && !dropdownButton.contains(e.target)) {
                dropdownMenu.classList.remove('show'); // ซ่อนเมนู
            }
        });
    } else {
        console.error("Dropdown elements not found");
    }
});
