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
?> 