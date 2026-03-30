<?php

require_once '../config/config.php';

// Check if admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin/equipment.php');
}

// Get and sanitize input
$id = (int)$_POST['id'];
$name = sanitize($_POST['name']);
$category = sanitize($_POST['category']);
$serial_number = sanitize($_POST['serial_number']);
$equipment_condition = sanitize($_POST['equipment_condition']);
$quantity = (int)$_POST['quantity'];

// Validate
if (empty($name) || empty($category) || empty($serial_number)) {
    $_SESSION['message'] = 'All fields are required';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/equipment.php');
}

// Check if serial number exists for another equipment
$check = mysqli_query($conn, "SELECT id FROM equipment WHERE serial_number = '$serial_number' AND id != $id");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Serial number already exists';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/equipment.php');
}

// Update equipment
$sql = "UPDATE equipment 
        SET name = '$name', 
            category = '$category', 
            serial_number = '$serial_number', 
            equipment_condition = '$equipment_condition', 
            quantity = $quantity 
        WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = 'Equipment updated successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error updating equipment: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

redirect('../admin/equipment.php');
?>