<?php
// admin_dashboard.php - Admin Dashboard
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Check if user is admin - if not, redirect to regular dashboard
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: dashboard.php");
    exit;
}

// Placeholder for admin data
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

// Fetch statistics from database
try {
    $totalUsersStmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $totalUsersStmt->fetchColumn();

    // Placeholder values for other stats - these should be replaced with real queries
    $activeProperties = 537;
    $pendingApprovals = 15;
    $loanApplications = 42;

    $registrationsTodayStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
    $registrationsTodayStmt->execute();
    $registrationsToday = $registrationsTodayStmt->fetchColumn();

    $messageRequests = 23;
} catch (PDOException $e) {
    $totalUsers = 0;
    $activeProperties = 0;
    $pendingApprovals = 0;
    $loanApplications = 0;
    $registrationsToday = 0;
    $messageRequests = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealEase - Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./layout/style.css">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg admin-navbar">
        <a class="navbar-brand" href="#">
            <i class="fas fa-shield-alt mr-2"></i>RealEase Admin
        </a>
        <button class="navbar-toggler bg-light" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-bell mr-1"></i> 
                        <span class="badge badge-danger">3</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-shield mr-1"></i><?php echo htmlspecialchars($username); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="admin_profile.php">Admin Profile</a>
                        <a class="dropdown-item" href="admin_settings.php">Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar py-3">
                <div class="list-group">
                    <a href="#" class="nav-link active">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="admin_users.php" class="nav-link">
                        <i class="fas fa-users mr-2"></i> User Management
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-building mr-2"></i> Properties
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-file-invoice-dollar mr-2"></i> Loan Applications
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-check-circle mr-2"></i> Approvals
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar mr-2"></i> Reports
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-envelope mr-2"></i> Messages
                    </a>
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog mr-2"></i> System Settings
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content-area">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Admin Dashboard</h2>
                    <div>
                        <button class="btn btn-outline-primary mr-2">
                            <i class="fas fa-download mr-1"></i> Export Reports
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Add Property
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow dashboard-card stat-card h-100" style="border-left-color: #4e73df !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300 stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow dashboard-card stat-card h-100" style="border-left-color: #1cc88a !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Properties</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $activeProperties; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-home fa-2x text-gray-300 stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow dashboard-card stat-card h-100" style="border-left-color: #f6c23e !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approvals</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingApprovals; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-check fa-2x text-gray-300 stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-0 shadow dashboard-card stat-card h-100" style="border-left-color: #e74a3b !important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Loan Applications</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $loanApplications; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300 stat-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Registrations -->
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow dashboard-card h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent User Registrations</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#">View All</a>
                                        <a class="dropdown-item" href="#">Export</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>User ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Registered</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                <tbody>
                    <?php
                    // Handle delete user from dashboard
                    if (isset($_GET['delete_user'])) {
                        $delete_id = intval($_GET['delete_user']);
                        $stmtDel = $pdo->prepare("DELETE FROM users WHERE id = :id");
                        $stmtDel->execute(['id' => $delete_id]);
                        // Redirect to avoid resubmission
                        header("Location: admin_dashboard.php");
                        exit;
                    }

                    // Fetch recent 5 user registrations
                    $stmtRecentUsers = $pdo->query("SELECT id, fullname, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
                    $recentUsers = $stmtRecentUsers->fetchAll(PDO::FETCH_ASSOC);

                    if ($recentUsers):
                        foreach ($recentUsers as $user):
                    ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('F d, Y', strtotime($user['created_at'])); ?></td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td>
                            <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="admin_dashboard.php?delete_user=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <tr><td colspan="6" class="text-center">No recent users found.</td></tr>
                    <?php endif; ?>
                </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="#" class="btn btn-sm btn-primary">View All Users</a>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Activity -->
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow dashboard-card h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Admin Activity</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-info text-white rounded p-3 mr-3">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">Today</div>
                                        <div class="font-weight-bold">New user registrations</div>
                                        <div><?php echo $registrationsToday; ?> new users today</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-success text-white rounded p-3 mr-3">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">Today</div>
                                        <div class="font-weight-bold">Properties approved</div>
                                        <div>7 properties approved today</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-warning text-white rounded p-3 mr-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">Pending</div>
                                        <div class="font-weight-bold">Message requests</div>
                                        <div><?php echo $messageRequests; ?> messages need response</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger text-white rounded p-3 mr-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">Alert</div>
                                        <div class="font-weight-bold">System notification</div>
                                        <div>3 issues need attention</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="#" class="btn btn-sm btn-primary">View Activity Log</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Properties and Sales Section -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow dashboard-card h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Properties</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonProperties" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButtonProperties">
                                        <a class="dropdown-item" href="#">View All</a>
                                        <a class="dropdown-item" href="#">Export</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Property ID</th>
                                                <th>Title</th>
                                                <th>Location</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                $stmtProperties = $pdo->query("SELECT id, title, location, price, status FROM properties ORDER BY created_at DESC LIMIT 5");
                                                $properties = $stmtProperties->fetchAll(PDO::FETCH_ASSOC);
                                                if ($properties):
                                                    foreach ($properties as $property):
                                            ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($property['id']); ?></td>
                                                <td><?php echo htmlspecialchars($property['title']); ?></td>
                                                <td><?php echo htmlspecialchars($property['location']); ?></td>
                                                <td>₱<?php echo number_format($property['price'], 2); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'badge-secondary';
                                                    $status = strtolower(trim($property['status']));
                                                    if ($status === 'available') $statusClass = 'badge-success';
                                                    elseif ($status === 'pending') $statusClass = 'badge-warning';
                                                    elseif ($status === 'sold') $statusClass = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($property['status']); ?></span>
                                                </td>
                                            </tr>
                                            <?php
                                                    endforeach;
                                                else:
                                            ?>
                                            <tr><td colspan="5" class="text-center">No recent properties found.</td></tr>
                                            <?php
                                                endif;
                                            } catch (PDOException $e) {
                                                echo '<tr><td colspan="5" class="text-center text-danger">Error loading properties.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card shadow dashboard-card h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonSales" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButtonSales">
                                        <a class="dropdown-item" href="#">View All</a>
                                        <a class="dropdown-item" href="#">Export</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sale ID</th>
                                                <th>Property ID</th>
                                                <th>Buyer</th>
                                                <th>Sale Price</th>
                                                <th>Sale Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                $stmtSales = $pdo->query("SELECT s.id, s.property_id, u.fullname AS buyer_name, s.sale_price, s.sale_date FROM sales s JOIN users u ON s.buyer_id = u.id ORDER BY s.sale_date DESC LIMIT 5");
                                                $sales = $stmtSales->fetchAll(PDO::FETCH_ASSOC);
                                                if ($sales):
                                                    foreach ($sales as $sale):
                                            ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($sale['id']); ?></td>
                                                <td>#<?php echo htmlspecialchars($sale['property_id']); ?></td>
                                                <td><?php echo htmlspecialchars($sale['buyer_name']); ?></td>
                                                <td>₱<?php echo number_format($sale['sale_price'], 2); ?></td>
                                                <td><?php echo date('F d, Y', strtotime($sale['sale_date'])); ?></td>
                                            </tr>
                                            <?php
                                                    endforeach;
                                                else:
                                            ?>
                                            <tr><td colspan="5" class="text-center">No recent sales found.</td></tr>
                                            <?php
                                                endif;
                                            } catch (PDOException $e) {
                                                echo '<tr><td colspan="5" class="text-center text-danger">Error loading sales data.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- End of content-area -->
        </div> <!-- End of row -->
    </div> <!-- End of container-fluid -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
