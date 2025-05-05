<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_ADMIN);

// Fetch logs with user info
$stmt = $pdo->query("
    SELECT l.id, u.name AS user_name, l.action, l.ip_address, l.created_at
    FROM logs l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT 100
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/admin_nav.php'; ?>
<div class="container mt-4">
    <h1>Activity Logs</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>IP Address</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['user_name']) ?></td>
                <td><?= htmlspecialchars($log['action']) ?></td>
                <td><?= htmlspecialchars($log['ip_address']) ?></td>
                <td><?= htmlspecialchars($log['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
