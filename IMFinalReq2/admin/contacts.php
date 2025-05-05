<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_ADMIN);

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    if ($contact_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$contact_id]);
        redirect('contacts.php');
    }
}

// Fetch all contacts with user and teacher info
$stmt = $pdo->query("
    SELECT c.id, c.name, c.email, c.phone, c.address, u.name AS user_name, t.name AS teacher_name, c.created_at
    FROM contacts c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN users t ON c.teacher_id = t.id
    ORDER BY c.created_at DESC
");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contacts - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/admin_nav.php'; ?>
<div class="container mt-4">
    <h1>Manage Contacts</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Contact Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>User</th>
                <th>Teacher</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?= htmlspecialchars($contact['name']) ?></td>
                <td><?= htmlspecialchars($contact['email']) ?></td>
                <td><?= htmlspecialchars($contact['phone']) ?></td>
                <td><?= htmlspecialchars($contact['address']) ?></td>
                <td><?= htmlspecialchars($contact['user_name']) ?></td>
                <td><?= htmlspecialchars($contact['teacher_name']) ?></td>
                <td><?= htmlspecialchars($contact['created_at']) ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                        <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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
