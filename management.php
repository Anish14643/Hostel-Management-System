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
        header("Location: management.php");
        exit();
    }

    // Handle deleting a student
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Refresh the page to show the updated student list
        header("Location: management.php");
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
              <th>Actions</th>
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
                    <button class="delete" onclick="confirmDelete(<?php echo $student['id']; ?>)">Delete</button>
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

  <!-- Hidden Delete Form -->
  <form method="POST" id="deleteForm">
    <input type="hidden" name="id" id="deleteId">
    <input type="hidden" name="delete_student" value="1">
  </form>

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

    function confirmDelete(id) {
      if (confirm("Are you sure you want to delete this student?")) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
      }
    }
  </script>
</body>
</html>
