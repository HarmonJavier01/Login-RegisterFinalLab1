<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

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

// Fetch user info including profile picture
$stmt = $pdo->prepare("SELECT fullname, email, phone, address, profile_picture FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($fullname)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    // Handle profile picture upload
    $profile_picture_path = $user['profile_picture'];
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

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_picture_path = $newFileName;
            } else {
                $errors[] = "There was an error moving the uploaded file.";
            }
        } else {
            $errors[] = "Upload failed. Allowed file types: " . implode(", ", $allowedfileExtensions);
        }
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email, phone = :phone, address = :address, profile_picture = :profile_picture WHERE id = :id");
        $updateStmt->execute([
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'profile_picture' => $profile_picture_path,
            'id' => $user_id
        ]);
        $success = "Profile updated successfully.";
        // Refresh user data
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Profile - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container mt-4">
        <h2>User Profile</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label><br/>
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="./uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="max-width: 150px; max-height: 150px; margin-bottom: 10px;">
                <?php else: ?>
                    <p>No profile picture uploaded.</p>
                <?php endif; ?>
                <input type="file" class="form-control-file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
