<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE reset_token=? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();

    if ($user_id) {
        // Update the password and clear token
        $update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE user_id=?");
        $update->bind_param("si", $new_password, $user_id);
        $update->execute();

        echo "Password successfully reset. <a href='login.php'>Login</a>";
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
}
?>
