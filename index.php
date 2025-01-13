<?php
// เริ่มต้น Session
session_start();

// เชื่อมต่อกับฐานข้อมูล
include './includes/db_connect.php'; // ตรวจสอบเส้นทางให้ถูกต้อง

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลห้องพักจากฐานข้อมูล
$sql = "SELECT id_rooms, name_rooms, price, image, description FROM rooms";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
    if (isset($_POST['fullname'], $_POST['email'], $_POST['message'])) {
        // รับค่าจากฟอร์ม
        $full_name = $conn->real_escape_string($_POST['fullname']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone_number = isset($_POST['phone-number']) ? $conn->real_escape_string($_POST['phone-number']) : null;
        $message = $conn->real_escape_string($_POST['message']);

        // บันทึกข้อมูลลงในฐานข้อมูล
        $sql = "INSERT INTO contacts (full_name, email, phone_number, message)
                VALUES ('$full_name', '$email', '$phone_number', '$message')";

        if ($conn->query($sql)) {
            echo "<script>alert('Your message has been sent successfully!');</script>";
        } else {
            echo "<script>alert('Error: Unable to send your message. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GotJung</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<!-- Navbar -->
<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-bold">GotJung Hotel</h1>
        <div class="flex items-center relative">
            <?php if (isset($_SESSION['customer_name'])): ?>
                <!-- Dropdown Button -->
                <button id="dropdownButton" class="bg-blue-500 px-4 py-2 rounded-lg hover:bg-blue-700 transition focus:outline-none">
                    ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['customer_name']); ?> ▼
                </button>
                <!-- Dropdown Menu -->
                <div id="dropdownMenu" class="absolute mt-2 w-48 bg-white text-black rounded-lg shadow-lg hidden">
                    <a href="user/Profile.php" class="block px-4 py-2 hover:bg-gray-200">โปรไฟล์</a>
                    <a href="user/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">ออกจากระบบ</a>
                </div>
            <?php else: ?>
                <!-- Login/Register Button -->
                <a href="user/User_Login.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-200 transition">
                    ลงทะเบียน / เข้าสู่ระบบ
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="relative bg-cover bg-center h-96" style="background-image: url('./assets/image/462653930_1753325032094193_5048219898302441821_n.png');">
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="text-center text-black p-4">
            <h2 class="text-4xl font-bold mb-4">โรงแรม</h2>
            <form method="GET" action="test.php" class="bg-white p-4 rounded-lg flex space-x-2">
                <input type="text" name="destination" placeholder="จุดหมายปลายทาง" class="p-2 border rounded">
                <input type="date" name="check_in" class="p-2 border rounded">
                <input type="date" name="check_out" class="p-2 border rounded">
                <input type="number" name="guests" placeholder="จำนวนผู้เข้าพัก" class="p-2 border rounded">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ค้นหา</button>
            </form>
        </div>
    </div>

</section>

<!-- Available Rooms Section -->
<section class="py-8">
    <div class="container mx-auto px-4 relative">
        <h2 class="text-2xl font-bold text-center mb-6">Available Rooms</h2>

        <!-- Left and Right Controls -->
        <button id="scrollLeft"
            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-4xl font-bold text-gray-600 hover:text-gray-900 transition duration-300 z-10">
            ←
        </button>
        <button id="scrollRight"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-4xl font-bold text-gray-600 hover:text-gray-900 transition duration-300 z-10">
            →
        </button>

        <!-- Horizontal Scrollable Container -->
        <div id="roomsContainer" class="flex space-x-6 overflow-x-auto snap-x snap-mandatory scrollbar-hide">
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                    $image_path = "./assets/images/" . $row['image'];
                    $image = (!empty($row['image']) && file_exists(__DIR__ . "/assets/images/" . $row['image'])) 
                    ? $image_path 
                    : "./assets/images/default.jpg";

                    // Card with hover effect (ลบสุ่มสีออก)
                    echo "<div class='snap-center flex-none w-96 shadow-md rounded-lg overflow-hidden relative cursor-pointer hover:scale-105 hover:shadow-xl transition duration-500 bg-white' onclick='openModal(\"{$row['name_rooms']}\", \"{$row['description']}\", \"฿" . number_format($row['price'], 2) . "\", \"{$image}\")'>";
                    echo "<img src='{$image}' alt='Room Image' class='w-full h-72 object-cover rounded-t-xl'>";
                    echo "<div class='p-6'>";
                    echo "<h3 class='text-2xl font-bold text-blue-700'>{$row['name_rooms']}</h3>";
                    echo "<p class='text-gray-700 truncate mb-4'>{$row['description']}</p>";
                    echo "<p class='text-xl font-bold text-blue-900'>฿" . number_format($row['price'], 2) . "</p>";
                    echo "</div>";
                    echo "</div>";
                    }
                } else {
                    echo "<p class='text-center text-red-500'>ไม่พบห้องพัก</p>";
                }
            ?>
        </div>
    </div>
