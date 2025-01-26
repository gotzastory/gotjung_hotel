<?php
session_start();
include('../includes/db_connect.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
if (!isset($_SESSION['id_customers'])) {
    die("กรุณาเข้าสู่ระบบก่อนทำการจองห้องพัก");
}

// ตรวจสอบว่าฟอร์มถูกส่งมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $room_id = $_POST['room_id'] ?? null;
    $check_in = $_POST['check_in'] ?? null;
    $check_out = $_POST['check_out'] ?? null;
    $nights = $_POST['nights'] ?? null;
    $total_price = $_POST['total_price'] ?? null;
    $proof_file = $_FILES['payment_proof']['name'] ?? null;

    // ตรวจสอบความถูกต้องของข้อมูล
    if (!$room_id || !$check_in || !$check_out || !$nights || !$total_price || !$proof_file) {
        die("ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบและลองอีกครั้ง");
    }

    // จัดการอัปโหลดไฟล์หลักฐาน
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($proof_file);
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_types)) {
        die("รูปแบบไฟล์ไม่ถูกต้อง อนุญาตเฉพาะไฟล์: " . implode(', ', $allowed_types));
    }

    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
        // เพิ่มข้อมูลลงในตาราง bookings
        $stmt = $conn->prepare("INSERT INTO bookings (id_rooms, check_in, check_out, nights, total_price, payment_slip, booking_date, payment_status, id_customers) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending', ?)");
        $stmt->bind_param('issidss', $room_id, $check_in, $check_out, $nights, $total_price, $proof_file, $_SESSION['id_customers']);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "การจองและอัปโหลดหลักฐานการชำระเงินสำเร็จ!";
            header("Location: ../index.php");
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }
    } else {
        echo "การอัปโหลดไฟล์ล้มเหลว กรุณาลองใหม่";
    }
}

// รับค่าการจองจาก GET
$room_id = $_GET['room_id'] ?? null;
$check_in = $_GET['check_in'] ?? null;
$check_out = $_GET['check_out'] ?? null;
$nights = $_GET['nights'] ?? null;
$total_price = $_GET['total_price'] ?? null;

$query = $conn->prepare("SELECT * FROM rooms WHERE id_rooms = ?");
$query->bind_param('i', $room_id);
$query->execute();
$room = $query->get_result()->fetch_assoc();

if (!$room) {
    die("ไม่พบข้อมูลห้องพัก");
}

$room_image = isset($room['image']) ? "../assets/images/main_image/" . htmlspecialchars($room['image']) : "../assets/images/default_image.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจอง</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- <script src="../assets/js/booking.js" defer></script> -->
    <style>
        body {
            background: linear-gradient(to bottom, #f97316, #fef3c7);
        }
        .header-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">

<!-- ข้อมูลการจอง -->
<div class="bg-white shadow-lg rounded-lg w-full max-w-4xl relative">
    <!-- Header Image -->
    <div class="header-image relative" style="background-image: url('<?php echo $room_image; ?>');">
        <!-- Text Overlay -->
        <h1 class="absolute inset-0 flex items-center justify-center text-3xl font-bold text-white bg-black bg-opacity-50">
            รายละเอียดการจอง
        </h1>
    </div>

    <div class="content px-8 py-6">
        <!-- ข้อมูลห้องพัก -->
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-700 flex items-center">
                <i class="fas fa-bed text-orange-500 mr-2"></i> ข้อมูลห้องพัก
            </h2>
            <p class="text-gray-600">ชื่อห้องพัก: <span class="font-medium"><?php echo htmlspecialchars($room['name_rooms']); ?></span></p>
            <p class="text-gray-600">ราคาต่อคืน: <span class="font-medium">฿<?php echo number_format($room['price'], 2); ?></span></p>
        </div>

        <!-- รายละเอียดการเข้าพัก -->
        <div class="mb-6">
            <h2 class="text-lg font-bold text-gray-700 flex items-center">
                <i class="fas fa-calendar-alt text-orange-500 mr-2"></i> รายละเอียดการเข้าพัก
            </h2>
            <p class="text-gray-600">วันที่เช็คอิน: <span class="font-medium"><?php echo htmlspecialchars($check_in); ?></span></p>
            <p class="text-gray-600">วันที่เช็คเอาท์: <span class="font-medium"><?php echo htmlspecialchars($check_out); ?></span></p>
            <p class="text-gray-600">จำนวนคืน: <span class="font-medium"><?php echo htmlspecialchars($nights); ?> คืน</span></p>
            <p class="text-gray-600">ราคารวม: <span class="font-medium">฿<?php echo number_format($total_price, 2); ?></span></p>
        </div>

        <!-- ปุ่มดำเนินการ -->
        <div class="flex justify-center space-x-4 mb-6">
            <a href="javascript:void(0)" 
                onclick="openPaymentModal('<?php echo htmlspecialchars($room_id, ENT_QUOTES, 'UTF-8'); ?>')" 
                class="bg-orange-500 text-white px-6 py-2 rounded-lg shadow hover:bg-orange-600 flex items-center">
                <i class="fas fa-credit-card mr-2"></i> ดำเนินการชำระเงิน
            </a>
            <a href="../index.php" 
            class="bg-gray-500 text-white px-6 py-2 rounded-lg shadow hover:bg-gray-600 flex items-center">
                <i class="fas fa-times-circle mr-2"></i> ยกเลิก
            </a>
        </div>

        <!-- Footer -->
        <footer class="footer bg-orange-100 text-center py-4 rounded-lg">
            <p class="text-gray-700">ขอบคุณที่ใช้บริการของเรา</p>
        </footer>
    </div>
</div>

<!-- Modal การชำระเงิน -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
        <!-- ปุ่มปิด -->
        <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closePaymentModal()">
            <span class="text-2xl">&times;</span>
        </button>

        <!-- Header -->
        <h3 class="text-2xl font-bold text-orange-500 mb-6 text-center">รายละเอียดการชำระเงิน</h3>

        <!-- รายละเอียดธนาคาร -->
        <div class="text-center mb-4">
            <p class="text-gray-700 font-bold">ชื่อธนาคาร: กสิกรไทย</p>
            <p class="text-gray-700">หมายเลขบัญชี: 0621238906</p>
            <p class="text-gray-600">ราคารวม: <span class="font-medium">฿<?php echo number_format($total_price, 2); ?></span></p>
        </div>

        <!-- QR Code -->
        <div class="flex justify-center mb-4">
            <img src="../assets/images/qr_code.jpg" alt="QR Code" class="w-32 h-32 object-contain">
        </div>

        <!-- ฟอร์มอัปโหลดหลักฐาน -->
        <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
            <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>">
            <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>">
            <input type="hidden" name="nights" value="<?php echo htmlspecialchars($nights); ?>">
            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

            <label for="proofFile" class="block font-bold mb-2">อัปโหลดหลักฐานการชำระเงิน</label>
            <input type="file" id="proofFile" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required class="block w-full text-gray-600 border rounded px-4 py-2">
            <button type="submit" class="mt-4 bg-orange-500 text-white px-6 py-2 rounded-lg shadow hover:bg-orange-600">
                <i class="fas fa-upload mr-2"></i> ยืนยันชำระเงิน
            </button>
        </form>
    </div>
</div>

<script>
    function openPaymentModal() {
    const modal = document.getElementById("paymentModal");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function closePaymentModal() {
    const modal = document.getElementById("paymentModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}
</script>


</body>
</html>
