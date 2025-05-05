<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/validation.php';
require_role(ROLE_USER);

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');

    $validation_errors = validate_contact(['name' => $name, 'email' => $email]);
    if ($validation_errors) {
        $errors = array_merge($errors, $validation_errors);
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO contacts (user_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $name, $email, $phone, $address]);
        $success = "Contact added successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Contact - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/user_nav.php'; ?>
<div class="container mt-4">
    <h1>Add Contact</h1>
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
    <form method="post" action="add_contact.php" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Contact Name</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Contact Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Contact Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Contact Address</label>
            <textarea class="form-control" id="address" name="address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Contact</button>
        <a href="contacts.php" class="btn btn-secondary">Back to Contacts</a>
    </form>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
