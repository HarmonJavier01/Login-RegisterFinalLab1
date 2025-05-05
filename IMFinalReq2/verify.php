<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$success = '';
$error = '';

if (isset($_GET['code'])) {
    $code = sanitize_input($_GET['code']);

    // Find user with this verification code
    $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE verification_code = ?");
    $stmt->execute([$code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_verified']) {
            $error = "Your email is already verified.";
        } else {
            // Update user to verified and clear verification code
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);
            $success = "Email verified successfully! You can now login.";
        }
    } else {
        $error = "Invalid verification code.";
    }
} else {
    $error = "Verification code is missing.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Email Verification</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <a href="login.php" class="btn btn-primary">Login</a>
    <?php endif; ?>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
