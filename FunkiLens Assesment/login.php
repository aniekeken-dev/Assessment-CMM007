<?php

require_once 'config/config.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'user/userdashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = clean($_POST['role'] ?? '');

    if ($email === '' || $password === '' || $role === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, name, email, password, role FROM users WHERE email = ? AND role = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'ss', $email, $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        $passwordMatches = false;
        if ($user) {
            $passwordMatches = password_verify($password, $user['password']) || md5($password) === $user['password'] || $password === $user['password'];
        }

        if ($passwordMatches) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/userdashboard.php');
        }

        $error = 'Invalid email, password, or role.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FunkiLens Rentals</title>
    <link rel="icon" type="image/jpeg" href="assets/logo.jpg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-brand">
            <img src="assets/logo.jpg" alt="FunkiLens Rentals logo" class="auth-logo">
            <h1>FunkiLens Rentals</h1>
            <p>Sign in to manage equipment, track rentals, and keep everything moving smoothly.</p>
        </div>

        <div class="form-card">
            <h2 style="margin-bottom: 1.25rem;">Login</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Select role</option>
                        <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?php echo (($_POST['role'] ?? '') === 'user') ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Login</button>
            </form>
        </div>

        <div class="auth-footer">
            Don&apos;t have an account?
            <a href="register.php">Create one here</a>
        </div>
    </div>
</div>
</body>
</html>
