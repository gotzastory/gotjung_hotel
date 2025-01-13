<?php
// เริ่มต้น Session และตรวจสอบการเข้าสู่ระบบ
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_Login.php');
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Container -->
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-700 text-white flex flex-col">
        <div class="p-6">
            <h1 class="text-3xl font-bold mb-4">Admin Dashboard</h1>
        </div>
        <nav class="flex-1 px-4 space-y-4">
            <a href="dashboard.php" class="flex items-center px-4 py-2 bg-blue-800 rounded hover:bg-blue-900">
                <!-- ไอคอน Dashboard -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4v4H3zM7 6h4v4H7zM11 14h4v4h-4zM15 10h4v4h-4z" />
                </svg>
                Dashboard
            </a>
            <a href="manage_services.php" class="flex items-center px-4 py-2 rounded hover:bg-blue-800">
                <!-- ไอคอน Services -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Manage Services
            </a>
            <a href="manage_bookings.php" class="flex items-center px-4 py-2 rounded hover:bg-blue-800">
                <!-- ไอคอน Bookings -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l7-7m0 14l7-7" />
                </svg>
                Manage Bookings
            </a>
            <a href="manage_message.php" class="flex items-center px-4 py-2 rounded hover:bg-blue-800">
                <!-- ไอคอน Messages -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10" />
                </svg>
                Manage Messages
            </a>
        </nav>
        <div class="p-4">
            <a href="Logout.php" class="flex items-center px-4 py-2 mt-auto bg-red-500 rounded hover:bg-red-600">
                <!-- ไอคอน Logout -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3" />
                </svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    

</div>

</body>
</html>
