<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header('Location: User_Login.php');
    exit();
}

$customer_name = $_SESSION['customer_name'];
$customer_id = $_SESSION['customer_id'];
include '../includes/db_connect.php';

$stmt = $conn->prepare("SELECT b.id_booking, r.name_rooms, b.booking_date, b.check_in, b.check_out 
    FROM booking b
    JOIN rooms r ON b.id_rooms = r.id_rooms
    WHERE b.id_customers = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <!-- User CSS -->
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body class="bg-white text-gray-800">

<!-- Navbar -->
<nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="../index.php" class="text-3xl font-bold">GotJung Hotel</a>
        <div class="relative inline-block">
        <button class="dropdown-button bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Menu
        </button>
        <ul class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded hidden">
            <li class="p-2 hover:bg-gray-100 text-black"><a href="profile.php">Profile</a></li>
            <li class="p-2 hover:bg-gray-100 text-black"><a href="edit_profile.php">Edit Profile</a></li>
            <li class="p-2 hover:bg-gray-100 text-black"><a href="review.php">Review</a></li>
            <li class="p-2 hover:bg-gray-100 text-red-500"><a href="logout.php">Logout</a></li>
        </ul>
        </div>
    </div>
</nav>

<!-- Profile Section -->
<main class="container mx-auto my-8 p-4 bg-white shadow-md rounded">
    <h1 class="text-2xl font-bold mb-4">Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h1>
    <p class="text-gray-700">Your ID: <?php echo htmlspecialchars($customer_id); ?></p>

    <!-- Booking History -->
    <section class="mt-8">
        <h2 class="text-xl font-semibold mb-4">Booking History</h2>
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-left border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">Booking ID</th>
                        <th class="border border-gray-300 px-4 py-2">Room Name</th>
                        <th class="border border-gray-300 px-4 py-2">Booking Date</th>
                        <th class="border border-gray-300 px-4 py-2">Check-in</th>
                        <th class="border border-gray-300 px-4 py-2">Check-out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['id_booking']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['name_rooms']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['check_in']); ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($row['check_out']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">No booking history found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-blue-600 text-white text-center py-4">
    <p>&copy; 2025 Hotel Booking. All rights reserved.</p>
</footer>
<script src="../assets/js/user.js"></script>
</body>
</html>
