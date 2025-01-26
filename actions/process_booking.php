<?php
// สมมติว่าค่าที่ส่งมาจาก Modal หรือ POST
$room_id = $_POST['room_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$nights = $_POST['nights'];
$total_price = $_POST['total_price'];

// ตรวจสอบว่าค่าถูกต้องก่อน Redirect
if (!$room_id || !$check_in || !$check_out || !$nights || !$total_price) {
    die("ค่าการจองไม่ถูกต้อง กรุณาตรวจสอบข้อมูล");
}

// Redirect ไปยัง booking.php พร้อมค่าส่งใน URL
header("Location: ../pages/booking.php?room_id=$room_id&check_in=$check_in&check_out=$check_out&nights=$nights&total_price=$total_price");
exit;
