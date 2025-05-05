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
    header("Location: admin_dashboard.php");
    exit;
}

$property_id = intval($_GET['id']);

// Fetch property data
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_property'])) {
    $title = trim($_POST['title']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);
    $description = trim($_POST['description']);

    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($location)) {
        $errors[] = "Location is required.";
    }
    if (empty($status)) {
        $errors[] = "Status is required.";
    }

    if (empty($errors)) {
        $stmtUpdate = $pdo->prepare("UPDATE properties SET title = :title, location = :location, status = :status, description = :description, updated_at = NOW() WHERE id = :id");
        $stmtUpdate->execute([
            'title' => $title,
            'location' => $location,
            'status' => $status,
            'description' => $description,
            'id' => $property_id
        ]);
        $success = "Property updated successfully.";
        // Refresh property data
        $stmt->execute(['id' => $property_id]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Property - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Property</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
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
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Active" <?php echo ($property['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Pending" <?php echo ($property['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Inactive" <?php echo ($property['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($property['description']); ?></textarea>
            </div>
            <button type="submit" name="update_property" class="btn btn-primary">Update Property</button>
        </form>
    </div>
</body>
</html>
