<?php

session_start();
 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <div class="container welcome-container">
        <div class="success-icon">
            <div class="check-circle">
                <svg viewBox="0 0 52 52" class="checkmark">
                    <circle class="checkmark-circle" fill="none" cx="26" cy="26" r="25"></circle>
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path>
                </svg>
            </div>
        </div>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        <p>You have successfully logged into your account.</p>
        <p>
            <a href="logout.php" class="btn logout-btn">Sign Out</a>
        </p>
    </div>
</body>
</html>