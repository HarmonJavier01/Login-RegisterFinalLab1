<?php
session_start();

require_once 'db.php';

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check user role
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_teacher() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'teacher';
}

function is_user() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

// Redirect to login if not logged in
function ensure_logged_in() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Redirect to dashboard based on role
function redirect_dashboard() {
    if (is_admin()) {
        header('Location: admin/dashboard.php');
        exit;
    } elseif (is_teacher()) {
        header('Location: teacher/dashboard.php');
        exit;
    } elseif (is_user()) {
        header('Location: user/dashboard.php');
        exit;
    } else {
        header('Location: login.php');
        exit;
    }
}

// Sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Log actions
function log_action($pdo, $user_id, $action) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $ip]);
}

// Generate random verification code
function generate_verification_code() {
    return bin2hex(random_bytes(16));
}
?>
