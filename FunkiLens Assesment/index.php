<?php

require_once 'config/config.php';

// If not logged in, redirect to login page
if (!isLoggedIn()) {
    redirect('login.php');
}

// If logged in, redirect to appropriate dashboard
if (isAdmin()) {
    redirect('admin/dashboard.php');
} else {
    redirect('user/userdashboard.php');
}
?>
