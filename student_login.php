<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'hostel_management';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    // Fetch student from database
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['password'])) {
        // Login successful
        $_SESSION['student_id'] = $student['id'];
        header("Location: dashboard.php"); 
        exit();
    } else {
        echo "<script>alert('Invalid student ID or password.'); window.location.href = 'student_login.html';</script>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
