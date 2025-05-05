<?php
require_once 'session.php';
require_once '../config/constants.php';

// Require login for protected pages
function require_login() {
    if (!is_logged_in()) {
        redirect('../login.php');
    }
}

// Require specific role to access page
function require_role($role) {
    require_login();
    if (get_user_role() !== $role) {
        // Optionally, redirect to unauthorized page or dashboard
        redirect('../login.php');
    }
}

// Check if current user has a role (or roles)
function has_role($roles) {
    if (!is_logged_in()) {
        return false;
    }
    $user_role = get_user_role();
    if (is_array($roles)) {
        return in_array($user_role, $roles);
    }
    return $user_role === $roles;
}
?>
