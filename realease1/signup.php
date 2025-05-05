<?php
// signup.php - Registration Page
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

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process registration form submission
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid form submission";
    } else {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $terms = isset($_POST['terms']) ? true : false;
        
        // Basic validation
        if (empty($fullname)) {
            $errors[] = "Full name is required";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($username)) {
            $errors[] = "Username is required";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        if (!$terms) {
            $errors[] = "You must agree to the Terms & Conditions";
        }
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $errors[] = "Username or email already exists";
        }
        
// If no errors, proceed with registration
if (empty($errors)) {
    // Handle profile picture upload
    $profile_picture_path = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/profile_pics/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_picture_path = $newFileName;
            } else {
                $errors[] = 'There was an error moving the uploaded file.';
            }
        } else {
            $errors[] = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, username, password, profile_picture) VALUES (:fullname, :email, :username, :password, :profile_picture)");
        $stmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'username' => $username,
            'password' => $hashed_password,
            'profile_picture' => $profile_picture_path
        ]);
        $_SESSION['register_success'] = true;
        header("Location: index.php");
        exit;
    }
}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealEase - Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./layout/style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mt-3">
                <!-- <img src="./assets/image/realease1-removebg-preview.png" alt="RealEase Logo" class="logo"> -->
            </div>
        </div>
        
        <div class="row signup-container">
            <div class="col-md-6">
                <div class="p-4 p-md-5">
                    <h2 class="mb-3">Start to build your dream house</h2>
                    <p class="text-muted mb-4 signup-text">Sign up to our website and start planning your dream home and calculate your loan amount.</p>
                    
                    <?php if(!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="error-list mb-0">
                                <?php foreach($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <div class="form-group">
        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full name" value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>">
    </div>
    <div class="form-group">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
    </div>
    <div class="form-group">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
    </div>
    <div class="form-group">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password">
    </div>
    <div class="form-group">
        <label for="profile_picture">Profile Picture (optional)</label>
        <input type="file" class="form-control-file" id="profile_picture" name="profile_picture" accept="image/*">
    </div>
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="terms" name="terms" <?php echo isset($terms) && $terms ? 'checked' : ''; ?>>
            <label class="custom-control-label" for="terms">I agree to the <a href="#" class="terms-link">Terms & Conditions</a></label>
        </div>
    </div>
    <button type="submit" name="register" class="btn btn-primary btn-block mb-3">Sign up</button>
    
    <p class="text-center mt-4">
        Already have an account? <a href="index.php" class="font-weight-bold text-primary">Sign in</a>
    </p>
</form>
                </div>
            </div>
            <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-5">
                <img src="./assets/image/signup-illustration.png" alt="Signup Illustration" class="illustration">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