</section>
<!-- Modal with Animation -->
<div id="modal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300 opacity-0">
    <div id="modalContent" class="bg-gradient-to-br from-white via-blue-50 to-blue-100 rounded-lg shadow-2xl w-full max-w-lg p-6 relative transform scale-95 transition-transform duration-300">
        <img id="modalImage" src="" alt="Room Image" class="w-full h-48 object-cover rounded-md mb-4">
        <h2 id="modalTitle" class="text-3xl font-extrabold text-blue-800 mb-4"></h2>
        <p id="modalDescription" class="text-gray-700 mb-4"></p>
        <p id="modalPrice" class="text-lg font-bold text-blue-900 mb-6"></p>
        <div class="flex justify-end space-x-4">
            <a id="modalBookingLink" href="#" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">จองเลย</a>
            <button onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-300">ปิด</button>
        </div>
    </div>
</div>

<!-- Reviews Section -->
<section class="bg-gray-200 py-8">
    <div class="container mx-auto px-7">
        <h3 class="text-2xl font-bold text-center mb-6">Comment & Review</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white shadow-md rounded-lg p-5 flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <img src="./assets/images/profile.jpg" alt="Profile" class="w-16 h-16 rounded-full">
                </div>
                <div>
                    <h4 class="font-bold">Thanaphat Thongburee</h4>
                    <p class="text-sm">Date: 01/01/2024</p>
                    <p>โรงแรมนี้ดีมาก! ห้องพักสะอาดและสะดวกสบาย</p>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg p-5 flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <img src="./assets/images/profile.jpg" alt="Profile" class="w-16 h-16 rounded-full">
                </div>
                <div>
                    <h4 class="font-bold">Thanaphat Thongburee</h4>
                    <p class="text-sm">Date: 01/01/2024</p>
                    <p>โรงแรมนี้ดีมาก! ห้องพักสะอาดและสะดวกสบาย</p>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg p-5 flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <img src="./assets/images/profile.jpg" alt="Profile" class="w-16 h-16 rounded-full">
                </div>
                <div>
                    <h4 class="font-bold">Thanaphat Thongburee</h4>
                    <p class="text-sm">Date: 01/01/2024</p>
                    <p>โรงแรมนี้ดีมาก! ห้องพักสะอาดและสะดวกสบาย</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-8 bg-gray-200">
<div class="flex justify-center items-center">
  <div class="bg-white rounded-lg shadow-lg overflow-hidden flex w-4/5 max-w-5xl">
    <!-- Left Section -->
    <div class="w-1/2 p-10 bg-gray-50">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Contact US</h2>
      <p class="text-gray-600 mb-6">
          Contact US
      </p>
      <div class="flex items-center mb-4">
          <div class="w-6 h-6 text-gray-600 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 11.387C18.143 13.055 20 14.438 20 16.71c0 1.72-1.22 3.29-3.157 4.305C14.917 22.143 12.517 23 10 23c-2.517 0-4.917-.857-6.843-1.985C1.22 20 0 18.43 0 16.71c0-2.271 1.857-3.654 3.138-5.322C4.416 10.91 6.406 10 8.5 10c2.094 0 4.084.91 5.362 2.387z" />
              </svg>
          </div>
          <p class="text-gray-600">xxx<br>Thailand, xxx</p>
      </div>
      <div class="flex items-center mb-4">
          <div class="w-6 h-6 text-gray-600 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
              </svg>
          </div>
          <p class="text-gray-600">+66 xxx-xxx-xxxx</p>
      </div>
      <div class="flex items-center mb-4">
          <div class="w-6 h-6 text-gray-600 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H3m0 0a9 9 0 0018 0m-18 0A9 9 0 0112 3a9 9 0 019 9z" />
              </svg>
          </div>
          <p class="text-gray-600">hello@example.com</p>
      </div>
      <div class="mt-6">
          <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0922733758173!2d-122.08385108515195!3d37.421999979825955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fba02ec7a1f31%3A0xc7d82b3e48b84a2b!2sGoogleplex!5e0!3m2!1sen!2sus!4v1637590093569!5m2!1sen!2sus" 
              width="100%" 
              height="200" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy">
          </iframe>
      </div>
    </div>
    <!-- Right Section -->
    <div div class="w-1/2 p-10">
      <form action="#" method="POST">
        <div class="mt-4">
          <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
          <input type="text" id="fullname" name="fullname" class="mt-1 p-2 border rounded w-full" required>
        </div>
        <div class="mt-4">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" id="email" name="email" class="mt-1 p-2 border rounded w-full" required>
        </div>
        <div class="mt-4">
              <label for="phone-number" class="block text-sm font-medium text-gray-700">Phone number</label>
              <input type="text" id="phone-number" name="phone-number" class="mt-1 p-2 border rounded w-full">
        </div>
        <div class="mt-4">
          <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
          <textarea id="message" name="message" rows="4" class="mt-1 p-2 border rounded w-full"></textarea>
        </div>
        <div class="mt-6">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Send message</button>
        </div>
      </form>
    </div>
  </div>
</div>
</section>

<!-- Footer -->
<footer class="bg-blue-600 text-white py-4">
    <div class="text-center">
        &copy; 2025 GotJung Hotel. All Rights Reserved.
    </div>
</footer>

<script src="assets/js/index.js"></script>
</body>
</html>
