<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

include '../includes/db_connect.php';

// ดึงข้อมูลจากตาราง booking พร้อมเชื่อมกับ customers และ rooms
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "
    SELECT 
        bookings.id_booking, 
        customers.fullname AS customer_name, 
        rooms.name_rooms AS room_name, 
        bookings.booking_date, 
        bookings.check_in, 
        bookings.check_out, 
        bookings.payment_status, 
        bookings.payment_slip 
    FROM 
        bookings
    INNER JOIN 
        customers ON bookings.id_customers = customers.id_customers
    INNER JOIN 
        rooms ON bookings.id_rooms = rooms.id_rooms
    WHERE 
        customers.fullname LIKE '%$search%' 
        OR rooms.name_rooms LIKE '%$search%'
    ORDER BY 
        bookings.id_booking ASC";
$result = $conn->query($sql);

// ตรวจสอบข้อผิดพลาดของ SQL
if (!$result) {
    die("Query failed: " . $conn->error);
}

// จัดการอัปเดตสถานะการชำระเงิน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_id'])) {
    $id = intval($_POST['update_payment_id']);
    $update_query = "UPDATE bookings SET payment_status = 'paid' WHERE id_booking = $id";
    if ($conn->query($update_query)) {
        echo "success";
    } else {
        echo "error";
    }
    exit();
}

// จัดการลบข้อมูลการจอง
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $delete_id = intval($_POST['delete_booking_id']); // ตรวจสอบและแปลงค่าเป็นตัวเลข
    $query = "DELETE FROM bookings WHERE id_booking = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $delete_id); // ผูกค่าตัวแปร
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
    exit();
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="logo">Gotjung Hotel</div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_services.php"><i class="fas fa-concierge-bell"></i> Manage Service</a>
            <a href="manage_bookings.php"><i class="fas fa-calendar-check"></i> Manage Booking</a>
        </nav>
        <div class="footer">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <button id="toggleButton">☰</button>
            <h1>Manage Booking</h1>
        </header>
        <main class="main-content flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6 mt-1">Manage Booking</h2>

            <!-- Search Form -->
            <form method="GET" class="mb-4">
                <input type="text" name="search" placeholder="Search by Customer Name or Room Name" class="border rounded p-2 w-full">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">Search</button>
            </form>

            <!-- Booking Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full table-auto border-collapse border border-gray-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <th class="border px-4 py-2">Booking ID</th>
                            <th class="border px-4 py-2">Customer Name</th>
                            <th class="border px-4 py-2">Room Name</th>
                            <th class="border px-4 py-2">Booking Date</th>
                            <th class="border px-4 py-2">Check-In</th>
                            <th class="border px-4 py-2">Check-Out</th>
                            <th class="border px-4 py-2">Payment Status</th>
                            <th class="border px-4 py-2">Payment Slip</th>
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
                            <td class="border px-4 py-2"><?php echo ucfirst($row['payment_status']); ?></td>
                            <td class="border px-4 py-2">
                                <?php if ($row['payment_slip']): ?>
                                    <a href="../uploads/<?php echo $row['payment_slip']; ?>" 
                                    target="_blank" 
                                    class="text-blue-500 hover:underline">
                                    View Slip
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500">No Slip Uploaded</span>
                                <?php endif; ?>
                            </td>
                            <td class="border px-4 py-2">
                                <button onclick="updatePaymentStatus(<?php echo $row['id_booking']; ?>)" 
                                        class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">
                                    Mark as Paid
                                </button>
                                <button onclick="deleteBooking(<?php echo $row['id_booking']; ?>)" 
                                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
