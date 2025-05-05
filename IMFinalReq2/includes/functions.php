<?php
// Common helper functions

// Sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Generate random verification code
function generate_verification_code($length = 20) {
    return bin2hex(random_bytes($length));
}

// Redirect to a given URL
function redirect($url) {
    header("Location: $url");
    exit;
}

// Get client IP address
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
?>
