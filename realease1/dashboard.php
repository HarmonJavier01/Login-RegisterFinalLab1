<?php
// dashboard.php - User Dashboard
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

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch user profile details
$user_profile = [];
try {
    $stmt = $pdo->prepare("SELECT fullname, email, phone, address FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user_profile = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user_profile = [];
}

// Fetch summary statistics for user
$summary_stats = [
    'total_properties' => 0,
    'total_sales' => 0,
    'total_purchases' => 0,
];
try {
    // Total properties listed by user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $summary_stats['total_properties'] = (int)$stmt->fetchColumn();

    // Total sales made by user (properties sold)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales s JOIN properties p ON s.property_id = p.id WHERE p.user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $summary_stats['total_sales'] = (int)$stmt->fetchColumn();

    // Total purchases made by user (properties bought)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE buyer_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $summary_stats['total_purchases'] = (int)$stmt->fetchColumn();
} catch (PDOException $e) {
    // Ignore errors, keep defaults
}

// Fetch recent buy and sell houses for the user
$recent_properties = [];
try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.title, p.location, p.price, p.status, s.sale_date, 'Sold' AS transaction_type
        FROM sales s
        JOIN properties p ON s.property_id = p.id
        WHERE s.buyer_id = :user_id
        ORDER BY s.sale_date DESC
        LIMIT 5
        UNION
        SELECT p.id, p.title, p.location, p.price, p.status, p.updated_at AS sale_date, 'Available' AS transaction_type
        FROM properties p
        WHERE p.user_id = :user_id AND p.status = 'available'
        ORDER BY p.updated_at DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $user_id]);
    $recent_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle query error gracefully
    $recent_properties = [];
}

