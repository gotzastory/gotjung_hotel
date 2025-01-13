// ดึงปุ่มและ Sidebar
const toggleButton = document.getElementById('toggleButton');
const sidebar = document.getElementById('sidebar');
const mainContent = document.querySelector('.main-content');

// ฟังก์ชันเปิด/ปิด Sidebar
toggleButton.addEventListener('click', () => {
  if (sidebar.style.left === '0px') {
    sidebar.style.left = '-250px'; // ซ่อน Sidebar
    mainContent.style.marginLeft = '0'; // ย้าย Main Content กลับ
  } else {
    sidebar.style.left = '0px'; // แสดง Sidebar
    mainContent.style.marginLeft = '250px'; // ดัน Main Content
  }
});

// ฟังก์ชัน Delete Contact ไฟล์ dashboard.php
function deleteContact(id) {
    if (confirm("Are you sure you want to delete this contact?")) {
        fetch("", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `delete_id=${id}`
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("Contact deleted successfully");
                location.reload();
            } else {
                alert("Failed to delete contact");
            }
        })
        .catch(error => console.error("Error:", error));
    }
}

// Function to toggle modal visibility
window.toggleModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.toggle('hidden');
    } else {
        console.error(`Modal with ID ${modalId} not found.`);
    }
};

// Function to open the edit modal and set its data
window.openEditModal = (id, name, price, type, description) => {
    const roomIdField = document.getElementById('edit-room-id');
    const roomNameField = document.getElementById('edit-room-name');
    const roomPriceField = document.getElementById('edit-room-price');
    const roomTypeField = document.getElementById('edit-room-type');
    const roomDescriptionField = document.getElementById('edit-room-description');

    if (roomIdField && roomNameField && roomPriceField && roomTypeField && roomDescriptionField) {
        roomIdField.value = id;
        roomNameField.value = name;
        roomPriceField.value = price;
        roomTypeField.value = type;
        roomDescriptionField.value = description;

        toggleModal('editRoomModal');
    } else {
        console.error('One or more fields not found in the edit modal.');
    }
};
