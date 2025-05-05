<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_USER);

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

// Fetch contacts with optional search filter
if ($search) {
    $search_param = '%' . $search . '%';
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR address LIKE ?) ORDER BY created_at DESC");
    $stmt->execute([$user_id, $search_param, $search_param, $search_param, $search_param]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
}
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Contacts - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/user_nav.php'; ?>
<div class="container mt-4">
    <h1>My Contacts</h1>
    <form method="get" action="contacts.php" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search contacts..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <a href="add_contact.php" class="btn btn-primary mb-3">Add Contact</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
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
                <td><?= htmlspecialchars($contact['created_at']) ?></td>
                <td>
                    <a href="edit_contact.php?id=<?= $contact['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <form method="post" action="contacts.php" onsubmit="return confirm('Are you sure you want to delete this contact?');" style="display:inline-block;">
                        <input type="hidden" name="delete_contact_id" value="<?= $contact['id'] ?>">
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
