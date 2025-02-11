<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'hostel_management';
$username_db = 'root';
$password_db = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user details
    if (isset($_SESSION['admin'])) {
        $user_type = 'Admin';
        $username = 'Admin';

        $total_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $occupied_rooms = $conn->query("SELECT COUNT(*) FROM students WHERE room_number IS NOT NULL")->fetchColumn();
        $total_rooms = 100; 
        $available_rooms = $total_rooms - $occupied_rooms;
        $pending_payments = $conn->query("SELECT COUNT(*) FROM students WHERE payment_status = 'Pending'")->fetchColumn();
    } else {
        $user_type = 'Student';
        $student_id = $_SESSION['student_id'];
        $stmt = $conn->prepare("SELECT full_name, room_number, payment_status FROM students WHERE id = :student_id");
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($student) {
            $username = $student['full_name'];
            $room_number = $student['room_number'];
            $payment_status = $student['payment_status'];
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hostel Management System Dashboard</title>
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
        <h1>Dashboard</h1>
        <div class="user-info">
          <span>Welcome, <?php echo $username; ?></span>
          <button onclick="logout()">Logout</button>
        </div>
      </header>

      <!-- Cards Section -->
      <div class="cards">
        <?php if (isset($_SESSION['admin'])): ?>
          <div class="card">
            <h3>Total Students</h3>
            <p><?php echo $total_students; ?></p>
          </div>
          <div class="card">
            <h3>Occupied Rooms</h3>
            <p><?php echo $occupied_rooms; ?></p>
          </div>
          <div class="card">
            <h3>Pending Payments</h3>
            <p><?php echo $pending_payments; ?></p>
          </div>
          <div class="card">
            <h3>Available Rooms</h3>
            <p><?php echo $available_rooms; ?></p>
          </div>
        <?php else: ?>
          <div class="card">
            <h3>Your Room Number</h3>
            <p><?php echo $room_number ?? 'Not Assigned'; ?></p>
          </div>
          <div class="card">
            <h3>Payment Status</h3>
            <p><?php echo $payment_status ?? 'Pending'; ?></p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Table Section -->
      <div class="table-section">
        <h2>Recent Activities</h2>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Student Name</th>
              <th>Room No</th>
              <th>Payment Status</th>
            </tr>
          </thead>
          <tbody>
          <?php
// Fetch recent activities for admin
{
    $stmt = $conn->query("SELECT id, full_name, room_number, payment_status FROM students ORDER BY id DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['room_number']}</td>
                <td>{$row['payment_status']}</td>
              </tr>";
    }
}
?>
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
