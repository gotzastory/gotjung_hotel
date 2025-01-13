// Dropdown Menu
const dropdownButton = document.getElementById('dropdownButton');
const dropdownMenu = document.getElementById('dropdownMenu');

// ตรวจสอบว่า dropdownButton มีอยู่ใน DOM ก่อนเพิ่ม Event Listener
dropdownButton?.addEventListener('click', (e) => {
    e.stopPropagation(); // หยุด Event ไม่ให้ Bubble ไปยัง document
    dropdownMenu.classList.toggle('hidden'); // แสดง/ซ่อน Dropdown
});

document.addEventListener('click', () => {
    dropdownMenu?.classList.add('hidden'); // ซ่อน Dropdown เมื่อคลิกนอก Dropdown
});

// Modal Functions
function openModal(title, description, price, image) {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const modalPrice = document.getElementById('modalPrice');
    const modalImage = document.getElementById('modalImage');

    // ตั้งค่าข้อมูลใน Modal
    modalTitle.textContent = title;
    modalDescription.textContent = description;
    modalPrice.textContent = price;
    modalImage.src = image;

    // แสดง Modal ด้วย Transition
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.add('opacity-100');
        document.getElementById('modalContent').classList.add('scale-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modalContent');

    // ปิด Modal ด้วย Transition
    modal.classList.remove('opacity-100');
    modalContent.classList.remove('scale-100');

    // ซ่อน Modal หลังจาก Transition จบ
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

//  เลื่อนสไลด์
document.addEventListener('DOMContentLoaded', () => {
    const scrollLeft = document.getElementById('scrollLeft');
    const scrollRight = document.getElementById('scrollRight');
    const roomsContainer = document.getElementById('roomsContainer');

    if (scrollLeft && scrollRight && roomsContainer) {
        // เมื่อกดปุ่มเลื่อนซ้าย
        scrollLeft.addEventListener('click', () => {
            roomsContainer.scrollBy({
                left: -300, // เลื่อนซ้าย 300px
                behavior: 'smooth', // การเลื่อนแบบนุ่มนวล
            });
        });

        // เมื่อกดปุ่มเลื่อนขวา
        scrollRight.addEventListener('click', () => {
            roomsContainer.scrollBy({
                left: 300, // เลื่อนขวา 300px
                behavior: 'smooth', // การเลื่อนแบบนุ่มนวล
            });
        });
    } else {
        console.error('Element not found: scrollLeft, scrollRight, or roomsContainer');
    }
});

