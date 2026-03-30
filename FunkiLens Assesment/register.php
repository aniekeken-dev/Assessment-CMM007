<?php

require_once 'config/db.php';


if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'user/userdashboard.php');
}

$error   = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and clean each form field
    $name     = clean($_POST['name']     ?? '');
    $email    = clean($_POST['email']    ?? '');
    $password = $_POST['password']       ?? '';  // raw – will be hashed
    $confirm  = $_POST['confirm']        ?? '';

    
    if (!$name || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';

    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';

    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match. Please try again.';

    } else {
        // Check if the email is already registered
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'That email address is already registered. Please log in instead.';
        } else {
            // Hash the password securely before saving
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user (always role = 'user')
            $insert = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            mysqli_stmt_bind_param($insert, 'sss', $name, $email, $hashed);

            if (mysqli_stmt_execute($insert)) {
                $success = 'Account created successfully! You can now log in.';
            } else {
                $error = 'Something went wrong. Please try again later.';
            }
            mysqli_stmt_close($insert);
        }
        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Funkilens Rentals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-wrapper">

        
        
        <div class="auth-brand">
            <h1> Funkilens Rentals</h1>
            <p>Create your account to start renting equipment</p>
        </div>

        
        <div class="form-card">
            <h2 style="margin-bottom:22px;font-size:1.3rem;">Create Account</h2>

            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <br><a href="login.php">→ Go to Login</a>
                </div>
            <?php endif; ?>

            <?php if (!$success): // Hide form after successful registration ?>
            <form method="POST" action="">

                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           placeholder="Your full name" required
                           value="<?php echo clean($_POST['name'] ?? ''); ?>">
                </div>

                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           placeholder="you@example.com" required
                           value="<?php echo clean($_POST['email'] ?? ''); ?>">
                </div>

                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Min. 6 characters" required>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm">Confirm Password</label>
                    <input type="password" id="confirm" name="confirm"
                           placeholder="Repeat your password" required>
                </div>

                
                <p id="match-msg" style="font-size:0.83rem;margin-bottom:12px;display:none;"></p>

                <button type="submit" class="btn btn-primary btn-full" style="margin-top:4px;">
                    Create Account
                </button>

            </form>
            <?php endif; ?>
        </div>

        <div class="auth-footer">
            Already have an account?
            <a href="login.php">Sign in here</a>
        </div>

    </div>
</div>

<script>
    
    const pwd     = document.getElementById('password');
    const confirm = document.getElementById('confirm');
    const msg     = document.getElementById('match-msg');

    function checkMatch() {
        if (!confirm.value) { msg.style.display = 'none'; return; }
        msg.style.display = 'block';
        if (pwd.value === confirm.value) {
            msg.textContent  = '✔ Passwords match';
            msg.style.color  = '#28a745';
        } else {
            msg.textContent  = '✖ Passwords do not match';
            msg.style.color  = '#dc3545';
        }
    }

    pwd.addEventListener('input', checkMatch);
    confirm.addEventListener('input', checkMatch);
</script>

</body>
</html>