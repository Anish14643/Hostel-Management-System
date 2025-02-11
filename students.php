<?php
session_start();

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
        <h1>Students</h1>
        <div class="user-info">
        <span>Welcome, Admin</span>
          <button onclick="logout()">Logout</button>
        </div>
      </header>

         <!-- Add Student Form -->
         <div class="form-container">
        <h2>Add New Student</h2>
        <form method="POST" class="vertical-form">
          <label for="full_name">Full Name</label>
          <input type="text" name="full_name" id="full_name" placeholder="Full Name" required>

          <label for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="Email" required>

          <label for="student_id">Student ID</label>
          <input type="text" name="student_id" id="student_id" placeholder="Student ID" required>

          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Password" required>

          <button type="submit" name="add_student">Add Student</button>
        </form>
      </div>

  <script>
    function logout() {
      window.location.href = "logout.php";
    }
  </script>
</html>
