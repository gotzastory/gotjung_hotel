<?php
// เริ่มต้น Session และตรวจสอบการเข้าสู่ระบบ
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_Login.php');
    exit();
}

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// ดึงข้อมูลจากตาราง rooms
$result = $conn->query("SELECT * FROM rooms ORDER BY id_rooms ASC");

// จัดการเพิ่มข้อมูลห้องใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_name = $conn->real_escape_string($_POST['room_name']);
    $price = floatval($_POST['price']);
    $type_rooms = $conn->real_escape_string($_POST['type_rooms']);
    $description = $conn->real_escape_string($_POST['description']);
    $amenities = isset($_POST['amenities']) ? implode(", ", $_POST['amenities']) : '';
    $amenities = $conn->real_escape_string($amenities);

    // แยกโฟลเดอร์สำหรับ main_image และ gallery_image
    $main_image_dir = '../assets/images/main_image/';
    $gallery_upload_dir = '../assets/images/gallery/';

    // ตรวจสอบและอัปโหลดภาพหลัก
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        // ใช้ชื่อไฟล์ต้นฉบับ
        $image_name = pathinfo($image['name'], PATHINFO_FILENAME) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        if (!move_uploaded_file($image['tmp_name'], $main_image_dir . $image_name)) {
            $image_name = ''; // ถ้าอัปโหลดล้มเหลว ให้ค่าว่าง
        }
    }

    // ตรวจสอบและอัปโหลด Gallery Images
    $gallery_images = [];
    for ($i = 1; $i <= 4; $i++) {
        $gallery_image_name = '';
        if (isset($_FILES["gallery_image$i"]) && $_FILES["gallery_image$i"]['error'] == UPLOAD_ERR_OK) {
            $gallery_image = $_FILES["gallery_image$i"];
            // ใช้ชื่อไฟล์ต้นฉบับ + _g1, _g2, _g3, _g4
            $gallery_image_name = pathinfo($gallery_image['name'], PATHINFO_FILENAME) . "_g$i." . pathinfo($gallery_image['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($gallery_image['tmp_name'], $gallery_upload_dir . $gallery_image_name)) {
                $gallery_images[] = $gallery_image_name;
            } else {
                $gallery_images[] = ''; // หากอัปโหลดล้มเหลว
            }
        } else {
            $gallery_images[] = ''; // หากไม่มีการอัปโหลด
        }
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO rooms (name_rooms, price, image, description, type_rooms, amenities, gallery_image1, gallery_image2, gallery_image3, gallery_image4) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssssssss", $room_name, $price, $image_name, $description, $type_rooms, $amenities, $gallery_images[0], $gallery_images[1], $gallery_images[2], $gallery_images[3]);
    $stmt->execute();

    header("Location: manage_services.php");
    exit();
}

// จัดการแก้ไขข้อมูลห้อง
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_room'])) {
    $id_rooms = intval($_POST['id_rooms']);
    $room_name = $conn->real_escape_string($_POST['room_name']);
    $price = floatval($_POST['price']);
    $type_rooms = $conn->real_escape_string($_POST['type_rooms']);
    $description = $conn->real_escape_string($_POST['description']);
    $amenities = isset($_POST['amenities']) ? implode(", ", $_POST['amenities']) : '';
    $amenities = $conn->real_escape_string($amenities);

    // อัปโหลดภาพหลักถ้ามีการเปลี่ยน
    $image_name = $conn->real_escape_string($_POST['current_image']);
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $image_name = pathinfo($image['name'], PATHINFO_FILENAME) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($image['tmp_name'], $main_image_dir . $image_name)) {
            if (!empty($_POST['current_image']) && file_exists($main_image_dir . $_POST['current_image'])) {
                unlink($main_image_dir . $_POST['current_image']); // ลบภาพเก่า
            }
        }
    }

    // ตรวจสอบและจัดการ Gallery Images
    $gallery_images = [];
    for ($i = 1; $i <= 4; $i++) {
        $current_gallery = $conn->real_escape_string($_POST["current_gallery_image$i"] ?? '');
        $gallery_image_name = $current_gallery;

        if (isset($_FILES["gallery_image$i"]) && $_FILES["gallery_image$i"]['error'] == UPLOAD_ERR_OK) {
            $gallery_image = $_FILES["gallery_image$i"];
            $gallery_image_name = pathinfo($gallery_image['name'], PATHINFO_FILENAME) . "_g$i." . pathinfo($gallery_image['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($gallery_image['tmp_name'], $gallery_upload_dir . $gallery_image_name)) {
                if (!empty($current_gallery) && file_exists($gallery_upload_dir . $current_gallery)) {
                    unlink($gallery_upload_dir . $current_gallery); // ลบไฟล์เก่าถ้ามี
                }
            }
        }
        $gallery_images[] = $gallery_image_name;
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE rooms SET name_rooms = ?, price = ?, type_rooms = ?, description = ?, amenities = ?, image = ?, gallery_image1 = ?, gallery_image2 = ?, gallery_image3 = ?, gallery_image4 = ? WHERE id_rooms = ?");
    $stmt->bind_param("sdssssssssi", $room_name, $price, $type_rooms, $description, $amenities, $image_name, $gallery_images[0], $gallery_images[1], $gallery_images[2], $gallery_images[3], $id_rooms);
    $stmt->execute();

    header("Location: manage_services.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Service</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- font font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</head>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="logo">Gotjung Hotel</div>
        <nav>
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> <!-- Font Awesome Icon -->
                Dashboard
            </a>
            <a href="manage_services.php">
                <i class="fas fa-concierge-bell"></i> <!-- Font Awesome Icon -->
                Manage Service
            </a>
            <a href="manage_bookings.php">
                <i class="fas fa-calendar-check"></i> <!-- Font Awesome Icon -->
                Manage Booking
            </a>
        </nav>
        <div class="footer">
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> <!-- Font Awesome Icon -->
                Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <button id="toggleButton">☰</button>
            <h1>Manage Service</h1>
        </header>
        <main class="main-content flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6 mt-1">Manage Service</h2>
            <button onclick="toggleModal('addRoomModal')" class="bg-green-500 text-white px-4 py-2 rounded mb-6">Add New Room</button>
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="table-auto w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="px-4 py-2 border border-gray-200">Room ID</th>
                            <th class="px-4 py-2 border border-gray-200">Room Name</th>
                            <th class="px-4 py-2 border border-gray-200">Price</th>
                            <th class="px-4 py-2 border border-gray-200">Image</th>
                            <th class="px-4 py-2 border border-gray-200">Description</th>
                            <th class="px-4 py-2 border border-gray-200">Type</th>
                            <th class="px-4 py-2 border border-gray-200">Amenities</th>
                            <th class="px-4 py-2 border border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='text-center border border-gray-200 hover:bg-gray-100'>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['id_rooms'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['name_rooms'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . number_format($row['price'], 2) . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>
                                        <img src='../assets/images/main_image/" . $row['image'] . "' alt='" . $row['name_rooms'] . "' class='h-32 w-auto object-cover mx-auto'>
                                    </td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['description'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['type_rooms'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['amenities'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>
                                        <button onclick=\"openEditModal('" . $row['id_rooms'] . "', '" . $row['name_rooms'] . "', '" . $row['price'] . "', '" . $row['type_rooms'] . "', '" . $row['description'] . "', '" . $row['amenities'] . "')\" class='bg-blue-500 text-white px-2 py-1 rounded'>Edit</button>
                                        <form method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this room?')\">
                                            <input type='hidden' name='delete_id' value='" . $row['id_rooms'] . "'>
                                            <button type='submit' class='bg-red-500 text-white px-2 py-1 rounded'>Delete</button>
                                        </form>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='border border-gray-300 px-4 py-2 text-center'>No rooms found</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="addRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 w-1/3">
            <h3 class="text-lg font-bold mb-4">Add New Room</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_room">
                <div class="mb-4">
                    <label for="add-room-name" class="block text-sm font-medium">Room Name:</label>
                    <input type="text" id="add-room-name" name="room_name" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="add-room-price" class="block text-sm font-medium">Price:</label>
                    <input type="number" step="0.01" id="add-room-price" name="price" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="add-room-type" class="block text-sm font-medium">Type:</label>
                    <select id="add-room-type" name="type_rooms" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="edit-room-description" class="block text-sm font-medium">Description:</label>
                    <textarea id="edit-room-description" name="description" class="w-full border border-gray-300 rounded px-3 py-2" rows="3" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="add-room-image" class="block text-sm font-medium">Image:</label>
                    <input type="file" id="add-room-image" name="image" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label for="add-room-amenities" class="block text-sm font-medium">Amenities:</label>
                    <div class="flex flex-wrap gap-2">
                        <label><input type="checkbox" name="amenities[]" value="Swimming Pool"> Swimming Pool</label>
                        <label><input type="checkbox" name="amenities[]" value="Fitness Center"> Fitness Center</label>
                        <label><input type="checkbox" name="amenities[]" value="Restaurant"> Restaurant</label>
                        <label><input type="checkbox" name="amenities[]" value="Spa"> Spa</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="add-gallery-image1" class="block text-sm font-medium">Gallery Image 1:</label>
                    <input type="file" id="add-gallery-image1" name="gallery_image1" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="add-gallery-image2" class="block text-sm font-medium">Gallery Image 2:</label>
                    <input type="file" id="add-gallery-image2" name="gallery_image2" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="add-gallery-image3" class="block text-sm font-medium">Gallery Image 3:</label>
                    <input type="file" id="add-gallery-image3" name="gallery_image3" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="add-gallery-image4" class="block text-sm font-medium">Gallery Image 4:</label>
                    <input type="file" id="add-gallery-image4" name="gallery_image4" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="toggleModal('addRoomModal')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Room</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 w-1/3">
            <h3 class="text-lg font-bold mb-4">Edit Room</h3>
            <form method="POST">
                <input type="hidden" id="edit-room-id" name="id_rooms">
                <input type="hidden" name="edit_room">
                <div class="mb-4">
                    <label for="edit-room-name" class="block text-sm font-medium">Room Name:</label>
                    <input type="text" id="edit-room-name" name="room_name" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="edit-room-price" class="block text-sm font-medium">Price:</label>
                    <input type="number" step="0.01" id="edit-room-price" name="price" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <!-- <div class="mb-4">
                    <label for="edit-room-type" class="block text-sm font-medium">Type:</label>
                    <input type="text" id="edit-room-type" name="type_rooms" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div> -->
                <div class="mb-4">
                    <label for="edit-room-type" class="block text-sm font-medium">Type:</label>
                    <select id="edit-room-type" name="type_rooms" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>
                <div class="mb-4"> 
                    <label for="edit-room-image" class="block text-sm font-medium">Image:</label>
                    <input type="file" id="edit-room-image" name="image" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label for="edit-room-description" class="block text-sm font-medium">Description:</label>
                    <textarea id="edit-room-description" name="description" class="w-full border border-gray-300 rounded px-3 py-2" rows="3" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="edit-room-amenities" class="block text-sm font-medium">Amenities:</label>
                    <div class="flex flex-wrap gap-2">
                        <label><input type="checkbox" name="amenities[]" value="Swimming Pool"> Swimming Pool</label>
                        <label><input type="checkbox" name="amenities[]" value="Fitness Center"> Fitness Center</label>
                        <label><input type="checkbox" name="amenities[]" value="Restaurant"> Restaurant</label>
                        <label><input type="checkbox" name="amenities[]" value="Spa"> Spa</label>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="edit-gallery-image1" class="block text-sm font-medium">Gallery Image 1:</label>
                    <input type="file" id="add-gallery-image1" name="gallery_image1" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="edit-gallery-image2" class="block text-sm font-medium">Gallery Image 2:</label>
                    <input type="file" id="add-gallery-image2" name="gallery_image2" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="edit-gallery-image3" class="block text-sm font-medium">Gallery Image 3:</label>
                    <input type="file" id="add-gallery-image3" name="gallery_image3" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="mb-4">
                    <label for="edit-gallery-image4" class="block text-sm font-medium">Gallery Image 4:</label>
                    <input type="file" id="add-gallery-image4" name="gallery_image4" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="toggleModal('editRoomModal')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>         
<script src="../assets/js/admin.js"></script>
</body>
</html>