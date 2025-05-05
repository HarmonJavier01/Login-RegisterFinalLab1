<?php
// loan_details.php - Display loan calculation details

// Get input values from GET parameters
$propertyValue = isset($_GET['propertyValue']) ? floatval($_GET['propertyValue']) : 0;
$downPaymentPercent = isset($_GET['downPaymentPercent']) ? floatval($_GET['downPaymentPercent']) : 0;
$principalAmount = isset($_GET['principalAmount']) ? floatval($_GET['principalAmount']) : 0;
$interestRate = isset($_GET['interestRate']) ? floatval($_GET['interestRate']) : 0;
$loanTerm = isset($_GET['loanTerm']) ? intval($_GET['loanTerm']) : 0;

// Calculate monthly interest rate
$monthlyInterestRate = $interestRate / 100 / 12;
// Total number of payments
$totalPayments = $loanTerm * 12;

// Calculate monthly payment using formula for amortizing loan
if ($monthlyInterestRate > 0 && $totalPayments > 0) {
    $monthlyPayment = $principalAmount * ($monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalPayments)) / (pow(1 + $monthlyInterestRate, $totalPayments) - 1);
} else {
    $monthlyPayment = 0;
}

// Calculate total payment and total interest
$totalPayment = $monthlyPayment * $totalPayments;
$totalInterest = $totalPayment - $principalAmount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loan Details - RealEase</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="./layout/style.css" />
</head>
<body>
    <div class="container mt-5">
        <h2>Loan Details</h2>
        <table class="table table-bordered mt-4">
            <tbody>
                <tr>
                    <th>Property Value</th>
                    <td>$<?php echo number_format($propertyValue, 2); ?></td>
                </tr>
                <tr>
                    <th>Down Payment (%)</th>
                    <td><?php echo number_format($downPaymentPercent, 2); ?>%</td>
                </tr>
                <tr>
                    <th>Principal Amount</th>
                    <td>$<?php echo number_format($principalAmount, 2); ?></td>
                </tr>
                <tr>
                    <th>Interest Rate (%)</th>
                    <td><?php echo number_format($interestRate, 2); ?>%</td>
                </tr>
                <tr>
                    <th>Loan Term</th>
                    <td><?php echo $loanTerm; ?> years</td>
                </tr>
                <tr>
                    <th>Monthly Payment</th>
                    <td>$<?php echo number_format($monthlyPayment, 2); ?></td>
                </tr>
                <tr>
                    <th>Total Payment</th>
                    <td>$<?php echo number_format($totalPayment, 2); ?></td>
                </tr>
                <tr>
                    <th>Total Interest</th>
                    <td>$<?php echo number_format($totalInterest, 2); ?></td>
                </tr>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>
