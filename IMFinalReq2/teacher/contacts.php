<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_TEACHER);

$user_id = $_SESSION['user_id'];

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact_id'])) {
    $contact_id = intval($_POST['delete_contact_id']);
    // Verify contact belongs to this teacher
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$contact_id, $user_id]);
    header('Location: contacts.php');
    exit;
}

// Fetch contacts assigned to this teacher
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Contacts - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/teacher_nav.php'; ?>
<div class="container mt-4">
    <h1>My Contacts</h1>
    <a href="assign.php" class="btn btn-primary mb-3">Assign Contact to User</a>
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
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this contact?');" style="display:inline-block;">
                        <input type="hidden" name="delete_contact_id" value="<?= $contact['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <!-- Edit functionality can be added here -->
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
