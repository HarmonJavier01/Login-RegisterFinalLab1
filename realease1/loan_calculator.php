<?php
// loan_calculator.php - Loan Calculator Form
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
    <title>Loan Calculator - RealEase</title>
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
                    <a href="admin_settings.php" class="sidebar-link"><i class="fas fa-cog"></i> Settings</a>
                    <a href="add_property.php" class="sidebar-link"><i class="fas fa-plus"></i> Add House</a>
                    <a href="logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 content-area">
                <h2>Loan Calculator</h2>
                <form id="loanCalculatorForm" method="GET" action="loan_details.php">
                    <div class="form-group">
                        <label for="propertyValue">Property Value (₱)</label>
                        <input type="number" class="form-control" id="propertyValue" name="propertyValue" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="downPaymentPercent">Down Payment (%)</label>
                        <input type="number" class="form-control" id="downPaymentPercent" name="downPaymentPercent" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="principalAmount">Principal Amount (₱)</label>
                        <input type="number" class="form-control" id="principalAmount" name="principalAmount" readonly>
                    </div>
                    <div class="form-group">
                        <label for="interestRate">Interest Rate (%)</label>
                        <input type="number" class="form-control" id="interestRate" name="interestRate" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="loanTerm">Loan Term (years)</label>
                        <input type="number" class="form-control" id="loanTerm" name="loanTerm" min="1" step="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
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
</body>
</html>
