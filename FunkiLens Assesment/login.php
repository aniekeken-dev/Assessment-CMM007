<?php

require_once 'config/config.php';


if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $role = sanitize($_POST['role']);
    
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        
        $password_hash = md5($password);
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password_hash' AND role = '$role'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            
            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('user/dashboard.php');
            }
        } else {
            $error = 'Invalid email, password, or role';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loginc - FunkiLens Rentals</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <hi>FunkiLens Rentals</hi>
        <h1>LOGIN</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            
            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
    </div>
</body>
</html>