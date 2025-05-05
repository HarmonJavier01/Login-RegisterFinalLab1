<?php
// property_details.php - Page to display details of a single property

session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

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

$username = $_SESSION['username'] ?? 'User';

$property_id = $_GET['id'] ?? null;
if (!$property_id) {
    header("Location: popular_houses.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = :id");
    $stmt->execute(['id' => $property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$property) {
        header("Location: popular_houses.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching property details: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($property['title']); ?> - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <img src="./assets/image/realease1-removebg-preview.png" alt="RealEase Logo" class="logo2" />
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle nav-user"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                            <span><?php echo htmlspecialchars($username); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="user_profile.php"><i class="fas fa-user mr-2"></i> Profile</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i> Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <h1 class="mb-4"><?php echo htmlspecialchars($property['title']); ?></h1>
        <div class="card mb-4">
            <img src="<?php echo htmlspecialchars($property['image'] ?? './assets/image/house-placeholder.png'); ?>" class="card-img-top" alt="Property Image" style="height: 400px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">Location: <?php echo htmlspecialchars($property['location']); ?></h5>
                <p class="card-text">Price: ₱<?php echo number_format($property['price'], 2); ?></p>
                <p class="card-text">Status: <?php echo htmlspecialchars(ucfirst($property['status'])); ?></p>
                <p class="card-text">Description: <?php echo nl2br(htmlspecialchars($property['description'] ?? 'No description available.')); ?></p>
                <a href="popular_houses.php" class="btn btn-secondary">Back to Popular Houses</a>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
