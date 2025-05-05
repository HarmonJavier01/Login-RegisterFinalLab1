<?php
require_once 'functions.php';

$code = $_GET['code'] ?? '';

if ($code) {
    $stmt = $pdo->prepare("SELECT id, is_email_verified FROM users WHERE verification_code = ?");
    $stmt->execute([$code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_email_verified']) {
            $message = "Your email is already verified.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET is_email_verified = 1, verification_code = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);
            $message = "Email verified successfully! You can now login.";
            log_action($pdo, $user['id'], 'Email verified');
        }
    } else {
        $message = "Invalid verification code.";
    }
} else {
    $message = "No verification code provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification - Multi User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Email Verification</h2>
    <div class="alert alert-info"><?=htmlspecialchars($message)?></div>
    <a href="login.php" class="btn btn-primary">Login</a>
</div>
</body>
</html>
