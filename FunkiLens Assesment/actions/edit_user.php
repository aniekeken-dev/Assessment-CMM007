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
$id = (int)$_POST['id'];
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$password = $_POST['password'];
$role = sanitize($_POST['role']);

// Validate
if (empty($name) || empty($email)) {
    $_SESSION['message'] = 'Name and email are required';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/users.php');
}

// Check if email exists for another user
$check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $id");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Email already exists';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/users.php');
}

// Build update query
if (!empty($password)) {
    // Update with new password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET name = '$name', email = '$email', password = '$password_hash', role = '$role' WHERE id = $id";
} else {
    // Update without changing password
    $sql = "UPDATE users SET name = '$name', email = '$email', role = '$role' WHERE id = $id";
}

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = 'User updated successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error updating user: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

redirect('../admin/users.php');
?>
