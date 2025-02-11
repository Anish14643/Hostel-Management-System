<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
    exit();
}

// Database connection
$host = 'localhost'; // Replace with your host if different
$dbname = 'hostel_management'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch payments from the database
    $stmt = $conn->query("SELECT * FROM payments");
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all payments as an associative array
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $payments = []; // Ensure $payments is an empty array if there's an error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments</title>
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
        <h1>Payments</h1>
        <div class="user-info">
          <span>Welcome, Admin</span>
          <button onclick="logout()">Logout</button>
        </div>
      </header>

      <!-- Payments Table -->
      <div class="table-section">
        <h2>Payment List</h2>
        <table>
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Payment Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($payments)): ?>
              <?php foreach ($payments as $payment): ?>
                <tr>
                  <td><?php echo htmlspecialchars($payment['student_id']); ?></td>
                  <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                  <td><?php echo htmlspecialchars($payment['status']); ?></td>
                  <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" style="text-align: center;">No payments found.</td>
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
