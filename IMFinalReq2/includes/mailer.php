<?php
// Custom mailer function without PHPMailer
function send_verification_email($to_email, $verification_code) {
    $subject = "Verify your email for Harmon";
    $verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php?code=" . urlencode($verification_code);
    $message = "Please click the following link to verify your email address:\n\n" . $verification_link;
    $headers = "From: no-reply@harmonjavier01@gmail.com\r\n";
    $headers .= "Reply-To: no-reply@harmonjavier01@gmail.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Use mail() function; ensure SMTP is configured in php.ini or use a local mail server
    return mail($to_email, $subject, $message, $headers);
}
?>
