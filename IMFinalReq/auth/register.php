<?php
require_once '../includes/functions.php';
require_once '../includes/phpmailer_setup.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $conn = getDBConnection();

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = 'Email already registered.';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Generate verification code
            $verification_code = generateVerificationCode();

            // Insert user with role 'user' by default
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, verification_code, is_verified, is_active, created_at) VALUES (?, ?, ?, 'user', ?, 0, 1, NOW())");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $verification_code);

            if ($stmt->execute()) {
                // Send verification email
                if (sendVerificationEmail($email, $verification_code)) {
                    $success = 'Registration successful! Please check your email for the verification code.';
                } else {
                    $errors[] = 'Registration successful but failed to send verification email.';
                }
            } else {
                $errors[] = 'Registration failed: ' . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register - Contact Management System</title>
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <h1>Register</h1>
    <?php if ($errors): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required /></label><br />
        <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required /></label><br />
        <label>Password: <input type="password" name="password" required /></label><br />
        <label>Confirm Password: <input type="password" name="confirm_password" required /></label><br />
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
