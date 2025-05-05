<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/validation.php';
require_once 'includes/mailer.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (!validate_name($name)) {
        $errors[] = "Name is required and must be less than 100 characters.";
    }
    if (!validate_email($email)) {
        $errors[] = "Valid email is required.";
    }
    if (!validate_password($password)) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        } else {
            // Insert user with hashed password and verification code
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = generate_verification_code(16);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_active, is_verified, verification_code) VALUES (?, ?, ?, 'user', 1, 0, ?)");
            $stmt->execute([$name, $email, $hashed_password, $verification_code]);

            // Send verification email
            if (send_verification_email($email, $verification_code)) {
                $success = "Registration successful! Please check your email to verify your account.";
            } else {
                $errors[] = "Failed to send verification email.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Harmon</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Register</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post" action="register.php" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password (min 6 characters)</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
    </form>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
