<?php
session_start();

// Check if admin is logged in
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