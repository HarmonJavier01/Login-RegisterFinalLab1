<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();
if (!isAdmin()) {
    header("Location: ../user/dashboard.php");
    exit();
}

// Get stats for dashboard
$users_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$teachers_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
$contacts_count = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$recent_activities = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container mt-4">
        <h2>Admin Dashboard</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text display-4"><?= $users_count ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Teachers</h5>
                        <p class="card-text display-4"><?= $teachers_count ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Contacts</h5>
                        <p class="card-text display-4"><?= $contacts_count ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Recent Activities
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($recent_activities as $activity): ?>
                                <li class="list-group-item">
                                    <strong><?= $activity['action'] ?></strong>: <?= $activity['description'] ?>
                                    <small class="text-muted d-block"><?= $activity['created_at'] ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        User Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="userChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Users', 'Teachers', 'Admins'],
                datasets: [{
                    data: [<?= $users_count ?>, <?= $teachers_count ?>, 1],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>