<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_TEACHER);

// Fetch basic analytics for teacher dashboard
// Count contacts assigned to this teacher
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE teacher_id = ?");
$stmt->execute([$user_id]);
$total_contacts = $stmt->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<?php include '../includes/teacher_nav.php'; ?>
<div class="container mt-4">
    <h1>Teacher Dashboard</h1>
    <div class="card text-white bg-info mb-3" style="max-width: 18rem;">
        <div class="card-header">Contacts Assigned</div>
        <div class="card-body">
            <h5 class="card-title"><?= $total_contacts ?></h5>
        </div>
    </div>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
