<?php

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       
define('DB_PASS', '');            
define('DB_NAME', 'funkilens_db');


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}


function redirect($url) {
    header("Location: $url");
    exit();
}


function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

function clean($data) {
    return sanitize($data);
}

function getEquipmentById($equipmentId) {
    global $conn;

    $equipmentId = (int) $equipmentId;
    $result = mysqli_query($conn, "SELECT * FROM equipment WHERE id = $equipmentId LIMIT 1");

    return $result ? mysqli_fetch_assoc($result) : null;
}

function createRentalBooking($userId, $equipmentId, $quantity, $rentDate) {
    global $conn;

    $userId = (int) $userId;
    $equipmentId = (int) $equipmentId;
    $quantity = (int) $quantity;
    $rentDate = sanitize($rentDate);

    if ($quantity < 1) {
        return ['success' => false, 'message' => 'Quantity must be at least 1.'];
    }

    $equipment = getEquipmentById($equipmentId);
    if (!$equipment) {
        return ['success' => false, 'message' => 'Equipment not found.'];
    }

    if ((int) $equipment['quantity'] < $quantity) {
        return ['success' => false, 'message' => 'Not enough equipment available.'];
    }

    $dueDate = date('Y-m-d', strtotime($rentDate . ' +7 days'));

    mysqli_begin_transaction($conn);

    try {
        $insertRental = "INSERT INTO rentals (user_id, equipment_id, quantity, rent_date, due_date, status)
                         VALUES ($userId, $equipmentId, $quantity, '$rentDate', '$dueDate', 'rented')";

        if (!mysqli_query($conn, $insertRental)) {
            throw new Exception(mysqli_error($conn));
        }

        $newQuantity = (int) $equipment['quantity'] - $quantity;
        $updateEquipment = "UPDATE equipment SET quantity = $newQuantity WHERE id = $equipmentId";

        if (!mysqli_query($conn, $updateEquipment)) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);

        return ['success' => true, 'message' => 'Equipment rented successfully!'];
    } catch (Exception $exception) {
        mysqli_rollback($conn);

        return ['success' => false, 'message' => 'Error renting equipment: ' . $exception->getMessage()];
    }
}
?>
