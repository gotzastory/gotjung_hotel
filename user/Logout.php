<?php
// เริ่มต้น Session
session_start();

// ล้างข้อมูลใน Session
session_unset();

// ทำลาย Session
session_destroy();

// เปลี่ยนเส้นทางไปยังหน้า Login
header("Location: ../index.php");
exit;
?>