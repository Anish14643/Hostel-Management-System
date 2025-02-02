<?php
// Database connection
$host = 'localhost';
$dbname = 'hostel_management';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $student_id = $_POST['student_id'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO students (full_name, email, student_id, password) VALUES (:full_name, :email, :student_id, :password)");
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    echo "<script>alert('Registration successful!'); window.location.href = 'student_login.html';</script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>