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
    <link href="../assets/css/admin.css" rel="stylesheet">
    <script defer src="../assets/js/admin.js"></script>
</head>
<body>
    <div class="slidebar">
        <ul>
            <li><a href="">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a></li>
            <li><a href="">
                
                <span>Manage Service</span>
            </a></li>
            <li><a href=""></a></li>
            <li><a href=""></a></li>
            <li><a href=""></a></li>
        </ul>
    </div>
</body>
</html>
