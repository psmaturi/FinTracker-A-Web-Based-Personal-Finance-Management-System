<?php
include 'db_connect.php'; // your DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate reset token and expiry time
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Save token in DB
        $update = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();

        // Replace with your actual domain name or localhost path
   $reset_link = "http://localhost/fintracker/reset_password.php?token=$token";


        $subject = "Password Reset - FinTracker";
        $message = "Click this link to reset your password: $reset_link";
        $headers = "From: no-reply@prudhvisai0508.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "A password reset link has been sent to your email.";
        } else {
            echo "Email sending failed. Please try again.";
        }
    } else {
        echo "Email not registered.";
    }

    $stmt->close();
}
?>
