<?php

require_once '../config/config.php';

unset($_SESSION['pending_payment']);

$_SESSION['message'] = 'PayPal demo payment was cancelled.';
$_SESSION['message_type'] = 'error';

redirect('../user/equipment.php');
?>
