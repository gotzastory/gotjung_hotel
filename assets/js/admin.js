// admin.js
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('aside');

    sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('hidden');
    });
});
