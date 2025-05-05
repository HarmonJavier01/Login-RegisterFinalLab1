<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

// Database connection parameters
$host = 'localhost';
$dbname = 'realease_db';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$errors = [];
$success = '';

if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit;
}

$user_id = intval($_GET['id']);

// Fetch user data
$stmt = $pdo->prepare("SELECT id, fullname, email, username, phone, address FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($fullname)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Check if username or email already exists for other users
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = :username OR email = :email) AND id != :id");
    $stmtCheck->execute(['username' => $username, 'email' => $email, 'id' => $user_id]);
    $count = $stmtCheck->fetchColumn();
    if ($count > 0) {
        $errors[] = "Username or email already exists for another user.";
    }

    if (empty($errors)) {
        $stmtUpdate = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email, username = :username, phone = :phone, address = :address WHERE id = :id");
        $stmtUpdate->execute([
            'fullname' => $fullname,
            'email' => $email,
            'username' => $username,
            'phone' => $phone,
            'address' => $address,
            'id' => $user_id
        ]);
        $success = "User updated successfully.";
        // Refresh user data
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit User - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container mt-4">
        <h2>Edit User</h2>
        <a href="admin_users.php" class="btn btn-secondary mb-3">Back to User Management</a>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
        </form>
    </div>
</body>
</html>
