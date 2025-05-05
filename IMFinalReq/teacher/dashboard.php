<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();
if (!isTeacher()) {
    header("Location: ../".$_SESSION['user_role']."/dashboard.php");
    exit();
}

// Get teacher's stats
$contacts_count = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE user_id = ?");
$contacts_count->execute([$_SESSION['user_id']]);
$contacts_count = $contacts_count->fetchColumn();

$assigned_users = $pdo->prepare("
    SELECT u.id, u.name, COUNT(a.id) as assignment_count 
    FROM assignments a
    JOIN users u ON a.user_id = u.id
    WHERE a.teacher_id = ?
    GROUP BY u.id
");
$assigned_users->execute([$_SESSION['user_id']]);
$assigned_users = $assigned_users->fetchAll();

$recent_activities = $pdo->prepare("
    SELECT al.*, u.name as user_name 
    FROM activity_logs al
    JOIN users u ON al.user_id = u.id
    WHERE u.id IN (
        SELECT user_id FROM assignments WHERE teacher_id = ?
    )
    ORDER BY al.created_at DESC 
    LIMIT 5
");
$recent_activities->execute([$_SESSION['user_id']]);
$recent_activities = $recent_activities->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container mt-4">
        <h2>Teacher Dashboard</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">My Contacts</h5>
                        <p class="card-text display-4"><?= $contacts_count ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Assigned Users</h5>
                        <p class="card-text display-4"><?= count($assigned_users) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Assigned Users
                    </div>
                    <div class="card-body">
                        <?php if (empty($assigned_users)): ?>
                            <p>No users assigned to you yet.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($assigned_users as $user): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($user['name']) ?>
                                        <span class="badge bg-primary rounded-pill"><?= $user['assignment_count'] ?> assignments</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Recent Student Activities
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_activities)): ?>
                            <p>No recent activities from your students.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($recent_activities as $activity): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars($activity['user_name']) ?></strong>: 
                                        <?= htmlspecialchars($activity['action']) ?> - <?= htmlspecialchars($activity['description']) ?>
                                        <small class="text-muted d-block"><?= $activity['created_at'] ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>