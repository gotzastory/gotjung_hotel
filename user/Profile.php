<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (!isset($_SESSION['customer_name'])) { 
    header("Location: ../user/User_Login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จาก Session
$customer_name = $_SESSION['customer_name'];
$customer_id = $_SESSION['id_customers']; // ต้องแน่ใจว่าค่านี้ถูกกำหนดใน Login

include '../includes/db_connect.php';

// ดึงข้อมูลประวัติการจองจากตาราง bookings
$stmt = $conn->prepare("
    SELECT 
        b.id_booking,
        b.check_in,
        b.check_out,
        b.nights,
        b.total_price,
        b.booking_date,
        b.payment_status,
        r.name_rooms
    FROM bookings b
    JOIN rooms r ON b.id_rooms = r.id_rooms
    WHERE b.id_customers = ?
    ORDER BY b.booking_date DESC
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

// Debug ค่า $customer_id (ใช้เฉพาะตอนตรวจสอบ)
if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการจอง</title>
    <!-- Tailwindcss script -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- font font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <!-- AOS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- index -->
    <link rel="stylesheet" href="../assets/css/index.css">
    <script src="../assets/js/index.js" defer></script>  
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

 <!-- Navbar -->
 <header class="bg-orange-500 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">GotJung Hotel</h1>
            <div class="flex items-center space-x-4 relative">
                <nav class="flex space-x-4 font-bold">
                    <a href="../index.php" class="hover:text-gray-200">Home</a>
                    <a href="../pages/room.php" class="hover:text-gray-200">Room</a>
                    <a href="#contact" class="hover:text-gray-200">Contact</a>
                    <a href="#amenities" class="hover:text-gray-200">Amenities</a>
                </nav>
                <span class="border-l border-white h-6"></span>
                <?php if (isset($_SESSION['customer_name'])): ?>
                    <!-- Dropdown Button -->
                    <div class="relative">
                        <button id="dropdownButton" class="bg-orange-400 px-4 py-2 rounded-lg hover:bg-orange-700 transition focus:outline-none flex items-center space-x-2">
                            <i class="fas fa-user"></i> <!-- เพิ่มไอคอน -->
                            <span>ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
                            <span>▼</span>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="dropdownMenu" class="absolute mt-2 w-48 bg-white text-black rounded-lg shadow-lg hidden" style="z-index: 50;">
                            <a href="../user/Profile.php" class="block px-4 py-2 hover:bg-gray-200">โปรไฟล์</a>
                            <a href="../user/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">ออกจากระบบ</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login/Register Button -->
                    <a href="user/User_Login.php" class="bg-orange-400 text-white px-4 py-2 rounded hover:bg-orange-200 transition">
                        ลงทะเบียน / เข้าสู่ระบบ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>   

<!-- Profile Section -->
<main class="container mx-auto mt-8 p-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-2xl font-bold mb-4 text-orange-500">ประวัติการจอง</h1>
    <p class="text-gray-700">ยินดีต้อนรับ: <span class="font-semibold text-orange-500"><?php echo htmlspecialchars($customer_name); ?></span></p>

    <!-- Booking History -->
    <section class="mt-8">
        <h2 class="text-xl font-bold mb-4">รายการจองของคุณ</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse bg-white rounded-xl shadow-lg">
                <thead class="bg-orange-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-bold rounded-tl-xl">รหัสการจอง</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">ชื่อห้อง</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">วันที่เช็คอิน</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">วันที่เช็คเอาท์</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">จำนวนคืน</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">ราคา (บาท)</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">สถานะการชำระเงิน</th>
                        <th class="py-3 px-4 text-left text-sm font-bold">เวลาจอง</th>
                        <th class="py-3 px-4 text-left text-sm font-bold rounded-tr-xl">รายละเอียด</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['id_booking']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['name_rooms']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['check_in']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['check_out']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['nights']); ?></td>
                                <td class="py-3 px-4"><?php echo number_format($row['total_price'], 2); ?></td>
                                <td class="py-3 px-4">
                                    <span class="<?php echo $row['payment_status'] == 'paid' ? 'text-green-500 font-bold' : 'text-red-500 font-bold'; ?>">
                                        <?php echo htmlspecialchars($row['payment_status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                <td class="py-3 px-4 text-center">
                                    <a href="../pages/booking_success.php?id=<?php echo $row['id_booking']; ?>" 
                                    class="text-blue-500 hover:underline">
                                        ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-gray-500">ไม่มีประวัติการจอง</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>
