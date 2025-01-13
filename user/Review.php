<?php
// Path: user/Reviews.php
session_start(); 
// ถ้าไม่ได้เข้าสู่ระบบให้ redirect ไปยังหน้า User_Login.php
if (!isset($_SESSION['customer_id'])) {
    header('Location: User_Login.php');
    exit();
}
// ดึงข้อมูลลูกค้าจาก Session
$customer_name = $_SESSION['customer_name']; // ชื่อลูกค้า
$customer_id = $_SESSION['customer_id']; // รหัสลูกค้า

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// ดึงข้อมูลการจองของลูกค้า
$stmt = $conn->prepare("SELECT b.id_booking, r.name_rooms, b.booking_date, b.check_in, b.check_out  -- // เลือกข้อมูล id_booking, name_rooms, booking_date, check_in, check_out
    FROM booking b -- // จากตาราง booking และ rooms
    JOIN rooms r ON b.id_rooms = r.id_rooms  -- // รวมข้อมูลระหว่าง booking และ rooms ด้วย id_rooms
    WHERE b.id_customers = ?"); // // จัดกลุ่มข้อมูลด้วย id_customers
$stmt->bind_param("i", $customer_id); // กำหนดค่าให้กับตัวแปรที่ใช้เป็นพารามิเตอร์
$stmt->execute(); // ประมวลผลคำสั่ง SQL
$result = $stmt->get_result(); // ดึงข้อมูลเก็บไว้ในตัวแปร $result
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <!-- User CSS -->
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body class="bg-white text-gray-800">
<!-- Navbar -->
<nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="../index.php" class="text-3xl font-bold">GotJung Hotel</a>
        <div class="relative inline-block">
        <button class="dropdown-button bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Menu
        </button>
        <ul class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded hidden">
            <li class="p-2 hover:bg-gray-100 text-black"><a href="profile.php">Profile</a></li>
            <li class="p-2 hover:bg-gray-100 text-black"><a href="edit_profile.php">Edit Profile</a></li>
            <li class="p-2 hover:bg-gray-100 text-black"><a href="review.php">Review</a></li>
            <li class="p-2 hover:bg-gray-100 text-red-500"><a href="logout.php">Logout</a></li>
        </ul>
        </div>
    </div>
</nav>

<!-- Form Review--> 
<div class="container mx-auto mt-8 p-4 bg-white rounded shadow-md max-w-md mx-auto">
    <h2 class="text-2xl font-bold mb-4">Leave a Review</h2>
    <form action="submit_review.php" method="POST">
        <div class="mb-4">
            <label for="rating" class="block text-gray-700">Rating:</label>
            <select id="rating" name="rating" class="w-full p-2 border border-gray-300 rounded">
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Very Good</option>
                <option value="3">3 - Good</option>
                <option value="2">2 - Fair</option>
                <option value="1">1 - Poor</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="review" class="block text-gray-700">Review:</label>
            <textarea id="review" name="review" rows="4" class="w-full p-2 border border-gray-300 rounded"></textarea>
        </div>
        <div class="flex justify-end">
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="alert('Hello World')">Submit Review</button>
        </div>
    </form>
</div>
    
<!-- Footer -->
<footer class="bg-blue-600 text-white text-center py-4">
    <p>&copy; 2025 Hotel Booking. All rights reserved.</p>
</footer>
<script src="../assets/js/user.js"></script>

</body>
</html>
