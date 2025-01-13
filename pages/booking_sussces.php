<?php
// เริ่มต้น Session และตรวจสอบการเข้าสู่ระบบ
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: Admin_login.php');
    exit();
}

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// ดึงข้อมูลสถิติ
$total_rooms = $conn->query("SELECT COUNT(*) AS total FROM rooms")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) AS total FROM booking")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$total_messages = $conn->query("SELECT COUNT(*) AS total FROM contacts")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Dropdown Menu */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            z-index: 50;
            min-width: 12rem;
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .dropdown-menu.show {
            display: block;
        }

        /* Dropdown Link */
        .dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .dropdown-menu a:hover {
            background-color: #f7fafc;
        }

        footer {
            margin-top: 53rem;
        }

    </style>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-gray-800 dark:bg-gray-900">
  <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
    <div class="relative flex h-16 items-center justify-between">
      <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
        <div class="flex shrink-0 items-center">
          <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
        </div>
        <div class="hidden sm:ml-6 sm:block">
          <div class="flex space-x-4">
            <!-- Dashboard Link -->
            <a href="../pages/test.php" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Dashboard</a>
            <!-- Manage Booking Link -->
            <a href="../admin/manage_booking.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage Booking</a>
            <!-- Manage Service Link -->
            <a href="../admin/manage_service.php" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Manage Service</a>
          </div>
        </div>
      </div>
      <!-- Profile dropdown -->
      <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
        <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
          <span class="sr-only">View notifications</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
          </svg>
        </button>
        <!-- Profile Dropdown Menu -->
        <div class="relative ml-3">
          <div>
            <button type="button" class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
              <span class="sr-only">Open user menu</span>
              <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
            </button>
          </div>
          <div class="dropdown-menu mt-2 py-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
            <a href="../admin/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>
<!-- Add JavaScript for dropdown functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userMenuButton = document.getElementById('user-menu-button');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    userMenuButton.addEventListener('click', function() {
        dropdownMenu.classList.toggle('show');
    });

    // Close the dropdown if the user clicks outside of it
    window.addEventListener('click', function(event) {
        if (!userMenuButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });
});
</script>



<!-- Dashboard Content -->
<!-- <div class="container mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold mb-6">Welcome, Admin!</h2> -->

    <!-- Dashboard Statistics -->
    <!-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white shadow-md rounded-lg p-4 text-center">
            <h3 class="text-xl font-bold">Rooms</h3>
            <p class="text-3xl text-blue-500 font-bold"><?php echo $total_rooms; ?></p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 text-center">
            <h3 class="text-xl font-bold">Bookings</h3>
            <p class="text-3xl text-blue-500 font-bold"><?php echo $total_bookings; ?></p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 text-center">
            <h3 class="text-xl font-bold">Customers</h3>
            <p class="text-3xl text-blue-500 font-bold"><?php echo $total_customers; ?></p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4 text-center">
            <h3 class="text-xl font-bold">Messages</h3>
            <p class="text-3xl text-blue-500 font-bold"><?php echo $total_messages; ?></p>
        </div>
    </div> -->

    <!-- Actions Section -->
    <!-- <div class="mt-8">
        <h3 class="text-xl font-bold mb-4">Manage System</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="manage_services.php" class="bg-green-500 text-white text-center p-4 rounded-lg hover:bg-green-600 transition">
                Manage Services/Rooms
            </a>
            <a href="manage_bookings.php" class="bg-blue-500 text-white text-center p-4 rounded-lg hover:bg-blue-600 transition">
                Manage Bookings
            </a>
            <a href="delete_message.php" class="bg-red-500 text-white text-center p-4 rounded-lg hover:bg-red-600 transition">
                Manage Messages
            </a>
        </div>
    </div>
</div> -->

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <!-- Your content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white shadow-md rounded-lg p-4 text-center">
                <h3 class="text-xl font-bold">Rooms</h3>
                <p class="text-3xl text-blue-500 font-bold"><?php echo $total_rooms; ?></p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 text-center">
                <h3 class="text-xl font-bold">Bookings</h3>
                <p class="text-3xl text-blue-500 font-bold"><?php echo $total_bookings; ?></p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 text-center">
                <h3 class="text-xl font-bold">Customers</h3>
                <p class="text-3xl text-blue-500 font-bold"><?php echo $total_customers; ?></p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 text-center">
                <h3 class="text-xl font-bold">Messages</h3>
                <p class="text-3xl text-blue-500 font-bold"><?php echo $total_messages; ?></p>
            </div>
        </div>
        

    </div>
</main>

<!-- Footer -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-4">
    <div class="text-center">
        &copy; 2024 Admin Dashboard. All Rights Reserved.
    </div>
</footer>

</body>
</html>
