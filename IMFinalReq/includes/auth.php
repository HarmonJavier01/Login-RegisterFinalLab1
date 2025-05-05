<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

function isTeacher() {
    return isLoggedIn() && $_SESSION['user_role'] === 'teacher';
}

function isUser() {
    return isLoggedIn() && $_SESSION['user_role'] === 'user';
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function redirectBasedOnRole() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header("Location: ../admin/dashboard.php");
        } elseif (isTeacher()) {
            header("Location: ../teacher/dashboard.php");
        } else {
            header("Location: ../user/dashboard.php");
        }
        exit();
    }
}

function generateVerificationCode() {
    return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
}
?>