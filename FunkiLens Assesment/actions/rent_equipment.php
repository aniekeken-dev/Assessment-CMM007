<?php

require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../user/equipment.php');
}

$userId = (int) $_SESSION['user_id'];
$equipmentId = (int) ($_POST['equipment_id'] ?? 0);
$quantity = (int) ($_POST['quantity'] ?? 0);
$rentDate = $_POST['rent_date'] ?? '';
$paymentMethod = clean($_POST['payment_method'] ?? 'paypal_demo');

$equipment = getEquipmentById($equipmentId);

if ($quantity < 1) {
    $_SESSION['message'] = 'Quantity must be at least 1.';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

if (!$equipment) {
    $_SESSION['message'] = 'Equipment not found.';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

if ((int) $equipment['quantity'] < $quantity) {
    $_SESSION['message'] = 'Not enough equipment available.';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

if ($paymentMethod === 'paypal_demo') {
    $_SESSION['pending_payment'] = [
        'user_id' => $userId,
        'equipment_id' => $equipmentId,
        'equipment_name' => $equipment['name'],
        'quantity' => $quantity,
        'rent_date' => $rentDate,
        'due_date' => date('Y-m-d', strtotime($rentDate . ' +7 days')),
        'unit_price' => (float) $equipment['rental_price'],
        'total_amount' => round(((float) $equipment['rental_price']) * $quantity, 2),
        'payment_method' => 'PayPal Demo'
    ];

    redirect('../user/paypal_demo.php');
}

$result = createRentalBooking($userId, $equipmentId, $quantity, $rentDate);
$_SESSION['message'] = $result['message'];
$_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

redirect($result['success'] ? '../user/my_rentals.php' : '../user/equipment.php');
?>
