<?php
require_once 'includes/auth.php';
redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    if (isset($_POST['update_profile'])) {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        
        if (empty($name)) $errors[] = "Name is required";
        if (empty($email)) $errors[] = "Email is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
        
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) $errors[] = "Email already in use by another account";
        
        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            if ($stmt->execute([$name, $email, $_SESSION['user_id']])) {
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                logActivity($_SESSION['user_id'], 'update_profile', "Updated profile information", $_SERVER['REMOTE_ADDR']);
                $_SESSION['success'] = "Profile updated successfully";
            }
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password)) $errors[] = "Current password is required";
        if (empty($new_password)) $errors[] = "New password is required";
        if ($new_password !== $confirm_password) $errors[] = "New passwords don't match";
        
        if (empty($errors)) {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                    logActivity($_SESSION['user_id'], 'update_password', "Changed password", $_SERVER['REMOTE_ADDR']);
                    $_SESSION['success'] = "Password updated successfully";
                }
            } else {
                $errors[] = "Current password is incorrect";
            }
        }
    }
}

// Get current user data
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <h2>Profile Settings</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        Update Profile Information
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Change Password
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>