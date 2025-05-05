<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_username, $db_password, $role);
        $stmt->fetch();
        
        if (password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            
            header("Location: " . ($role === 'admin' ? 'admin_Dashboard.php' : 'user_Dashboard.php'));
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RealEase - Log In</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="row shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 1000px;">
      
      <!-- Form Section -->
      <div class="col-lg-6 bg-white p-5">
        <img src="./assets/realease1-removebg-preview.png" alt="RealEase Logo" class="mb-4" style="width: 100px;">
        <h2 class="fw-bold mb-3">Welcome to RealEase!</h2>
        <p class="text-muted mb-4">Begin planning your dream home and calculate your loan amount.</p>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="signupForm" method="POST" action="">
          <div class="mb-3">
            <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" required>
          </div>
          <div class="mb-3">
            <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
          </div>

          <div class="d-flex justify-content-between mb-3 small">
            <div>
              <input type="checkbox" id="remember"> <label for="remember">Remember Me</label>
            </div>
            <a href="#" class="text-decoration-none">Forget password</a>
          </div>

          <button type="submit" class="btn btn-info w-100 text-white mb-3">Log in</button>

          <div class="text-center text-muted my-3">
            <span>— Continue with —</span>
          </div>

          <button type="button" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2">
            <img src="./assets/google.png" width="20" height="20" alt="Google">
            Sign in with Google
          </button>

          <p class="text-center mt-4">Don’t have an account? <a href="#" class="fw-bold text-primary text-decoration-none">Sign up</a></p>
        </form>
      </div>

      <!-- Image Section -->
      <div class="col-lg-6 d-none d-lg-block p-0">
        <img src="./assets/login image.jpg" alt="House" class="img-fluid h-100 w-100 object-fit-cover">
      </div>

    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- JS Validation -->
  <script>
    document.getElementById('signupForm').addEventListener('submit', function(e) {
      const username = this.username.value.trim();
      const password = this.password.value.trim();
      if (!username || !password) {
        e.preventDefault();
        alert("Please fill out both username and password.");
      }
    });
  </script>
</body>
</html>
