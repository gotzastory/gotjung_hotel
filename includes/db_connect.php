<?php
$servername = "localhost";
$username = "root";
$password = "";
$mydb = "hotel_bookings"; // ตรวจสอบว่าชื่อฐานข้อมูลตรงกับที่สร้างใน MySQL

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $mydb);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    // แสดงข้อผิดพลาดอย่างละเอียด
    die("Database connection failed: " . $conn->connect_error);
} else {
    // ถ้าการเชื่อมต่อสำเร็จ สามารถใช้ $conn ได้เลย
    // echo "Connected successfully"; // ถ้าต้องการแสดงว่าเชื่อมต่อสำเร็จ
}
?>
