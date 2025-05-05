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
    <title>Add House - RealEase</title>
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
                    <h2>Add House</h2>
                </div>
                <form method="post" action="process_add_property.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="title">Property Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter property title" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" placeholder="Enter location" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="price">Price ($)</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Enter price" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="property_type">Property Type</label>
                            <select class="form-control" id="property_type" name="property_type" required>
                                <option value="">Select type</option>
                                <option value="house">House</option>
                                <option value="apartment">Apartment</option>
                                <option value="condo">Condo</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Property Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter property description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Property Image</label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Property</button>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
