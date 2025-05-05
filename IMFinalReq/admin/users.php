<?php
require_once '../../includes/auth.php';
redirectIfNotLoggedIn();
if (!isAdmin()) {
    header("Location: ../".$_SESSION['user_role']."/dashboard.php");
    exit();
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_teacher'])) {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        
        $errors = [];
        
        if (empty($name)) $errors[] = "Name is required";
        if (empty($email)) $errors[] = "Email is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
        if (empty($password)) $errors[] = "Password is required";
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = "Email already registered";
        
        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_verified, is_active) VALUES (?, ?, ?, 'teacher', TRUE, TRUE)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                logActivity($_SESSION['user_id'], 'create_teacher', "Created teacher account: $email", $_SERVER['REMOTE_ADDR']);
                $_SESSION['success'] = "Teacher account created successfully";
            }
        }
    } elseif (isset($_POST['update_status'])) {
        $user_id = $_POST['user_id'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        if ($stmt->execute([$is_active, $user_id])) {
            $action = $is_active ? 'activated' : 'deactivated';
            logActivity($_SESSION['user_id'], 'update_user_status', "$action user ID: $user_id", $_SERVER['REMOTE_ADDR']);
            $_SESSION['success'] = "User status updated";
        }
    }
}

// Handle search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? sanitize($_GET['role']) : '';

$query = "SELECT * FROM users WHERE role != 'admin'";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term]);
}

if (!empty($role_filter)) {
    $query .= " AND role = ?";
    $params[] = $role_filter;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container mt-4">
        <h2>User Management</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
                            Create Teacher Account
                        </button>
                    </div>
                    <div class="col-md-6">
                        <form class="row g-2">
                            <div class="col-md-4">
                                <select name="role" class="form-select">
                                    <option value="">All Roles</option>
                                    <option value="teacher" <?= $role_filter === 'teacher' ? 'selected' : '' ?>>Teachers</option>
                                    <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>Users</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="search" class="form-control" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= ucfirst($user['role']) ?></td>
                                    <td>
                                        <span class="badge <?= $user['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td><?= $user['created_at'] ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active" 
                                                    <?= $user['is_active'] ? 'checked' : '' ?> 
                                                    onchange="this.form.submit()">
                                                <input type="hidden" name="update_status" value="1">
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Teacher Modal -->
    <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Teacher Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="create_teacher" class="btn btn-primary">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>