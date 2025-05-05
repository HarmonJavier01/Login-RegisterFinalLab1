<?php
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function logActivity($user_id, $action, $description, $ip_address) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $description, $ip_address]);
}

function exportToCSV($data, $filename = 'export.csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add headers
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
    }
    
    // Add data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user info from session
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? ''
    ];
}

// Logout user by destroying session
function logoutUser() {
    if (isLoggedIn()) {
        session_unset();
        session_destroy();
        return true;
    }
    return false;
}
