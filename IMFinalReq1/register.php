<?php
require_once 'functions.php';

// if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
//     require __DIR__ . '/vendor/autoload.php'; // Ensure PHPMailer is loaded
// }

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = 'user'; // Default role for registration

    if (!$username) {
        $errors[] = 'Username is required.';
    }
    if (!$email) {
        $errors[] = 'Valid email is required.';
    }
    if (!$password) {
        $errors[] = 'Password is required.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = generate_verification_code();

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, verification_code) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $role, $verification_code]);
            $user_id = $pdo->lastInsertId();

            // Send verification email using PHP mail()
            $to = $email;
            $subject = 'Email Verification';
            $verification_link = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/verify_email.php?code=$verification_code";
            $message = "Hi $username,\n\nPlease click the link below to verify your email:\n$verification_link";
            $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

            if (mail($to, $subject, $message, $headers)) {
                log_action($pdo, $user_id, 'User registered and verification email sent');
                $success = 'Registration successful! Please check your email to verify your account.';
            } else {
                $errors[] = 'Verification email could not be sent.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Multi User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Register</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?=htmlspecialchars($error)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
    <?php endif; ?>
    <form method="post" action="register.php" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-link">Login</a>
    </form>
</div>
</body>
</html>
