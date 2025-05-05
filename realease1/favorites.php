<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Favorites - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
    <script src="./assets/js/favorites_hover.js" defer></script>
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
                    <a href="search_properties.php" class="sidebar-link"><i class="fas fa-search"></i> Search Properties</a>
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
                    <h2>Favorites</h2>
                </div>
                <div class="row" id="favorites-list">
                    <!-- Example favorite property cards -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house 1.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 1</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱300,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house2.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 2</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱450,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house 3.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 2</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱450,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house 4.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 2</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱450,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house 5.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 2</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱450,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house 1.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 2</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱450,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./assets/image/house 3.jpg" class="card-img-top" alt="Favorite Property Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">Favorite Property 2</h5>
                                <p class="card-text">Location: Sample Location</p>
                                <p class="card-text">Price: ₱450,000</p>
                                <a href="#" class="btn btn-primary btn-sm">View Details</a>
                                <button class="btn btn-outline-danger btn-sm ml-2">Remove</button>
                            </div>
                        </div>
                    </div>

                    <!-- More favorite property cards can be added here -->
                </div>
            </main>
        </div>
    </div>
</body>
</html>
