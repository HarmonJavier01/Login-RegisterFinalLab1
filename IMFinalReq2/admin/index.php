<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_role(ROLE_ADMIN);

// Fetch basic analytics for dashboard
// Count users by role
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$user_counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Count total contacts
$stmt = $pdo->query("SELECT COUNT(*) FROM contacts");
$total_contacts = $stmt->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Harmon</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/chart.js"></script>
</head>
<body>
<?php include '../includes/admin_nav.php'; ?>
<div class="container mt-4">
    <h1>Admin Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Users</div>
                <div class="card-body">
                    <p>Admins: <?= $user_counts['admin'] ?? 0 ?></p>
                    <p>Teachers: <?= $user_counts['teacher'] ?? 0 ?></p>
                    <p>Users: <?= $user_counts['user'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Contacts</div>
                <div class="card-body">
                    <p>Total Contacts: <?= $total_contacts ?></p>
                </div>
            </div>
        </div>
    </div>
    <canvas id="analyticsChart" width="400" height="200"></canvas>
</div>
<script>
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    const analyticsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Admins', 'Teachers', 'Users'],
            datasets: [{
                label: 'User Counts',
                data: [
                    <?= $user_counts['admin'] ?? 0 ?>,
                    <?= $user_counts['teacher'] ?? 0 ?>,
                    <?= $user_counts['user'] ?? 0 ?>
                ],
                backgroundColor: ['#007bff', '#28a745', '#ffc107']
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
</body>
</html>
