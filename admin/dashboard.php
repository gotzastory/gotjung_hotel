<?php
// เริ่มต้น Session และตรวจสอบการเข้าสู่ระบบ
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_Login.php');
    exit();
}

// เชื่อมต่อฐานข้อมูล
include '../includes/db_connect.php';

// ดึงข้อมูลสถิติ
$total_rooms = $conn->query("SELECT COUNT(*) AS total FROM rooms")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) AS total FROM booking")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$total_messages = $conn->query("SELECT COUNT(*) AS total FROM contacts")->fetch_assoc()['total'];

// ดึงข้อมูลจากตาราง contacts
$result = $conn->query("SELECT id_contacts AS id, full_name AS name, email, message, contact_date AS created_at FROM contacts ORDER BY contact_date DESC");
if (!$result) {
    die("Error in SQL Query: " . $conn->error); // แสดงข้อผิดพลาดของ SQL
}

// ตรวจสอบคำขอ Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id_contacts = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "success"; // ส่งข้อความ "success" กลับไป
        exit();
    } else {
        echo "error"; // ส่งข้อความ "error" กลับไป
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slide Sidebar</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
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
            <h1>Dashboard</h1>
        </header>

        <!-- Main Content -->
        <main class="main-content flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6">Welcome, Admin!</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-xl transition">
                    <h3 class="text-lg font-bold text-gray-700">Total Rooms</h3>
                    <p class="text-4xl text-blue-600 font-bold"><?php echo $total_rooms; ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-xl transition">
                    <h3 class="text-lg font-bold text-gray-700">Total Bookings</h3>
                    <p class="text-4xl text-blue-600 font-bold"><?php echo $total_bookings; ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-xl transition">
                    <h3 class="text-lg font-bold text-gray-700">Total Customers</h3>
                    <p class="text-4xl text-blue-600 font-bold"><?php echo $total_customers; ?></p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 text-center hover:shadow-xl transition">
                    <h3 class="text-lg font-bold text-gray-700">Total Messages</h3>
                    <p class="text-4xl text-blue-600 font-bold"><?php echo $total_messages; ?></p>
                </div>
            </div>

            <!-- Contact -->
            <h2 class="text-2xl font-bold mb-6 mt-6">Contact Messages</h2>
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="table-auto w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="px-4 py-2 border border-gray-200">#</th>
                            <th class="px-4 py-2 border border-gray-200">Name</th>
                            <th class="px-4 py-2 border border-gray-200">Email</th>
                            <th class="px-4 py-2 border border-gray-200">Message</th>
                            <th class="px-4 py-2 border border-gray-200">Date</th>
                            <th class="px-4 py-2 border border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        $result = $conn->query("SELECT id_contacts AS id, full_name AS name, email, message, contact_date AS created_at FROM contacts ORDER BY contact_date DESC");
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='text-center border border-gray-200 hover:bg-gray-100'>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td class='px-4 py-2 truncate'>" . htmlspecialchars($row['message']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($row['created_at']) . "</td>";
                                echo "<td class='px-4 py-2 flex justify-center space-x-2'>";
                                echo "<button onclick='deleteContact(" . $row['id'] . ")' class='px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600'>Delete</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr>";
                            echo "<td colspan='6' class='px-4 py-2 text-center text-gray-500'>No contacts found</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
