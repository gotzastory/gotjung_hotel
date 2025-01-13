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

    $image = $_FILES['image'];
    $image_name = time() . '_' . basename($image['name']);
    $upload_dir = '../assets/images/';
    $upload_file = $upload_dir . $image_name;

    if (move_uploaded_file($image['tmp_name'], $upload_file)) {
        $stmt = $conn->prepare("INSERT INTO rooms (name_rooms, price, image, description, type_rooms) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $room_name, $price, $image_name, $description, $type_rooms);
        $stmt->execute();

        // Redirect หลังเพิ่มข้อมูลสำเร็จ
        header("Location: manage_services.php");
        exit();
    }
}
// จัดการแก้ไขข้อมูลห้อง
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_room'])) {
    $id_rooms = intval($_POST['id_rooms']);
    $room_name = $conn->real_escape_string($_POST['room_name']);
    $price = floatval($_POST['price']);
    $type_rooms = $conn->real_escape_string($_POST['type_rooms']);
    $description = $conn->real_escape_string($_POST['description']);

    $stmt = $conn->prepare("UPDATE rooms SET name_rooms = ?, price = ?, type_rooms = ?, description = ? WHERE id_rooms = ?");
    $stmt->bind_param("sdssi", $room_name, $price, $type_rooms, $description, $id_rooms);
    $stmt->execute();

    // Redirect หลังแก้ไขข้อมูลสำเร็จ
    header("Location: manage_services.php");
    exit();
}
// จัดการลบข้อมูลห้อง
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id_rooms = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    // Redirect หลังลบข้อมูลสำเร็จ
    header("Location: manage_services.php");
    exit();
} // ปิด if ตรงนี้

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Service</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
    <div class="logo">Gotjung Hotel</div>
    <nav>
      <a href="dashboard.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M3 10h18M3 16h18"></path>
        </svg>
        Dashboard
      </a>
      <a href="manage_services.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m0 0l-4-4m4 4H3"></path>
        </svg>
        Manage Service
      </a>
      <a href="manage_bookings.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Manage Booking
      </a>
    </nav>
    <div class="footer">
      <a href="logout.php">Logout</a>
    </div>
  </div>

    <!-- Header -->
    <div class="main-content">
        <header>
            <button id="toggleButton">☰</button>
            <h1>Manage Service</h1>
        </header>
        <!-- Main Content -->
        <main class="main-content flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6 mt-1">Manage Service</h2>
            <!-- Button to open Add Room Modal -->
            <button onclick="toggleModal('addRoomModal')" class="bg-green-500 text-white px-4 py-2 rounded mb-6">Add New Room</button>
            <!-- ตาราง -->
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
                            <th class="px-4 py-2 border border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php         
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='text-center border border-gray-200 hover:bg-gray-100'>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['id_rooms'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['name_rooms'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . number_format($row['price'], 2) . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>
                                        <img src='../assets/images/" . $row['image'] . "' alt='" . $row['name_rooms'] . "' class='h-32 w-auto object-cover mx-auto'>
                                    </td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['description'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>" . $row['type_rooms'] . "</td>";
                                echo "<td class='border border-gray-300 px-4 py-2'>
                                        <button onclick=\"openEditModal('" . $row['id_rooms'] . "', '" . $row['name_rooms'] . "', '" . $row['price'] . "', '" . $row['type_rooms'] . "', '" . $row['description'] . "', '" . $row['image'] . "')\" class='bg-blue-500 text-white px-2 py-1 rounded'>Edit</button>
                                        <form method='POST' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this room?')\">
                                            <input type='hidden' name='delete_id' value='" . $row['id_rooms'] . "'>
                                            <button type='submit' class='bg-red-500 text-white px-2 py-1 rounded'>Delete</button>
                                        </form>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='border border-gray-300 px-4 py-2 text-center'>No rooms found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <!-- Add Room Modal -->
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
                    <input type="text" id="add-room-type" name="type_rooms" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="add-room-image" class="block text-sm font-medium">Image:</label>
                    <input type="file" id="add-room-image" name="image" class="w-full border border-gray-300 rounded px-3 py-2" accept="image/*" required>
                </div>
                <div class="mb-4">
                    <label for="add-room-description" class="block text-sm font-medium">Description:</label>
                    <textarea id="add-room-description" name="description" class="w-full border border-gray-300 rounded px-3 py-2" rows="3" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="toggleModal('addRoomModal')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Room</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Room Modal -->
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
                <div class="mb-4">
                    <label for="edit-room-type" class="block text-sm font-medium">Type:</label>
                    <input type="text" id="edit-room-type" name="type_rooms" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="edit-room-description" class="block text-sm font-medium">Description:</label>
                    <textarea id="edit-room-description" name="description" class="w-full border border-gray-300 rounded px-3 py-2" rows="3" required></textarea>
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