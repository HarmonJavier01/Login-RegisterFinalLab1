<?php
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username) {
        $errors[] = 'Username is required.';
    }
    if (!$password) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                $errors[] = 'Your account is deactivated. Please contact admin.';
            } elseif (!$user['is_email_verified']) {
                $errors[] = 'Please verify your email before logging in.';
            } else {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                log_action($pdo, $user['id'], 'User logged in');

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } elseif ($user['role'] === 'teacher') {
                    header('Location: teacher/dashboard.php');
                } else {
                    header('Location: user/dashboard.php');
                }
                exit;
            }
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Multi User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Login</h2>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?=htmlspecialchars($error)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="login.php" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-link">Register</a>
    </form>
</div>
</body>
</html>
