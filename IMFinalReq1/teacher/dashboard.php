<?php
require_once '../functions.php';
ensure_logged_in();
if (!is_teacher()) {
    header('Location: ../login.php');
    exit;
}

// Fetch contacts assigned to this teacher or created by this teacher
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE teacher_id = ? OR user_id = ?");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle contact deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact_id'])) {
    $contact_id = (int)$_POST['delete_contact_id'];
    // Verify ownership
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND (teacher_id = ? OR user_id = ?)");
    $stmt->execute([$contact_id, $_SESSION['user_id'], $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$contact_id]);
        log_action($pdo, $_SESSION['user_id'], "Deleted contact ID $contact_id");
    }
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard - Multi User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Teacher Dashboard</h2>
    <p>Welcome, <?=htmlspecialchars($_SESSION['username'])?> | <a href="../logout.php">Logout</a></p>

    <h3>Your Contacts</h3>
    <a href="add_contact.php" class="btn btn-primary mb-3">Add Contact</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?=htmlspecialchars($contact['name'])?></td>
                <td><?=htmlspecialchars($contact['email'])?></td>
                <td><?=htmlspecialchars($contact['phone'])?></td>
                <td><?=htmlspecialchars($contact['address'])?></td>
                <td>
                    <a href="edit_contact.php?id=<?=htmlspecialchars($contact['id'])?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this contact?');">
                        <input type="hidden" name="delete_contact_id" value="<?=htmlspecialchars($contact['id'])?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
