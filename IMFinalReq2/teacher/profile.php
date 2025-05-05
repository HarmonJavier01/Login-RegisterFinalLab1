<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/validation.php';
require_role(ROLE_TEACHER);

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Fetch current user info
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate email
    if (!validate_email($email)) {
        $errors[] = "Invalid email address.";
    }

    // Check if email changed and is unique
    if ($email !== $user['email']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already taken.";
        }
    }

    // If changing password, validate current password and new password
    if (!empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'] ?? '')) {
            $errors[] = "Current password is incorrect.";
        }
        if (!validate_password($new_password)) {
            $errors[] = "New password must be at least 6 characters.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
    }

    if (empty($errors)) {
        // Update email and password if changed
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->execute([$email, $hashed_password, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $user_id]);
        }
        $success = "Profile updated successfully.";
        // Refresh user data
        $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/teacher_nav.php'; ?>
<div class="container mt-4">
    <h1>Profile</h1>
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
    <form method="post" action="profile.php" novalidate>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
        </div>
        <hr>
        <h5>Change Password</h5>
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password">
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password (min 6 characters)</label>
            <input type="password" class="form-control" id="new_password" name="new_password">
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
