<?php
// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบค่าที่ได้รับจากฟอร์ม
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($fullname) && !empty($email) && !empty($phone) && !empty($password)) {
        // ตรวจสอบว่าอีเมลมีอยู่ในระบบหรือยัง
        $check_stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "อีเมลนี้มีการใช้งานแล้ว";
        } else {
            // เตรียมคำสั่ง SQL
            $stmt = $conn->prepare("INSERT INTO customers (fullname, email, phone, password) VALUES (?, ?, ?, ?)");

            if (!$stmt) {
                die("Error in preparing statement: " . $conn->error);
            }

            // ผูกค่าตัวแปรกับคำสั่ง SQL
            $stmt->bind_param("ssss", $fullname, $email, $phone, $password);

            // ดำเนินการคำสั่ง SQL
            if ($stmt->execute()) {
                echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); window.location.href='User_Login.php';</script>";
            } else {
                $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
            }

            $stmt->close();
        }

        $check_stmt->close();
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-100 flex items-center justify-center min-h-screen">
    <!-- Card -->
    <div class="flex flex-col md:flex-row bg-white shadow-lg rounded-lg overflow-hidden max-w-4xl w-full">
        <!-- Image Section -->
        <div class="w-full md:w-1/2">
            <img src="../assets/images/index/beautiful-interior-view-of-a-room-at-coastal-free-photo.jpg" alt="Register Image" class="w-full h-full object-cover">
        </div>

        <!-- Form Section -->
        <div class="w-full md:w-1/2 p-8">
            <!-- Tabs -->
            <div class="flex justify-center space-x-2 mb-6">
                <a href="Register.php" class="bg-orange-500 text-white font-bold px-4 py-2 rounded-lg hover:bg-orange-600">สมัครสมาชิก</a>
                <a href="User_Login.php" class="bg-orange-100 text-orange-500 font-bold px-4 py-2 rounded-lg hover:text-orange-600 hover:bg-orange-200">เข้าสู่ระบบ</a>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-bold text-orange-600 mb-6">สมัครสมาชิก</h2>


            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-4 text-red-700 bg-red-100 rounded-lg">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="fullname" class="block text-sm font-medium text-gray-700">ชื่อเต็ม</label>
                    <input type="text" id="fullname" name="fullname" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">อีเมล</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">เบอร์โทร</label>
                    <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600">สมัครสมาชิก</button>
            </form>

        </div>
    </div>
</body>
</html>
