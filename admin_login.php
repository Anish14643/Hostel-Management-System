<?php
session_start();
$admin_username = 'admin';
$admin_password = 'admin123';

$username = $_POST['username'];
$password = $_POST['password'];

if ($username === $admin_username && $password === $admin_password) {
    $_SESSION['admin'] = true;
    header("Location: dashboard.php");
    exit();
} else {
    echo "<script>alert('Invalid username or password.'); window.location.href = 'admin_login.html';</script>";
}
?>
