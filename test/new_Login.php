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
<body class="bg-orange-100 flex items-center justify-center min-h-screen">
    <!-- Card -->
    <div class="flex flex-col md:flex-row bg-white shadow-lg rounded-lg overflow-hidden max-w-4xl w-full">
        <!-- Image Section -->
        <div class="w-full md:w-1/2">
            <img src="../assets/images/index/beautiful-interior-view-of-a-room-at-coastal-free-photo.jpg" alt="Hotel Image" class="w-full h-full object-cover">
        </div>

        <!-- Form Section -->
        <div class="w-full md:w-1/2 p-8">
            <!-- Tabs -->
            <div class="flex justify-center space-x-2 mb-6">
                <a href="Register.php" class="bg-orange-100 text-orange-500 font-bold px-4 py-2 rounded-lg hover:text-orange-600 hover:bg-orange-200">สมัครสมาชิก</a>
                <a href="User_Login.php" class="bg-orange-500 text-white font-bold px-4 py-2 rounded-lg hover:bg-orange-600">เข้าสู่ระบบ</a>
            </div>


            <!-- Title -->
            <h2 class="text-2xl font-bold text-orange-600 mb-6">เข้าสู่ระบบ</h2>

            <!-- Form -->
            <form action="login_process.php" method="POST">
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 mb-2">อีเมล</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 mb-2">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-lg hover:bg-orange-600">เข้าสู่ระบบ</button>
            </form>

            <!-- Forgot Password -->
            <div class="mt-4 text-center">
                <a href="Forgot_password.php" class="text-orange-500 hover:underline">ลืมรหัสผ่าน?</a>
            </div>
        </div>
    </div>
</body>
</html>
