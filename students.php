<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.html");
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

    // Initialize $students as an empty array
    $students = [];

    // Fetch students from the database
    $stmt = $conn->query("SELECT * FROM students");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle adding a new student
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $student_id = $_POST['student_id'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO students (full_name, email, student_id, password) VALUES (:full_name, :email, :student_id, :password)");
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Refresh the page to show the updated student list
        header("Location: students.php");
        exit();
    }

    // Handle updating a student's room and payment status
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
        $id = $_POST['id'];
        $room_number = $_POST['room_number'];
        $payment_status = $_POST['payment_status'];

        $stmt = $conn->prepare("UPDATE students SET room_number = :room_number, payment_status = :payment_status WHERE id = :id");
        $stmt->bindParam(':room_number', $room_number);
        $stmt->bindParam(':payment_status', $payment_status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Refresh the page to show the updated student list
        header("Location: students.php");
        exit();
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
  <title>Students</title>
  <style>
    /* Add your existing CSS styles here */
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

    /* Edit Form Styles */
    .edit-form {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .edit-form h2 {
      margin-top: 0;
    }

    .edit-form input, .edit-form select {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .edit-form button {
      background-color: #2c3e50;
      color: #fff;
      border: none;
      padding: 8px 16px;
      cursor: pointer;
      border-radius: 4px;
    }

    .edit-form button:hover {
      background-color: #1abc9c;
    }

    /* Overlay Styles */
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

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
        <h1>Students</h1>
        <div class="user-info">
          <span>Welcome, Admin</span>
          <button onclick="logout()">Logout</button>
        </div>
      </header>

      <!-- Add Student Form -->
      <div class="form-container">
        <h2>Add New Student</h2>
        <form method="POST">
          <input type="text" name="full_name" placeholder="Full Name" required>
          <input type="email" name="email" placeholder="Email" required>
          <input type="text" name="student_id" placeholder="Student ID" required>
          <input type="password" name="password" placeholder="Password" required>
          <button type="submit" name="add_student">Add Student</button>
        </form>
      </div>

      <!-- Students Table -->
      <div class="table-section">
        <h2>Student List</h2>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Email</th>
              <th>Student ID</th>
              <th>Room Number</th>
              <th>Payment Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($students)): ?>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?php echo $student['id']; ?></td>
                  <td><?php echo $student['full_name']; ?></td>
                  <td><?php echo $student['email']; ?></td>
                  <td><?php echo $student['student_id']; ?></td>
                  <td><?php echo $student['room_number'] ?? 'Not Assigned'; ?></td>
                  <td><?php echo $student['payment_status'] ?? 'Pending'; ?></td>
                  <td>
                    <button onclick="openEditForm(<?php echo $student['id']; ?>, '<?php echo $student['room_number'] ?? ''; ?>', '<?php echo $student['payment_status'] ?? ''; ?>')">Edit</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center;">No students found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit Form -->
  <div class="overlay" id="overlay"></div>
  <div class="edit-form" id="editForm">
    <h2>Edit Student</h2>
    <form method="POST">
      <input type="hidden" name="id" id="editId">
      <input type="text" name="room_number" id="editRoomNumber" placeholder="Room Number">
      <select name="payment_status" id="editPaymentStatus">
        <option value="Pending">Pending</option>
        <option value="Paid">Paid</option>
      </select>
      <button type="submit" name="update_student">Update</button>
      <button type="button" onclick="closeEditForm()">Cancel</button>
    </form>
  </div>

  <script>
    function logout() {
      window.location.href = "logout.php";
    }

    function openEditForm(id, roomNumber, paymentStatus) {
      document.getElementById('editId').value = id;
      document.getElementById('editRoomNumber').value = roomNumber;
      document.getElementById('editPaymentStatus').value = paymentStatus;
      document.getElementById('editForm').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
    }

    function closeEditForm() {
      document.getElementById('editForm').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }
  </script>
</body>
</html>