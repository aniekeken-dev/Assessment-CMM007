<?php

require_once '../config/config.php';

// Check if admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    redirect('../admin/users.php');
}

$id = (int)$_GET['id'];

// Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    $_SESSION['message'] = 'You cannot delete your own account';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/users.php');
}

// Delete user 
$sql = "DELETE FROM users WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = 'User deleted successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error deleting user: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

redirect('../admin/users.php');
?>