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
    <title>Notifications - RealEase</title>
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
                    <h2>Notifications</h2>
                </div>
                <div class="list-group" id="notifications-list" style="touch-action: pan-y;">
                    <!-- Example notifications -->
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start notification-item" ontouchstart="handleTouchStart(event)" ontouchmove="handleTouchMove(event)" ontouchend="handleTouchEnd(event)">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">New Property Added</h5>
                            <small>2 days ago</small>
                        </div>
                        <p class="mb-1">A new property matching your preferences has been added.</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start notification-item" ontouchstart="handleTouchStart(event)" ontouchmove="handleTouchMove(event)" ontouchend="handleTouchEnd(event)">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Price Drop Alert</h5>
                            <small>5 days ago</small>
                        </div>
                        <p class="mb-1">The price of a property in your favorites has dropped.</p>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action flex-column align-items-start notification-item" ontouchstart="handleTouchStart(event)" ontouchmove="handleTouchMove(event)" ontouchend="handleTouchEnd(event)">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Message from Agent</h5>
                            <small>1 week ago</small>
                        </div>
                        <p class="mb-1">You have received a new message from your property agent.</p>
                    </a>
                    <!-- More notifications can be added here -->
                </div>
            </main>
        </div>
    </div>

    <script>
        let touchStartX = 0;
        let touchCurrentX = 0;
        let swipedItem = null;

        function handleTouchStart(e) {
            touchStartX = e.touches[0].clientX;
            swipedItem = e.currentTarget;
            swipedItem.style.transition = '';
        }

        function handleTouchMove(e) {
            touchCurrentX = e.touches[0].clientX;
            const deltaX = touchCurrentX - touchStartX;
            if (deltaX > 0) { // Only allow swipe right
                swipedItem.style.transform = `translateX(${deltaX}px)`;
            }
        }

        function handleTouchEnd(e) {
            const deltaX = touchCurrentX - touchStartX;
            if (deltaX > 100) { // Threshold for swipe right
                // Automatically delete without confirmation
                swipedItem.style.transition = 'transform 0.3s ease-out';
                swipedItem.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    swipedItem.remove();
                }, 300);
            } else {
                swipedItem.style.transition = 'transform 0.3s ease-out';
                swipedItem.style.transform = 'translateX(0)';
            }
            swipedItem = null;
            touchStartX = 0;
            touchCurrentX = 0;
        }
    </script>
</body>
</html>
