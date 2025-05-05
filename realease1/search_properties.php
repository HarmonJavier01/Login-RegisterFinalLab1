<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

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

$location = $_GET['location'] ?? '';
$property_type = $_GET['property_type'] ?? '';
$price_range = $_GET['price_range'] ?? '';

// Build SQL query with filters
$sql = "SELECT * FROM properties WHERE status = 'available'";
$params = [];

if (!empty($location)) {
    $sql .= " AND location LIKE :location";
    $params['location'] = '%' . $location . '%';
}

if (!empty($property_type)) {
    $sql .= " AND property_type = :property_type";
    $params['property_type'] = $property_type;
}

if (!empty($price_range)) {
    if ($price_range === '500001+') {
        $sql .= " AND price >= 500001";
    } else {
        list($min_price, $max_price) = explode('-', $price_range);
        $sql .= " AND price BETWEEN :min_price AND :max_price";
        $params['min_price'] = $min_price;
        $params['max_price'] = $max_price;
    }
}

$sql .= " ORDER BY updated_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $properties = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search Properties - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebarMenu">
                <div class="pt-3">
                    <a href="dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> Home</a>
                    <div class="sidebar-dropdown">
                        <a href="loan_calculator.php" class="sidebar-link dropdown-toggle"><i class="fas fa-calculator"></i> Calculator</a>
                        <div class="sidebar-submenu">
                            <a href="loan_calculator.php" class="sidebar-link pl-4">Loan Calculator</a>
                        </div>
                    </div>
                    <a href="search_properties.php" class="sidebar-link active"><i class="fas fa-search"></i> Search Properties</a>
                    <a href="favorites.php" class="sidebar-link"><i class="fas fa-heart"></i> Favorites</a>
                    <a href="notifications.php" class="sidebar-link"><i class="fas fa-bell"></i> Notifications</a>
                    <!-- <a href="admin_settings.php" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a> -->
                    <a href="add_property.php" class="sidebar-link"><i class="fas fa-plus"></i> Add House</a>
                    <a href="logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 content-area">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    <h2>Search Properties</h2>
                </div>
                <form class="mb-4" method="GET" action="search_properties.php" id="searchForm">
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <input type="text" class="form-control" placeholder="Location" name="location" id="locationInput" value="<?php echo htmlspecialchars($location); ?>" autocomplete="off" />
                        </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control" name="property_type" id="propertyTypeSelect">
                        <option value="">Property Type</option>
                        <option value="house" <?php if ($property_type === 'house') echo 'selected'; ?>>House</option>
                        <option value="apartment" <?php if ($property_type === 'apartment') echo 'selected'; ?>>Apartment</option>
                        <option value="condo" <?php if ($property_type === 'condo') echo 'selected'; ?>>Condo</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control" name="price_range" id="priceRangeSelect">
                        <option value="">Price Range</option>
                        <option value="0-100000" <?php if ($price_range === '0-100000') echo 'selected'; ?>>₱0 - ₱100,000</option>
                        <option value="100001-300000" <?php if ($price_range === '100001-300000') echo 'selected'; ?>>₱100,001 - ₱300,000</option>
                        <option value="300001-500000" <?php if ($price_range === '300001-500000') echo 'selected'; ?>>₱300,001 - ₱500,000</option>
                        <option value="500001+" <?php if ($price_range === '500001+') echo 'selected'; ?>>₱500,001+</option>
                    </select>
                </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <div class="row" id="property-list">
                    <?php if (empty($properties)) : ?>
                        <p>No properties found matching your criteria.</p>
                    <?php else : ?>
                        <?php foreach ($properties as $property) : ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <div id="propertyCarousel<?php echo $property['id']; ?>" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php
                                            $images = explode(',', $property['image']);
                                            foreach ($images as $index => $image) :
                                            ?>
                                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                    <img src="<?php echo htmlspecialchars(trim($image)); ?>" class="d-block w-100" alt="Property Image" style="height: 200px; object-fit: cover;">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <a class="carousel-control-prev" href="#propertyCarousel<?php echo $property['id']; ?>" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#propertyCarousel<?php echo $property['id']; ?>" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                                        <p class="card-text">Location: <?php echo htmlspecialchars($property['location']); ?></p>
                                        <p class="card-text">Price: ₱<?php echo number_format($property['price'], 2); ?></p>
                                        <p class="card-text">
                                            Rating:
                                            <?php
                                            $fullStars = floor($property['rating']);
                                            $halfStar = ($property['rating'] - $fullStars) >= 0.5;
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
                                        </p>
                                        <a href="property_details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function () {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Auto-submit form on input change
            const searchForm = document.getElementById('searchForm');
            const locationInput = document.getElementById('locationInput');
            const propertyTypeSelect = document.getElementById('propertyTypeSelect');
            const priceRangeSelect = document.getElementById('priceRangeSelect');

            let timeoutId;

            function submitFormDebounced() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    searchForm.submit();
                }, 500); // debounce 500ms
            }

            locationInput.addEventListener('input', submitFormDebounced);
            propertyTypeSelect.addEventListener('change', submitFormDebounced);
            priceRangeSelect.addEventListener('change', submitFormDebounced);
        });
    </script>
</body>
</html>
