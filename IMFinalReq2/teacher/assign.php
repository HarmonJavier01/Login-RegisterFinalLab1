<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/validation.php';
require_role(ROLE_TEACHER);

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_name = sanitize_input($_POST['contact_name'] ?? '');
    $contact_email = sanitize_input($_POST['contact_email'] ?? '');
    $contact_phone = sanitize_input($_POST['contact_phone'] ?? '');
    $contact_address = sanitize_input($_POST['contact_address'] ?? '');
    $assigned_user_id = intval($_POST['assigned_user_id'] ?? 0);

    // Validate contact name
    if (empty($contact_name)) {
        $errors[] = "Contact name is required.";
    }

    // Validate assigned user exists and is a user role
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = ?");
    $stmt->execute([$assigned_user_id, ROLE_USER]);
    if (!$stmt->fetch()) {
        $errors[] = "Assigned user is invalid.";
    }

    if (empty($errors)) {
        // Insert contact with teacher_id and user_id
        $stmt = $pdo->prepare("INSERT INTO contacts (user_id, teacher_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$assigned_user_id, $user_id, $contact_name, $contact_email, $contact_phone, $contact_address]);
        $success = "Contact assigned successfully.";
    }
}

// Fetch users to assign contacts to
$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE role = ?");
$stmt->execute([ROLE_USER]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Contact - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/teacher_nav.php'; ?>
<div class="container mt-4">
    <h1>Assign Contact to User</h1>
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
    <form method="post" action="assign.php" novalidate>
        <div class="mb-3">
            <label for="contact_name" class="form-label">Contact Name</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" required value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="contact_email" class="form-label">Contact Email</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="contact_phone" class="form-label">Contact Phone</label>
            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="contact_address" class="form-label">Contact Address</label>
            <textarea class="form-control" id="contact_address" name="contact_address"><?= htmlspecialchars($_POST['contact_address'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="assigned_user_id" class="form-label">Assign to User</label>
            <select class="form-select" id="assigned_user_id" name="assigned_user_id" required>
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= (isset($_POST['assigned_user_id']) && $_POST['assigned_user_id'] == $user['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Assign Contact</button>
        <a href="contacts.php" class="btn btn-secondary">Back to Contacts</a>
    </form>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
