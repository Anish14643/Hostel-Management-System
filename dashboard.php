<?php
session_start();

// Check if user is logged in (admin or student)
if (!isset($_SESSION['admin']) && !isset($_SESSION['student_id'])) {
    header("Location: admin_login.html"); // Redirect to login if not logged in
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'hostel_management';
$username_db = 'root';
$password_db = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize variables to avoid undefined variable warnings
    $username = 'Guest'; // Default value
    $total_students = 0;
    $occupied_rooms = 0;
    $pending_payments = 0;
    $available_rooms = 0;

    // Fetch user details
    if (isset($_SESSION['admin'])) {
        $user_type = 'Admin';
        $username = 'Admin';

        // Fetch overall statistics for admin
        $total_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $occupied_rooms = $conn->query("SELECT COUNT(*) FROM students WHERE room_number IS NOT NULL")->fetchColumn();
        $total_rooms = 100; // Assuming total rooms are 100
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
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }

    .dashboard {
      display: flex;
    }

    .sidebar {
      width: 250px;
      background-color: #2c3e50;
      color: #fff;
      height: 100vh;
      padding: 20px;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      margin: 20px 0;
    }

    .sidebar ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 18px;
    }

    .sidebar ul li a:hover {
      color: #1abc9c;
    }

    /* Main Content Styles */
    .main-content {
      flex: 1;
      padding: 20px;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    header h1 {
      margin: 0;
    }

    .user-info {
      display: flex;
      align-items: center;
    }

    .user-info span {
      margin-right: 10px;
    }

    .user-info button {
      background-color: #e74c3c;
      color: #fff;
      border: none;
      padding: 8px 16px;
      cursor: pointer;
      border-radius: 4px;
    }

    .user-info button:hover {
      background-color: #c0392b;
    }

    /* Cards Section */
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .card h3 {
      margin: 0 0 10px;
      font-size: 18px;
    }

    .card p {
      margin: 0;
      font-size: 24px;
      font-weight: bold;
      color: #2c3e50;
    }

    /* Table Section */
    .table-section {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .table-section h2 {
      margin-top: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th, table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    table th {
      background-color: #f4f4f4;
    }

    table tr:hover {
      background-color: #f1f1f1;
    }
    /* Logo Styles */
    .logo {
        width: 150px; /* Adjust the size as needed */
        margin-bottom: 20px; /* Space below the logo */
        border-radius: 8px; /* Optional: Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: Add a shadow */
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Add the logo here -->
      <img src="logo.png" alt="COSY HOSTEL Logo" class="logo">
      <h2>Hostel Management</h2>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="students.php">Students</a></li>
        <li><a href="rooms.php">Rooms</a></li>
        <li><a href="payments.php">Payments</a></li>
        <li><a href="reports.php">Reports</a></li>
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
if (isset($_SESSION['admin'])) {
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