<?php
session_start();

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// กำหนดค่าตัวแปรเริ่มต้นสำหรับการค้นหา
$type = isset($_GET['type']) ? $_GET['type'] : '';
$max_price = isset($_GET['price']) ? $_GET['price'] : 10000;

// Query ดึงข้อมูลห้องพัก
$query = "SELECT * FROM rooms WHERE price <= ?";
$params = [$max_price];
if (!empty($type)) {
    $query .= " AND type_rooms = ?"; // แก้ไขคอลัมน์
    $params[] = $type;
}

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error); // ตรวจสอบข้อผิดพลาด
}

// Bind parameters
$stmt->bind_param(str_repeat('s', count($params)), ...$params);

// Execute
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
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

    <!-- Hero Section with Search -->
    <section class="relative bg-cover bg-center bg-no-repeat" style="background-image: url('../assets/images/pngtree-exquisite-hotel-bedroom-suite-with-a-classic-orange-theme-rendered-in-picture-image_4058872.jpg'); height: 400px;">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-white">
            <h2 class="text-4xl font-bold mb-4">Welcome to GotJung Hotel</h2>
            <p class="text-lg mb-6">Discover luxury, comfort, and convenience in the heart of the city</p>
        </div>
        <!-- Search Section -->
        <div class="absolute inset-x-0 bottom-0 transform translate-y-1/2">
            <div class="container mx-auto">
                <form method="GET" class="bg-white p-4 shadow-md rounded-lg max-w-3xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-bold mb-2 text-orange-400">ประเภทห้อง</label>
                            <select id="type" name="type" class="w-full border rounded p-2">
                                <option value="">-- เลือกประเภทห้อง --</option>
                                <option value="standard" <?php echo $type == 'standard' ? 'selected' : ''; ?>>Standard</option>
                                <option value="deluxe" <?php echo $type == 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                                <option value="suite" <?php echo $type == 'suite' ? 'selected' : ''; ?>>Suite</option>
                            </select>
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-bold mb-2 text-orange-400">ช่วงราคา</label>
                            <input
                                type="range"
                                id="price"
                                name="price"
                                min="0"
                                max="10000"
                                step="500"
                                class="w-full"
                                value="<?php echo $max_price; ?>"
                                oninput="updatePrice(this.value)"
                            >
                            <span>ราคา <span id="price-display">0</span> - <?php echo $max_price; ?> บาท</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-orange-500 text-white w-full py-3 rounded-lg hover:bg-orange-600 font-bold">ค้นหา</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <div id="rooms" class="container mx-auto py-16">
        <h2 class="text-2xl font-bold mb-8 text-orange-500">รายการห้องทั้งหมด</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="bg-white rounded-lg shadow-md p-4">';
                        echo '<img src="../assets/images/' . $row['image'] . '" alt="' . htmlspecialchars($row['name_rooms']) . '" class="h-40 w-full object-cover rounded mb-4">';
                        echo '<h3 class="text-lg font-bold mb-2">' . htmlspecialchars($row['name_rooms']) . '</h3>';
                        echo '<p class="text-orange-500 font-bold mb-2">฿' . number_format($row['price'], 2) . '</p>';
                        echo '<p class="text-gray-700 text-sm mb-4">' . htmlspecialchars($row['description']) . '</p>';
                        echo '<a href="detail_room.php?room_id=' . $row['id_rooms'] . '" class="bg-orange-500 text-white font-bold w-full block text-center py-2 rounded hover:bg-orange-600">Book now</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="text-gray-500 text-center">No rooms available at the moment.</p>';
                }
            ?>
        </div>
    </div>

    <script>
        function updatePrice(value) {
    document.getElementById('price-display').textContent = value;
    }

    // กำหนดค่าเริ่มต้นเมื่อโหลดหน้า
    document.addEventListener('DOMContentLoaded', () => {
        const priceInput = document.getElementById('price');
        updatePrice(priceInput.value);
    });
    </script>
</body>
</html>
