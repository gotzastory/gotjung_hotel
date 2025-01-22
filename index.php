<?php
session_start(); // เริ่มต้น Session

// เชื่อมต่อฐานข้อมูล
include './includes/db_connect.php';

// ดึงข้อมูลห้องพักจากตาราง rooms
$query = "SELECT * FROM rooms";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GotJung Hotel</title>
    <!-- Tailwindcss script -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- font font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <!-- AOS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- index -->
    <link rel="stylesheet" href="./assets/css/index.css">
    <script src="./assets/js/index.js" defer></script>  
</head>
<body class="font-sans">
    <!-- Navbar -->
    <header class="bg-orange-500 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">GotJung Hotel</h1>
            <div class="flex items-center space-x-4 relative">
                <nav class="flex space-x-4 font-bold">
                    <a href="index.php" class="hover:text-gray-200">Home</a>
                    <a href="./pages/room.php" class="hover:text-gray-200">Room</a>
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
                            <a href="user/Profile.php" class="block px-4 py-2 hover:bg-gray-200">โปรไฟล์</a>
                            <a href="user/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-200">ออกจากระบบ</a>
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

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-screen" style="background-image: url('./assets/images/pngtree-exquisite-hotel-bedroom-suite-with-a-classic-orange-theme-rendered-in-picture-image_4058872.jpg');">
        <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
            <div class="bg-black bg-opacity-70 p-12 rounded-lg text-center">
                <h1 class="text-5xl font-bold">Welcome to GotJung Hotel</h1>
                <p class="text-xl mt-8">Discover comfort, luxury, and unforgettable moments at our hotel!</p>
                <div class="mt-10">
                    <a href="./pages/room.php" class="bg-orange-500 px-10 py-3 font-bold rounded hover:bg-orange-600">Book now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-12">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6 text-orange-500 text-left">Our Rooms</h2>
            <p class="mb-8 text-gray-600 text-left">Explore a selection of rooms crafted to provide a remarkable stay experience.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-0">
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="bg-white rounded-lg p-4" style="width: 300px; height: 450px; margin: 0 auto;">';
                            echo '<img src="./assets/images/' . $row['image'] . '" alt="' . htmlspecialchars($row['name_rooms']) . '" class="h-40 w-full object-cover rounded mb-4">';
                            echo '<h3 class="text-lg font-bold mb-2 text-left">' . htmlspecialchars($row['name_rooms']) . '</h3>';
                            echo '<p class="text-orange-500 font-bold mb-2 text-left">฿' . number_format($row['price'], 2) . '</p>';
                            echo '<p class="text-gray-700 text-sm mb-4 text-left"><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>';
                            echo '<a href="pages/detail_room.php?room_id=' . $row['id_rooms'] . '" class="bg-orange-500 text-white font-bold w-full block text-center py-2 rounded hover:bg-orange-600">Book now</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="text-gray-500 text-center">No rooms available at the moment.</p>';
                    }
                ?>
            </div>
        </div>
    </section>

    <!-- Amenities Section -->
    <section id="amenities" class="py-12 bg-orange-100">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold text-orange-500 mb-6">Our Amenities</h2>
            <p class="text-orange-400 mb-10">Enjoy top-notch facilities designed for a comfortable stay.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Amenity Card -->
                <div class="bg-white rounded-lg p-12 text-center shadow">
                    <div class="text-orange-500 text-4xl mb-4">
                        <i class="fas fa-swimmer"></i> <!-- ไอคอนสระว่ายน้ำ -->
                    </div>
                    <h3 class="text-xl font-bold text-orange-500 mb-2">Swimming Pool</h3>
                    <p class="text-gray-600 text-sm">Relax in our outdoor pool with beautiful views.</p>
                </div>
                <!-- Fitness Center -->
                <div class="bg-white rounded-lg p-12 text-center shadow">
                    <div class="text-orange-500 text-4xl mb-4">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3 class="text-xl font-bold text-orange-500 mb-2">Fitness Center</h3>
                    <p class="text-gray-600 text-sm">Stay active with state-of-the-art equipment.</p>
                </div>
                <!-- Restaurant -->
                <div class="bg-white rounded-lg p-12 text-center shadow">
                    <div class="text-orange-500 text-4xl mb-4">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="text-xl font-bold text-orange-500 mb-2">Restaurant</h3>
                    <p class="text-gray-600 text-sm">Enjoy delicious meals prepared by top chefs.</p>
                </div>
                <!-- Spa -->
                <div class="bg-white rounded-lg p-12 text-center shadow">
                    <div class="text-orange-500 text-4xl mb-4" >
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="text-xl font-bold text-orange-500 mb-2">Spa</h3>
                    <p class="text-gray-600 text-sm">Relax and rejuvenate with premium spa services.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Guest Reviews Section -->
    <section id="reviews" class="py-12">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6 text-orange-500">What Our Guest Say</h2>
            <p class="mb-8 text-gray-600">Here's what some of our guests have shared about their experience</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Review Card -->
                <div class="bg-gray-100 rounded-lg p-4 text-left shadow">
                    <p class="text-gray-700 mb-4">"Lorem ipsum dolor sit amet consectetur adipiscing elit."</p>
                    <div class="flex items-center">
                        <i class="fas fa-user text-2xl text-gray-500 mr-4"></i> <!-- ไอคอนแทนโปรไฟล์ -->
                        <div>
                            <p class="text-sm font-bold">Tnp</p>
                            <p class="text-sm text-gray-500">Stayed in Sweet Room</p>
                        </div>
                    </div>
                </div>
                
                <!-- Another Review Card -->
                <div class="bg-gray-100 rounded-lg p-4 text-left shadow">
                    <p class="text-gray-700 mb-4">"Lorem ipsum dolor sit amet consectetur adipiscing elit."</p>
                    <div class="flex items-center">
                        <i class="fas fa-user text-2xl text-gray-500 mr-4"></i> <!-- ไอคอนแทนโปรไฟล์ -->
                        <div>
                            <p class="text-sm font-bold">Tnp</p>
                            <p class="text-sm text-gray-500">Stayed in Sweet Room</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-8 bg-white-100">
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
                            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-700">Send message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-orange-500 text-white py-6">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Left Side -->
            <div class="text-sm font-bold">
                ปวส.1 กลุ่ม <span class="font-bold">Hotel Booking</span>
            </div>
            <!-- Social Icons -->
            <div class="flex space-x-4">
                <a href="https://github.com" target="_blank" class="hover:text-gray-200">
                    <i class="fab fa-github text-lg"></i>
                </a>
                <a href="https://facebook.com" target="_blank" class="hover:text-gray-200">
                    <i class="fab fa-facebook text-lg"></i>
                </a>
                <a href="https://instagram.com" target="_blank" class="hover:text-gray-200">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
            </div>
        </div>
        <!-- Divider -->
        <div class="border-t border-gray-300 my-4"></div>
        <!-- Bottom Section -->
        <div class="text-center text-sm mt-4">
            © 2025 GotJung Hotel, All rights reserved.
        </div>
    </footer>

</body>
</html>