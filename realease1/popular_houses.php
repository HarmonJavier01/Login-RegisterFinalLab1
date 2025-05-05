<?php
// popular_houses.php - Page to display all popular houses

session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

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

$username = $_SESSION['username'] ?? 'User';

// Fetch popular houses from database or define static data as fallback
$popular_houses = [];
try {
    $stmt = $pdo->query("SELECT id, title, location, price, image, rating, views FROM properties WHERE status = 'available' ORDER BY views DESC LIMIT 50");
    $popular_houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback static data if query fails
    $popular_houses = [
        [
            'id' => 1,
            'title' => 'Modern Family House',
            'location' => 'New York',
            'price' => 850000,
            'image' => './assets/image/house 1.jpg',
            'rating' => 4.5,
            'views' => 1200,
        ],
        [
            'id' => 2,
            'title' => 'Luxury Villa',
            'location' => 'Los Angeles',
            'price' => 1250000,
            'image' => './assets/image/house2.jpg',
            'rating' => 4.8,
            'views' => 980,
        ],
        [
            'id' => 3,
            'title' => 'Cozy Cottage',
            'location' => 'San Francisco',
            'price' => 650000,
            'image' => './assets/image/house 3.jpg',
            'rating' => 4.3,
            'views' => 1100,
        ],
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Popular Houses - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
    <script src="./assets/js/popular_houses_page_hover.js" defer></script>
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
                            <!-- <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i> Settings</a> -->
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <h1 class="mb-4">Popular Houses</h1>
        <div class="row">
            <?php foreach ($popular_houses as $house) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm position-relative">
                        <img src="<?php echo htmlspecialchars($house['image']); ?>" class="card-img-top" alt="House Image" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($house['title']); ?></h5>
                            <p class="card-text">
                                Location: <?php echo htmlspecialchars($house['location']); ?><br>
                                Price: ₱<?php echo number_format($house['price'], 2); ?><br>
                                Rating:
                                <?php
                                $fullStars = floor($house['rating']);
                                $halfStar = ($house['rating'] - $fullStars) >= 0.5;
                                for ($i = 0; $i < $fullStars; $i++) {
                                    echo '<i class="fas fa-star text-warning"></i>';
                                }
                                if ($halfStar) {
                                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                }
                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    echo '<i class="far fa-star text-warning"></i>';
                                }
                                ?>
                                (<?php echo number_format($house['rating'], 1); ?>)
                            </p>
                            <a href="property_details.php?id=<?php echo $house['id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
