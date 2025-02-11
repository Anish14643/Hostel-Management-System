<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'hostel_management';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch rooms from the database
    $stmt = $conn->query("SELECT room_number, status FROM rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rooms as an associative array
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $rooms = []; // Ensure $rooms is an empty array if there's an error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rooms</title>
  <link rel="stylesheet" href="content.css">
</head>
<body>
  <div class="dashboard">
    <div class="sidebar">
      <img src="logo.png" alt="COSY HOSTEL Logo" class="logo">
      <h2>Hostel Management</h2>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="students.php">Students</a></li>
        <li><a href="rooms.php">Rooms</a></li>
        <li><a href="payments.php">Payments</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="management.php">Manage</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <header>
        <h1>Rooms</h1>
        <div class="user-info">
          <span>Welcome, Admin</span>
          <button onclick="logout()">Logout</button>
        </div>
      </header>

      <!-- Rooms Table -->
      <div class="table-section">
        <h2>Room List</h2>
        <table>
          <thead>
            <tr>
              <th>Room Number</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rooms)): ?>
              <?php foreach ($rooms as $room): ?>
                <tr>
                  <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                  <td><?php echo htmlspecialchars($room['status']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="2">No rooms found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function logout() {
      window.location.href = "logout.php";
    }
  </script>
</body>
</html>
