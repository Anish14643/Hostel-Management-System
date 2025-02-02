<?php
session_start();

// Hardcoded admin credentials
$admin_username = 'admin';
$admin_password = 'admin123';

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

if ($username === $admin_username && $password === $admin_password) {
    // Login successful
    $_SESSION['admin'] = true;
    header("Location: dashboard.php"); // Redirect to dashboard
    exit();
} else {
    echo "<script>alert('Invalid username or password.'); window.location.href = 'admin_login.html';</script>";
}
?>