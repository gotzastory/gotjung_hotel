<?php
session_start();
include('../includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // ดึงข้อมูลจากฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ? AND password = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // สร้าง Session
            $_SESSION['customer_name'] = $user['fullname'];
            $_SESSION['id_customers'] = $user['id_customers'];
            header('Location: ../index.php');
            exit();
        } else {
            $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "กรุณากรอกอีเมลและรหัสผ่านให้ครบถ้วน";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">เข้าสู่ระบบ</h2>
        <!-- แสดงข้อความผิดพลาด -->
        <?php if (isset($error)): ?>
            <div class="mb-4 bg-red-100 text-red-700 text-sm p-2 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <!-- ฟอร์มล็อกอิน -->
        <form action="" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">อีเมล</label>
                <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg">เข้าสู่ระบบ</button>
        </form>
        <div class="text-center mt-4">
            <a href="register.php" class="text-blue-500 hover:underline">สมัครสมาชิก</a> | 
            <a href="forgot_password.php" class="text-blue-500 hover:underline">ลืมรหัสผ่าน?</a>
        </div>
    </div>
</body>
</html>
