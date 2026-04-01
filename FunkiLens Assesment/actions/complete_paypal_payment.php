<?php

require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../user/equipment.php');
}

$pending = $_SESSION['pending_payment'] ?? null;

if (!$pending) {
    $_SESSION['message'] = 'No pending PayPal demo payment was found.';
    $_SESSION['message_type'] = 'error';
    redirect('../user/equipment.php');
}

$paypalEmail = clean($_POST['paypal_email'] ?? '');

if ($paypalEmail === '' || !filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = 'Enter a valid PayPal demo email to continue.';
    $_SESSION['message_type'] = 'error';
    redirect('../user/paypal_demo.php');
}

$result = createRentalBooking(
    (int) $pending['user_id'],
    (int) $pending['equipment_id'],
    (int) $pending['quantity'],
    $pending['rent_date']
);

unset($_SESSION['pending_payment']);

$_SESSION['message'] = $result['success']
    ? 'Demo PayPal payment approved for ' . htmlspecialchars($paypalEmail) . '. Rental created successfully.'
    : $result['message'];
$_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

redirect($result['success'] ? '../user/my_rentals.php' : '../user/equipment.php');
?>
