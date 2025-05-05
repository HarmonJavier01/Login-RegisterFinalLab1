<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Get logged in user ID
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// Get logged in user role
function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

// Logout user
function logout() {
    session_unset();
    session_destroy();
}
?>
