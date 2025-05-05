<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Admin';

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

// Placeholder for settings update
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Example: update some settings (this can be expanded)
    $siteName = trim($_POST['site_name'] ?? '');
    if (empty($siteName)) {
        $errors[] = "Site name cannot be empty.";
    }

    if (empty($errors)) {
        // Save settings to database or config file (not implemented)
        $success = "Settings updated successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Settings - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container mt-4">
        <h2>Admin Settings</h2>
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
                <label for="site_name">Site Name</label>
                <input type="text" class="form-control" id="site_name" name="site_name" value="RealEase" required />
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="dark_mode" name="dark_mode" />
                <label class="form-check-label" for="dark_mode">Enable Dark Mode</label>
            </div>
            <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
