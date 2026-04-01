<?php

require_once '../config/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pending = $_SESSION['pending_payment'] ?? null;

if (!$pending) {
    $_SESSION['message'] = 'Start a rental first before opening the PayPal demo page.';
    $_SESSION['message_type'] = 'error';
    redirect('equipment.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Demo Checkout - FunkiLens Rentals</title>
    <link rel="icon" type="image/jpeg" href="../assets/logo.jpg">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="brand-lockup">
                <img src="../assets/logo.jpg" alt="FunkiLens Rentals logo">
                <div class="brand-text">
                    <h1>FunkiLens Rentals</h1>
                    <small>Rentals</small>
                </div>
            </div>
            <nav class="nav">
                <a href="userdashboard.php">Dashboard</a>
                <a href="equipment.php" class="active">Browse Equipment</a>
                <a href="my_rentals.php">My Rentals</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="payment-shell">
            <div class="payment-brand">
                <span class="payment-badge">Prototype Checkout</span>
                <h2>Pay with PayPal</h2>
                <p>Review your order details below and continue to PayPal to complete your checkout securely.</p>

                <div class="payment-summary">
                    <div class="price-line">
                        <span>Equipment</span>
                        <strong><?php echo htmlspecialchars($pending['equipment_name']); ?></strong>
                    </div>
                    <div class="price-line">
                        <span>Quantity</span>
                        <strong><?php echo (int) $pending['quantity']; ?></strong>
                    </div>
                    <div class="price-line">
                        <span>Unit Price</span>
                        <strong>$<?php echo number_format((float) $pending['unit_price'], 2); ?></strong>
                    </div>
                    <div class="price-line">
                        <span>Rent Date</span>
                        <strong><?php echo htmlspecialchars($pending['rent_date']); ?></strong>
                    </div>
                    <div class="price-line">
                        <span>Due Date</span>
                        <strong><?php echo htmlspecialchars($pending['due_date']); ?></strong>
                    </div>
                    <div class="price-line total-line">
                        <span>Total</span>
                        <strong>$<?php echo number_format((float) $pending['total_amount'], 2); ?></strong>
                    </div>
                </div>
            </div>

            <div class="payment-panel">
                <div class="paypal-mark">
                    <img src="../assets/logo.jpg" alt="FunkiLens Rentals logo">
                    <span>PayPal</span>
                </div>
                <h2>Complete Payment</h2>
                <p>Log in to your PayPal account to confirm this payment and finish your equipment booking.</p>

                <form action="../actions/complete_paypal_payment.php" method="POST">
                    <div class="form-group">
                        <label for="paypal_email">PayPal Email</label>
                        <input type="email" id="paypal_email" name="paypal_email" placeholder="name@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="paypal_name">Account Name</label>
                        <input type="text" id="paypal_name" name="paypal_name" placeholder="Full name">
                    </div>

                    <div class="form-group">
                        <label for="paypal_note">Checkout Status</label>
                        <input type="text" id="paypal_note" value="Sandbox checkout environment" readonly>
                    </div>

                    <button type="submit" class="btn btn-paypal btn-full">Pay $<?php echo number_format((float) $pending['total_amount'], 2); ?> with PayPal</button>
                </form>

                <a href="../actions/cancel_paypal_payment.php" class="btn btn-secondary btn-full" style="margin-top: 0.85rem;">Cancel</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> FunkiLens Rentals</p>
    </div>
</body>
</html>
