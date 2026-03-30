<?php

require_once '../config/config.php';

// Check if admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/users.php');
}

// Get and sanitize input
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$password = $_POST['password'];
$role = sanitize($_POST['role']);

// Validate
if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['message'] = 'All fields are required';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/users.php');
}

// Check if email exists
$check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Email already exists';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/users.php');
}

// Hash password (using MD5 for simplicity in this student project)
$password_hash = md5($password);

// Insert user
$sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password_hash', '$role')";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = 'User added successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error adding user: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

redirect('../admin/users.php');
?>