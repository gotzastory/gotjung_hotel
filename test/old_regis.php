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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบค่าที่ได้รับจากฟอร์ม
    if (isset($_POST['fullname'], $_POST['email'], $_POST['phone'], $_POST['password'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = $_POST['password']; // เก็บ Plain Text

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare("INSERT INTO customers (fullname, email, phone, password) VALUES (?, ?, ?, ?)");

        // ตรวจสอบว่าคำสั่ง SQL สำเร็จหรือไม่
        if (!$stmt) {
            die("Error in preparing statement: " . $conn->error);
        }

        // ผูกค่าตัวแปรกับคำสั่ง SQL
        $stmt->bind_param("ssss", $fullname, $email, $phone, $password);

        // ดำเนินการคำสั่ง SQL
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please login.'); window.location.href='User_Login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not register.');</script>";
        }

        // ปิด statement
        $stmt->close();
    } else {
        $error = "Please fill in all the fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Register</h2>
        <?php if (isset($error)): ?>
            <div class="mb-4 bg-red-100 text-red-700 text-sm p-2 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-4">
                <label for="fullname" class="block text-sm font-medium text-gray-700">ชื่อเต็ม</label>
                <input type="text" id="fullname" name="fullname" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">อีเมล</label>
                <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">เบอร์โทร</label>
                <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg">สมัครสมาชิก</button>
        </form>
        <div class="text-center mt-4">
            <a href="User_Login.php" class="text-blue-500 hover:underline">หากมีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
        </div>
    </div>

</body>
</html>
