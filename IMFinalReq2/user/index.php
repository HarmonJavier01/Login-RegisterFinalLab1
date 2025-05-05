<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_USER);

$user_id = $_SESSION['user_id'];

// Fetch user's contacts count for dashboard
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_contacts = $stmt->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/user_nav.php'; ?>
<div class="container mt-4">
    <h1>User Dashboard</h1>
    <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
        <div class="card-header">My Contacts</div>
        <div class="card-body">
            <h5 class="card-title"><?= $total_contacts ?></h5>
        </div>
    </div>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
