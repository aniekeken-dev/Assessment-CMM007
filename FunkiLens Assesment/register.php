<?php

require_once 'config/config.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'user/userdashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $check = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'That email address is already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            mysqli_stmt_bind_param($insert, 'sss', $name, $email, $hashed);

            if (mysqli_stmt_execute($insert)) {
                $success = 'Account created successfully. You can now log in.';
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
    <title>Register - FunkiLens Rentals</title>
    <link rel="icon" type="image/jpeg" href="assets/logo.jpg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-brand">
            <img src="assets/logo.jpg" alt="FunkiLens Rentals logo" class="auth-logo">
            <h1>FunkiLens Rentals</h1>
            <p>Create your account and start renting cameras, lenses, drones, and studio gear in a clean dashboard.</p>
        </div>

        <div class="form-card">
            <h2 style="margin-bottom: 1.25rem;">Create Account</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <br>
                    <a href="login.php">Go to login</a>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required placeholder="Your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Minimum 6 characters">
                    </div>

                    <div class="form-group">
                        <label for="confirm">Confirm Password</label>
                        <input type="password" id="confirm" name="confirm" required placeholder="Repeat your password">
                    </div>

                    <p id="match-msg" style="font-size: 0.88rem; margin-bottom: 1rem; display: none;"></p>

                    <button type="submit" class="btn btn-primary btn-full">Create Account</button>
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
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm');
const matchMessage = document.getElementById('match-msg');

function checkMatch() {
    if (!passwordInput || !confirmInput || !matchMessage) {
        return;
    }

    if (!confirmInput.value) {
        matchMessage.style.display = 'none';
        return;
    }

    matchMessage.style.display = 'block';

    if (passwordInput.value === confirmInput.value) {
        matchMessage.textContent = 'Passwords match';
        matchMessage.style.color = '#15803d';
    } else {
        matchMessage.textContent = 'Passwords do not match';
        matchMessage.style.color = '#dc2626';
    }
}

if (passwordInput && confirmInput) {
    passwordInput.addEventListener('input', checkMatch);
    confirmInput.addEventListener('input', checkMatch);
}
</script>
</body>
</html>
