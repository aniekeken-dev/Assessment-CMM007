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

// Check if serial number exists
$check = mysqli_query($conn, "SELECT id FROM equipment WHERE serial_number = '$serial_number'");
if (mysqli_num_rows($check) > 0) {
    $_SESSION['message'] = 'Serial number already exists';
    $_SESSION['message_type'] = 'error';
    redirect('../admin/equipment.php');
}

// Insert equipment
$sql = "INSERT INTO equipment (name, category, serial_number, equipment_condition, quantity) 
        VALUES ('$name', '$category', '$serial_number', '$equipment_condition', $quantity)";

if (mysqli_query($conn, $sql)) {
    $_SESSION['message'] = 'Equipment added successfully';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error adding equipment: ' . mysqli_error($conn);
    $_SESSION['message_type'] = 'error';
}

redirect('../admin/equipment.php');
?>