<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports</title>
  <link rel="stylesheet" href="content.css">
</head>
<body>
  <div class="dashboard">
    <!-- Sidebar -->
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
        <h1>Reports</h1>
        <div class="user-info">
          <span>Welcome, Admin</span>
          <button onclick="logout()">Logout</button>
        </div>
      </header>

      <!-- Reports Section -->
      <div class="table-section">
        <h2>Occupancy Report</h2>
        <table>
          <thead>
            <tr>
              <th>Total Rooms</th>
              <th>Occupied Rooms</th>
              <th>Available Rooms</th>
              <th>Occupancy Rate</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Fetch occupancy data from the database
            $host = 'localhost';
            $dbname = 'hostel_management';
            $username = 'root';
            $password = '';

            try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Total rooms
                $stmt = $conn->query("SELECT COUNT(*) AS total_rooms FROM rooms");
                $total_rooms = $stmt->fetch(PDO::FETCH_ASSOC)['total_rooms'];

                // Occupied rooms
                $stmt = $conn->query("SELECT COUNT(*) AS occupied_rooms FROM rooms WHERE status = 'Occupied'");
                $occupied_rooms = $stmt->fetch(PDO::FETCH_ASSOC)['occupied_rooms'];

                // Available rooms
                $available_rooms = $total_rooms - $occupied_rooms;

                // Occupancy rate (handle division by zero)
                if ($total_rooms > 0) {
                    $occupancy_rate = ($occupied_rooms / $total_rooms) * 100;
                } else {
                    $occupancy_rate = 0; // Set occupancy rate to 0 if no rooms exist
                }

                echo "<tr>
                        <td>{$total_rooms}</td>
                        <td>{$occupied_rooms}</td>
                        <td>{$available_rooms}</td>
                        <td>{$occupancy_rate}%</td>
                      </tr>";
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Error fetching data: " . $e->getMessage() . "</td></tr>";
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
