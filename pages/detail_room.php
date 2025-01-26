<?php
session_start();

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// ตรวจสอบ room_id
if (!isset($_GET['room_id']) || !is_numeric($_GET['room_id'])) {
    die("Invalid request. Room ID is required.");
}
$room_id = $_GET['room_id'];

// ดึงข้อมูลห้องพักจากฐานข้อมูล
$query = "SELECT * FROM rooms WHERE id_rooms = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('i', $room_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Room not found.");
}
$room = $result->fetch_assoc();

// เตรียมข้อมูล Gallery Images
$gallery_images = [];
for ($i = 1; $i <= 4; $i++) {
    $gallery_column = "gallery_image$i";
    if (!empty($room[$gallery_column])) {
        $gallery_images[] = "../assets/images/gallery/" . htmlspecialchars($room[$gallery_column]);
    }
}

// จัดการค่าเริ่มต้นของ amenities
$amenities = !empty($room['amenities']) ? explode(',', $room['amenities']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room Details</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="../assets/css/index.css">
<link rel="stylesheet" href="../assets/css/detail_room.css">
<body class="bg-gray-100">
    <!-- Navbar -->
    <header class="bg-orange-500 text-white py-4">  
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">GotJung Hotel</h1>
            <div class="flex items-center space-x-4 relative">
                <nav class="flex space-x-4 font-bold">
                    <a href="../index.php" class="hover:text-gray-200">Home</a>
                    <a href="room.php" class="hover:text-gray-200">Room</a>
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
                    <a href="../user/User_Login.php" class="bg-orange-400 text-white px-4 py-2 rounded hover:bg-orange-200 transition">
                        ลงทะเบียน / เข้าสู่ระบบ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Room Details Section -->
    <div class="container mx-auto py-6">
        <!-- Main Image -->
        <div class="bg-white rounded-lg shadow-md">
            <img src="../assets/images/main_image/<?php echo htmlspecialchars($room['image']); ?>" 
                 alt="<?php echo htmlspecialchars($room['name_rooms']); ?>" 
                 class="w-full h-96 object-cover rounded-t-lg">

            <div class="px-8 py-6">
                <!-- Gallery Section -->
                <h3 class="text-xl text-orange-500 font-bold mb-4">แกลเลอรีรูปภาพ</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <?php foreach ($gallery_images as $index => $image): ?>
                        <?php if (file_exists($image)): ?>
                            <div class="overflow-hidden rounded-lg">
                                <img src="<?php echo $image; ?>" 
                                    alt="Gallery image <?php echo $index + 1; ?>" 
                                    class="w-full h-24 md:h-32 object-cover hover:scale-105 transition-transform cursor-pointer"
                                    onclick="openModal(<?php echo $index; ?>)">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Room Details -->
                <h2 class="text-3xl font-bold text-orange-500 mb-4"><?php echo htmlspecialchars($room['name_rooms']); ?></h2>
                <p class="text-lg text-orange-500 font-bold mb-4">฿<?php echo number_format($room['price'], 2); ?> / คืน</p>
                <h3 class="text-xl font-bold mb-2">รายละเอียด</h3>
                <p class="text-gray-600 mb-6"><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>

                <!-- Amenities Section -->
                <h3 class="text-xl font-bold mb-2">สิ่งอำนวยความสะดวก</h3>
                <div class="flex flex-wrap gap-2 mb-6">
                    <?php foreach ($amenities as $amenity): ?>
                        <span class="bg-orange-100 text-orange-500 px-4 py-2 rounded-full text-sm font-bold">
                            <?php echo htmlspecialchars(trim($amenity)); ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <!-- Booking Button -->
                <div class="text-center">
                    <button 
                        class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 inline-block"
                        onclick="openBookingModal()">จองทันที
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center hidden z-50 transition-opacity duration-300 ease-in-out">
        <div class="bg-white rounded-xl shadow-lg p-6 relative max-w-4xl w-full mx-4">
            <!-- Close Button -->
            <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeModal()">
                <span class="text-2xl">&times;</span>
            </button>
            
            <!-- Modal Header -->
            <h3 class="text-2xl font-bold text-orange-500 mb-6 text-left">รูปภาพ</h3>

            <!-- Main Image -->
            <div class="relative flex items-center justify-center mb-6">
                <button class="absolute left-4 p-3 bg-gray-100 rounded-full shadow-md hover:bg-orange-500 hover:text-white transition duration-300" onclick="prevImage()">
                    &#8592;
                </button>
                <img id="modalImage" class="rounded-lg max-w-full max-h-[70vh] object-contain transition-transform duration-300 hover:scale-105" src="" alt="รูปภาพ">
                <button class="absolute right-4 p-3 bg-gray-100 rounded-full shadow-md hover:bg-orange-500 hover:text-white transition duration-300" onclick="nextImage()">
                    &#8594;
                </button>
            </div>

            <!-- Thumbnails -->
            <div class="flex justify-center space-x-4">
                <?php foreach ($gallery_images as $index => $image): ?>
                    <img class="thumbnail w-40 h-32 rounded-md border-2 border-transparent hover:border-orange-500 cursor-pointer transition-transform duration-300 hover:scale-110" 
                        src="<?php echo $image; ?>" 
                        alt="Thumbnail <?php echo $index + 1; ?>" 
                        onclick="openModal(<?php echo $index; ?>)">
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal จองห้องพัก -->
    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg p-6 relative max-w-md w-full mx-4">
            <!-- Close Button -->
            <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" onclick="closeBookingModal()">
                <span class="text-2xl">&times;</span>
            </button>
            
            <!-- Modal Header -->
            <h3 class="text-2xl font-bold text-orange-600 mb-6 text-center">จองห้องพัก</h3>

            <!-- Booking Form -->
            <form id="bookingForm" action="booking.php" method="GET" class="space-y-4">
                <!-- Hidden Input for Room ID -->
                <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['id_rooms']); ?>">

                <!-- Check-in Date -->
                <div>
                    <label for="checkInDate" class="block text-gray-700 font-bold">วันที่เช็คอิน</label>
                    <input type="date" id="checkInDate" name="check_in" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                </div>

                <!-- Check-out Date -->
                <div>
                    <label for="checkOutDate" class="block text-gray-700 font-bold">วันที่เช็คเอาท์</label>
                    <input type="date" id="checkOutDate" name="check_out" class="w-full border border-gray-300 rounded-lg px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                </div>

                <div class="bg-orange-50 rounded-lg p-4 font-bold">
                    <p>จำนวนคืน: <span id="numberOfNights" class="text-orange-700">0</span> คืน</p>
                    <input type="hidden" id="nightsInput" name="nights" value="0">
                    <p>ราคาทั้งหมด: ฿ <span id="totalPrice" class="text-orange-700">0</span></p>
                    <input type="hidden" id="totalPriceInput" name="total_price" value="0">
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-orange-500 text-white font-bold px-6 py-2 rounded-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1">
                        ยืนยันการจอง
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const galleryImages = <?php echo json_encode($gallery_images); ?>; // รูปภาพทั้งหมดใน JSON
        const roomPricePerNight = <?php echo htmlspecialchars($room['price']); ?>; // ราคาต่อคืนจาก PHP
    </script>
    <script src="../assets/js/detail_room.js" defer></script>
    <script src="../assets/js/index.js"></script>
</body>
</html>
 