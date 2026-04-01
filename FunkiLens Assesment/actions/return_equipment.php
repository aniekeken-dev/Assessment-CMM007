<?php

require_once '../config/config.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    redirect('../user/my_rentals.php');
}

$user_id = $_SESSION['user_id'];
$rental_id = (int)$_GET['id'];

// Get rental details
$rental = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT r.*, e.quantity as available_qty 
    FROM rentals r 
    JOIN equipment e ON r.equipment_id = e.id 
    WHERE r.id = $rental_id AND r.user_id = $user_id AND r.status = 'rented'
"));

if (!$rental) {
    $_SESSION['message'] = 'Rental not found or already returned';
    $_SESSION['message_type'] = 'error';
    redirect('../user/my_rentals.php');
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update rental record
    $sql_return = "UPDATE rentals SET status = 'returned' WHERE id = $rental_id";
    mysqli_query($conn, $sql_return);
    
    // Increase equipment quantity
    $new_quantity = $rental['available_qty'] + $rental['quantity'];
    $sql_update = "UPDATE equipment SET quantity = $new_quantity WHERE id = {$rental['equipment_id']}";
    mysqli_query($conn, $sql_update);
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['message'] = 'Equipment returned successfully!';
    $_SESSION['message_type'] = 'success';
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    $_SESSION['message'] = 'Error returning equipment: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

// Redirect based on user role
if (isAdmin()) {
    redirect('../admin/dashboard.php');
} else {
    redirect('../user/my_rentals.php');
}
?>
