<?php
require_once '../functions.php';
ensure_logged_in();
if (!is_admin()) {
    header('Location: ../login.php');
    exit;
}

// Fetch all users except admins
$stmt = $pdo->prepare("SELECT id, username, email, role, is_active, is_email_verified FROM users WHERE role != 'admin'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle activate/deactivate user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = (int)$_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        log_action($pdo, $_SESSION['user_id'], "Activated user ID $user_id");
    } elseif ($action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$user_id]);
        log_action($pdo, $_SESSION['user_id'], "Deactivated user ID $user_id");
    }
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Multi User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?=htmlspecialchars($_SESSION['username'])?> | <a href="../logout.php">Logout</a></p>

    <h3>Registered Users</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Email Verified</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?=htmlspecialchars($user['id'])?></td>
                <td><?=htmlspecialchars($user['username'])?></td>
                <td><?=htmlspecialchars($user['email'])?></td>
                <td><?=htmlspecialchars($user['role'])?></td>
                <td><?= $user['is_active'] ? 'Yes' : 'No' ?></td>
                <td><?= $user['is_email_verified'] ? 'Yes' : 'No' ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?=htmlspecialchars($user['id'])?>">
                        <?php if ($user['is_active']): ?>
                            <button type="submit" name="action" value="deactivate" class="btn btn-warning btn-sm">Deactivate</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="activate" class="btn btn-success btn-sm">Activate</button>
                        <?php endif; ?>
                    </form>
                    <!-- Edit/Delete buttons can be added here -->
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
