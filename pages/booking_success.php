<?php
session_start();

// ตรวจสอบว่าได้ส่ง ID การจองมาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: ../user/Profile.php");
    exit();
}

// ดึงค่า ID การจองจาก URL
$booking_id = $_GET['id'];

include '../includes/db_connect.php';

// ดึงรายละเอียดการจองจากฐานข้อมูล
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
    WHERE b.id_booking = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $booking = $result->fetch_assoc();
} else {
    echo "ไม่พบข้อมูลการจอง";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจอง</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-100">

<!-- Booking Details -->
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-lg">
        <h1 class="text-2xl font-bold text-orange-500 text-center mb-6">
            <i class="fas fa-receipt"></i> GotJung Services
        </h1>

        <div class="border-b pb-4 mb-4">
            <p class="font-bold text-gray-700"><i class="fas fa-calendar-alt"></i> Reservation</p>
            <p>วันที่: <?php echo htmlspecialchars($booking['booking_date']); ?></p>
            <p>Booking ID: <?php echo htmlspecialchars($booking['id_booking']); ?></p>
        </div>

        <div class="border-b pb-4 mb-4">
            <p class="font-bold text-gray-700"><i class="fas fa-user"></i> Bill To:</p>
            <p>วันที่เช็คอิน: <?php echo htmlspecialchars($booking['check_in']); ?></p>
            <p>วันที่เช็คเอาท์: <?php echo htmlspecialchars($booking['check_out']); ?></p>
            <p>
                สถานะ:
                <span class="<?php echo $booking['payment_status'] == 'paid' ? 'bg-green-100 text-green-600 px-2 py-1 rounded' : 'bg-red-100 text-red-600 px-2 py-1 rounded'; ?>">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($booking['payment_status']); ?>
                </span>
            </p>
        </div>

        <div class="border-b pb-4 mb-4">
            <p class="font-bold text-gray-700"><i class="fas fa-info-circle"></i> Description</p>
            <div class="flex justify-between">
                <p>ห้อง: <?php echo htmlspecialchars($booking['name_rooms']); ?></p>
                <p><?php echo number_format($booking['total_price'], 2); ?> B</p>
            </div>
            <div class="flex justify-between">
                <p>จำนวนคืน</p>
                <p><?php echo htmlspecialchars($booking['nights']); ?></p>
            </div>
        </div>

        <div class="flex justify-between font-bold text-lg text-gray-700">
            <p>Total</p>
            <p><?php echo number_format($booking['total_price'], 2); ?> B</p>
        </div>

        <p class="text-center mt-4 text-gray-500"><i class="fas fa-smile"></i> ขอบคุณสำหรับการจอง</p>
        <p class="text-center text-sm text-gray-400">
            **หมายเหตุ: สามารถแสดงหน้านี้เป็นหลักฐานการจองห้องพัก
        </p>

        <div class="text-center mt-6">
            <a href="../index.php" class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600">
                <i class="fas fa-home"></i> กลับสู่หน้าหลัก
            </a>
        </div>
    </div>
</div>

</body>
</html>