if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RealEase - Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
    <script src="./assets/js/app.js" defer></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
                <a class="navbar-brand" href="dashboard.php">
                    <img src="./assets/image/realease1-removebg-preview.png" alt="RealEase Logo" class="logo2" style="max-height: 40px; width: auto;" />
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
                            <a class="dropdown-item" href="?logout=1"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Splash Modal -->
    <div
        class="modal fade"
        id="splashModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="splashModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="splashModalLabel">Welcome to RealEase</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Hello, <?php echo htmlspecialchars($username); ?>! Here is a summary of your recent activity:</p>
                    <ul>
                        <li>You have <?php echo count($recent_properties); ?> recent buy/sell house activities.</li>
                        <!-- Additional recent app usage info can be added here -->
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Get Started</button>
                        </div>
                    </div>
                </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebarMenu">
                <div class="pt-3">
                    <!-- Added User Settings link -->
                    <a href="dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> Home</a>
                    <div class="sidebar-dropdown">
                        <a href="loan_calculator.php" class="sidebar-link dropdown-toggle"><i class="fas fa-calculator"></i> Calculator</a>
                        <div class="sidebar-submenu">
                            <a href="loan_calculator.php" class="sidebar-link pl-4">Loan Calculator</a>
                        </div>
                    </div>
                    <a href="search_properties.php" class="sidebar-link"><i class="fas fa-search"></i> Search Properties</a>
                    <a href="favorites.php" class="sidebar-link"><i class="fas fa-heart"></i> Favorites</a>
                    <a href="notifications.php" class="sidebar-link"><i class="fas fa-bell"></i> Notifications</a>
                    <!-- <a href="admin_settings.php" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a> -->
                    <a href="add_property.php" class="sidebar-link"><i class="fas fa-plus"></i> Add House</a>
                    <a href="?logout=1" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 content-area">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                </div>

                <!-- User Profile Summary -->
                <div class="row mb-4">
                    <!-- Removed user profile and summary statistics as per user request -->
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="jumbotron bg-light p-4 rounded">
                            <h2 class="display-5">Your RealEase Dashboard</h2>
                            <p class="lead">Manage your properties, view recent activity, and explore new opportunities.</p>
                            <hr class="my-4">
                            <p>Use the sidebar to navigate through your dashboard features.</p>
                            <a class="btn btn-primary btn-lg" href="add_property.php" role="button">Add New Property</a>
                        </div>
                    </div>
                </div>

                <!-- New Section: Popular Houses and Ratings -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Popular Houses & Ratings</h3>
                        <div class="row">
                            <?php
                            // Example static popular houses data; replace with dynamic query if available
                            $popular_houses = [
                                [
                                    'title' => 'Modern Family House',
                                    'location' => 'New York',
                                    'price' => 850000,
                                    'image' => './assets/image/house 1.jpg',
                                    'rating' => 4.5,
                                    'views' => 1200,
                                ],
                                [
                                    'title' => 'Luxury Villa',
                                    'location' => 'Los Angeles',
                                    'price' => 1250000,
                                    'image' => './assets/image/house 5.jpg',
                                    'rating' => 4.8,
                                    'views' => 980,
                                ],
                                [
                                    'title' => 'Estella',
                                    'location' => 'San Francisco',
                                    'price' => 650000,
                                    'image' => './assets/image/house 1.jpg',
                                    'rating' => 4.3,
                                    'views' => 1100,
                                ],
                                [
                                    'title' => 'Esme',
                                    'location' => 'San Francisco',
                                    'price' => 650000,
                                    'image' => './assets/image/house2.jpg',
                                    'rating' => 4.3,
                                    'views' => 1100,
                                ],
                                [
                                    'title' => 'Drew',
                                    'location' => 'San Francisco',
                                    'price' => 650000,
                                    'image' => './assets/image/house 3.jpg',
                                    'rating' => 4.3,
                                    'views' => 1100,
                                ],
                                [
                                    'title' => 'Anton',
                                    'location' => 'San Francisco',
                                    'price' => 650000,
                                    'image' => './assets/image/house 5.jpg',
                                    'rating' => 4.3,
                                    'views' => 1100,
                                ],
                            ];
                            foreach ($popular_houses as $house) : ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card shadow-sm position-relative popular-house-card">
                                        <img src="<?php echo htmlspecialchars($house['image']); ?>" class="card-img-top popular-house-img" alt="House Image" style="height: 200px; object-fit: cover;">
                                        <button class="btn btn-light position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="addToFavorites('<?php echo htmlspecialchars($house['title']); ?>')">
                                            <i class="far fa-heart" id="heart-icon-<?php echo htmlspecialchars($house['title']); ?>"></i>
                                        </button>
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
                                                <br>
                                                Views: <?php echo number_format($house['views']); ?>
                                            </p>
                                            <a href="popular_houses.php" class="btn btn-link p-0">View All</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- New Section: Amenities -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Property Amenities</h3>
                        <div class="d-flex flex-wrap">
                            <?php
                            // Example static amenities data; replace with dynamic query if available
                            $amenities = [
                                ['icon' => 'fas fa-swimming-pool', 'name' => 'Swimming Pool'],
                                ['icon' => 'fas fa-dumbbell', 'name' => 'Gym'],
                                ['icon' => 'fas fa-wifi', 'name' => 'Wi-Fi'],
                                ['icon' => 'fas fa-parking', 'name' => 'Parking'],
                                ['icon' => 'fas fa-tree', 'name' => 'Garden'],
                                ['icon' => 'fas fa-shield-alt', 'name' => 'Security'],
                                ['icon' => 'fas fa-tv', 'name' => 'Cable TV'],
                                ['icon' => 'fas fa-fireplace', 'name' => 'Fireplace'],
                            ];
                            foreach ($amenities as $amenity) : ?>
                                <div class="amenity-card text-center m-2 p-3 border rounded shadow-sm" style="width: 120px;">
                                    <i class="<?php echo htmlspecialchars($amenity['icon']); ?> fa-2x mb-2 text-primary"></i>
                                    <div><?php echo htmlspecialchars($amenity['name']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- New Section: Appointment Meeting and Calendar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3>Appointment & Calendar</h3>
                        <div class="card p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Schedule a Meeting</h5>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#appointmentModal">New Appointment</button>
                            </div>
                            <div id="calendar" class="border rounded p-3" style="min-height: 300px;">
                                <!-- Placeholder for calendar UI -->
                                <p class="text-muted">Calendar view coming soon...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Modal -->
                <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form id="appointmentForm" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="appointmentModalLabel">New Appointment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="appointmentDate">Date</label>
                                    <input type="date" class="form-control" id="appointmentDate" name="appointmentDate" required>
                                </div>
                                <div class="form-group">
                                    <label for="appointmentTime">Time</label>
                                    <input type="time" class="form-control" id="appointmentTime" name="appointmentTime" required>
                                </div>
                                <div class="form-group">
                                    <label for="appointmentNotes">Notes</label>
                                    <textarea class="form-control" id="appointmentNotes" name="appointmentNotes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Appointment</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <?php if (empty($recent_properties)) : ?>
                        <p>No recent buy or sell houses found.</p>
                    <?php else : ?>
                        <!-- Property Slider/Carousel -->
                        <div id="propertyCarousel" class="carousel slide w-100" data-ride="carousel" data-interval="5000">
                            <div class="carousel-inner">
                                <?php foreach ($recent_properties as $index => $property) : ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
                                            <img src="<?php echo htmlspecialchars($property['image'] ?? './assets/image/house-placeholder.png'); ?>" class="card-img-top" alt="House Image" style="height: 300px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                                                <p class="card-text">
                                                    Location: <?php echo htmlspecialchars($property['location']); ?><br>
                                                    Price: ₱<?php echo number_format($property['price'], 2); ?><br>
                                                    Status: <?php echo htmlspecialchars(ucfirst($property['status'])); ?><br>
                                                    Transaction: <?php echo htmlspecialchars($property['transaction_type']); ?>
                                                </p>
                                                <button class="btn btn-outline-primary btn-sm mr-2">Rate</button>
                                                <?php if ($property['status'] === 'available') : ?>
                                                    <button class="btn btn-success btn-sm">Buy</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <a class="carousel-control-prev" href="#propertyCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#propertyCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                // Fetch user's own house for sale
                $user_house = null;
                try {
                    $stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = :user_id AND status = 'available' LIMIT 1");
                    $stmt->execute(['user_id' => $user_id]);
                    $user_house = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $user_house = null;
                }
                ?>

                <?php if ($user_house) : ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h3>Your House for Sale</h3>
                            <div class="card">
                                <img src="<?php echo htmlspecialchars($user_house['image'] ?? './assets/image/house-placeholder.png'); ?>" class="card-img-top" alt="Your House Image" style="height: 300px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($user_house['title']); ?></h5>
                                    <p class="card-text">
                                        Location: <?php echo htmlspecialchars($user_house['location']); ?><br>
                                        Price: ₱<?php echo number_format($user_house['price'], 2); ?><br>
                                        Status: <?php echo htmlspecialchars(ucfirst($user_house['status'])); ?>
                                    </p>
                                    <a href="edit_property.php?id=<?php echo $user_house['id']; ?>" class="btn btn-primary">Edit Property Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- Removed message for no house listed for sale -->
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- New Section: User Inquiries -->
    <!-- Removed User Inquiries section as per user request -->

    <!-- Recent Properties Modal -->
    <div class="modal fade" id="recentPropertiesModal" tabindex="-1" role="dialog" aria-labelledby="recentPropertiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recentPropertiesModalLabel">Recently Used Buy and Sell Houses</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Transaction Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_properties as $property) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($property['title']); ?></td>
                                        <td><?php echo htmlspecialchars($property['location']); ?></td>
                                        <td>₱<?php echo number_format($property['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($property['status'])); ?></td>
                                        <td><?php echo htmlspecialchars($property['transaction_type']); ?></td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($property['sale_date']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            if (!sessionStorage.getItem('welcomeShown')) {
                $('#splashModal').modal('show');
                sessionStorage.setItem('welcomeShown', 'true');
            }

            // Function to add house to favorites (simulate with alert and icon toggle)
            window.addToFavorites = function(houseTitle) {
                const heartIcon = document.getElementById('heart-icon-' + houseTitle);
                if (heartIcon.classList.contains('far')) {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                    alert(houseTitle + ' added to favorites!');
                    // TODO: Add AJAX call here to update favorites in backend
                } else {
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                    alert(houseTitle + ' removed from favorites!');
                    // TODO: Add AJAX call here to update favorites in backend
                }
            };

            function calculatePrincipal() {
                var propertyValue = parseFloat($('#propertyValue').val()) || 0;
                var downPaymentPercent = parseFloat($('#downPaymentPercent').val()) || 0;
                var principal = propertyValue * (1 - downPaymentPercent / 100);
                $('#principalAmount').val(principal.toFixed(2));
            }

            $('#propertyValue, #downPaymentPercent').on('input', calculatePrincipal);

            // Initialize principal amount on page load
            calculatePrincipal();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown > a.dropdown-toggle');
            dropdownToggles.forEach(function (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const parent = this.parentElement;
                    parent.classList.toggle('active');
                    const submenu = parent.querySelector('.sidebar-submenu');
                    if (submenu) {
                        if (submenu.style.display === 'block') {
                            submenu.style.display = 'none';
                        } else {
                            submenu.style.display = 'block';
                        }
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function () {
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
