<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/session.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        // Fetch user by email
        $stmt = $pdo->prepare("SELECT id, password, role, is_active, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (!$user['is_active']) {
                $errors[] = "Your account is deactivated. Please contact admin.";
            } elseif (!$user['is_verified']) {
                $errors[] = "Please verify your email before logging in.";
            } elseif (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        redirect('admin/index.php');
                        break;
                    case 'teacher':
                        redirect('teacher/index.php');
                        break;
                    case 'user':
                    default:
                        redirect('user/index.php');
                        break;
                }
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Harmon</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Login</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="login.php" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-link">Don't have an account? Register</a>
    </form>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
