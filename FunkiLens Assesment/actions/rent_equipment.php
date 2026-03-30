<?php

require_once '../config/config.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../user/equipment.php');
}

// Get input
$user_id = $_SESSION['user_id'];
$equipment_id = (int)$_POST['equipment_id'];
$quantity = (int)$_POST['quantity'];
$rent_date = sanitize($_POST['rent_date']);

// Validate
if ($quantity < 1) {
    $_SESSION['message'] = 'Quantity must be at least 1';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

// Check equipment availability
$equipment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM equipment WHERE id = $equipment_id"));

if (!$equipment) {
    $_SESSION['message'] = 'Equipment not found';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

if ($equipment['quantity'] < $quantity) {
    $_SESSION['message'] = 'Not enough equipment available';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert rental record
    $sql_rental = "INSERT INTO rentals (user_id, equipment_id, quantity, rent_date, status) 
                   VALUES ($user_id, $equipment_id, $quantity, '$rent_date', 'rented')";
    mysqli_query($conn, $sql_rental);
    
    // Decrease equipment quantity
    $new_quantity = $equipment['quantity'] - $quantity;
    $sql_update = "UPDATE equipment SET quantity = $new_quantity WHERE id = $equipment_id";
    mysqli_query($conn, $sql_update);
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['message'] = 'Equipment rented successfully!';
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    $_SESSION['message'] = 'Error renting equipment: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

redirect('../user/my_rentals.php');
?>