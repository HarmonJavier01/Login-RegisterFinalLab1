<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_role(ROLE_ADMIN);

// Handle activate/deactivate/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = intval($_POST['user_id'] ?? 0);

    if ($user_id > 0) {
        if ($action === 'activate') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$user_id]);
        } elseif ($action === 'deactivate') {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
            $stmt->execute([$user_id]);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
        }
        // Redirect to avoid form resubmission
        redirect('users.php');
    }
}

// Fetch all users except admins (or include admins if needed)
$stmt = $pdo->query("SELECT id, name, email, role, is_active, is_verified, created_at FROM users WHERE role != 'admin' ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/admin_nav.php'; ?>
<div class="container mt-4">
    <h1>Manage Users</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Verified</th>
                <th>Active</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= $user['is_verified'] ? 'Yes' : 'No' ?></td>
                <td><?= $user['is_active'] ? 'Yes' : 'No' ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <?php if ($user['is_active']): ?>
                            <button type="submit" name="action" value="deactivate" class="btn btn-warning btn-sm">Deactivate</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="activate" class="btn btn-success btn-sm">Activate</button>
                        <?php endif; ?>
                    </form>
                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
