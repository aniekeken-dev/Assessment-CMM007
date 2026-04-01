<?php

require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('../admin/equipment.php');
}

$id = (int) $_GET['id'];
$sql = "DELETE FROM equipment WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = 'Equipment deleted successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error deleting equipment: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

redirect('../admin/equipment.php');
?>
