<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

include '../includes/db_connect.php';

// ดึงข้อมูลจากตาราง booking พร้อมเชื่อมกับ customers และ rooms
$sql = "
    SELECT 
        booking.id_booking, 
        customers.fullname AS customer_name, 
        rooms.name_rooms AS room_name, 
        booking.booking_date, 
        booking.check_in, 
        booking.check_out 
    FROM 
        booking
    INNER JOIN 
        customers ON booking.id_customers = customers.id_customers
    INNER JOIN 
        rooms ON booking.id_rooms = rooms.id_rooms
    ORDER BY 
        booking.id_booking ASC";
$result = $conn->query($sql);

// ตรวจสอบข้อผิดพลาดของ SQL
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Booking</title>
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
            <h1>Manage Booking</h1>
        </header>
        <!-- Main Content -->
        <main class="main-content flex-1 p-8">
          <h2 class="text-2xl font-bold mb-6 mt-1">Manage Booking</h2>
          <!-- ตาราง -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
              <table class="min-w-full table-auto border-collapse border border-gray-200">
                <thead class="bg-blue-600 text-white">
                  <tr>
                      <th class="border px-4 py-2">Booking ID</th>
                      <th class="border px-4 py-2">Customer Name</th>
                      <th class="border px-4 py-2">Room Name</th>
                      <th class="border px-4 py-2">Check-In</th>
                      <th class="border px-4 py-2">Check-Out</th>
                      <th class="border px-4 py-2">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                  <tr class="bg-white border-b">
                      <td class="border px-4 py-2"><?php echo $row['id_booking']; ?></td>
                      <td class="border px-4 py-2"><?php echo $row['customer_name']; ?></td>
                      <td class="border px-4 py-2"><?php echo $row['room_name']; ?></td>
                      <td class="border px-4 py-2"><?php echo $row['booking_date']; ?></td>
                      <td class="border px-4 py-2"><?php echo $row['check_in']; ?></td>
                      <td class="border px-4 py-2"><?php echo $row['check_out']; ?></td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
        </main>
    </div>
</div>         
<script src="../assets/js/admin.js"></script>
</body>
</html>