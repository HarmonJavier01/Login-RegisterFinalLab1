<?php
// index.php - Login Page

session_start();

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

/**
 * Redirect to dashboard if user is already logged in
 */
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Handle login form submission
 */
$login_error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $login_error = "Invalid form submission";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Basic validation
        if (empty($username) || empty($password)) {
            $login_error = "Please enter both username and password";
        } else {
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['username'] === 'admin') {
    // Set default password if not hashed correctly
    if (!password_verify('admin123!', $user['password'])) {
        $hashedPassword = password_hash('admin123!', PASSWORD_DEFAULT);
        $updatePasswordStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updatePasswordStmt->execute(['password' => $hashedPassword, 'id' => $user['id']]);
    }

    // Ensure admin record exists
    if (!isset($admin) || !$admin) {
        $insertAdminStmt = $pdo->prepare("INSERT INTO admins (user_id, role) VALUES (:user_id, 'superadmin')");
        $insertAdminStmt->execute(['user_id' => $user['id']]);
    }
}

if ($user && password_verify($password, $user['password'])) {
    // Check if user is admin
    $adminStmt = $pdo->prepare("SELECT * FROM admins WHERE user_id = :user_id LIMIT 1");
    $adminStmt->execute(['user_id' => $user['id']]);
    $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $admin !== false;

    // Ensure admin link exists if user is admin but no admin record
    if ($_SESSION['is_admin'] === false && $user['username'] === 'admin') {
        $insertAdminStmt = $pdo->prepare("INSERT INTO admins (user_id, role) VALUES (:user_id, 'superadmin')");
        $insertAdminStmt->execute(['user_id' => $user['id']]);
        $_SESSION['is_admin'] = true;
    }

    // Redirect admin users to admin_dashboard.php
    if ($_SESSION['is_admin']) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit;
} else {
    $login_error = "Invalid username or password";
}
        }
    }
}

/**
 * Handle forgot password form submission (simulate)
 */
$forgot_error = '';
$forgot_success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $forgot_error = "Invalid form submission";
    } else {
        $forgot_email = trim($_POST['forgot_email']);

        if (empty($forgot_email)) {
            $forgot_error = "Please enter your email address";
        } elseif (!filter_var($forgot_email, FILTER_VALIDATE_EMAIL)) {
            $forgot_error = "Invalid email format";
        } else {
            // Simulate sending reset email
            $forgot_success = "Password reset instructions have been sent to your email (simulation).";
        }
    }
}

// Check for registration success message
if (isset($_SESSION['register_success'])) {
    $register_success = "Registration successful! Please log in.";
    unset($_SESSION['register_success']);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RealEase - Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container">
        <div class="row login-container">
            <div class="col-md-6">
                <div class="p-4 p-md-5">
                    <div class="row">
                        <div class="col-12 text-center mb-4">
                            <!-- <img src="./assets/image/realease1-removebg-preview.png" alt="RealEase Logo" class="logo" /> -->
                        </div>
                    </div>
                    <h2 class="mb-4">Welcome to RealEase!</h2>
                    <p class="text-muted mb-5">Begin planning your dream home and calculate your loan amount.</p>

                    <?php if (!empty($login_error)) : ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($login_error); ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($register_success)) : ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($register_success); ?></div>
                    <?php endif; ?>

                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required />
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember" />
                                <label for="remember" class="mb-0">Remember Me</label>
                            </div>
                            <a href="#" class="forgot-link" data-toggle="modal" data-target="#forgotPasswordModal">forgot password</a>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-block">Sign in</button>
                    </form>

                    <div class="divider">
                        <div class="divider-line"></div>
                        <div class="divider-text">Continue with</div>
                        <div class="divider-line"></div>
                    </div>

                    <button class="btn google-btn btn-block">
                        <img src="./assets/image/google.png" alt="Google" class="google-icon" />
                        Sign in with Google
                    </button>

                    <p class="text-center mt-4">
                        Don't have an account? <a href="signup.php" class="font-weight-bold text-primary">Sign up</a>
                    </p>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-block">
                <div class="login-image h-100" style="background-image: url('./assets/image/login image.jpg');"></div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($forgot_error)) : ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($forgot_error); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($forgot_success)) : ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($forgot_success); ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label for="forgot_email">Enter your email address</label>
                            <input type="email" class="form-control" id="forgot_email" name="forgot_email" required />
                        </div>
                        <button type="submit" name="forgot" class="btn btn-primary">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
