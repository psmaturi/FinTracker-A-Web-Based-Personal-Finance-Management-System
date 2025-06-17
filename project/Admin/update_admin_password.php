<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Check if new password and confirm password match
    if ($new !== $confirm) {
        die("❌ New passwords do not match.");
    }

    // Fetch the existing hashed password from the 'admin' table
    $new_password = 'new_password_here';
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
$query = "UPDATE admin SET password_hash = ? WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("si", $hashed_password, $admin_id);
$stmt->execute();

    $query->bind_param("i", $admin_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify the current password
        if (password_verify($current, $admin['password_hash'])) {
            $newHash = password_hash($new, PASSWORD_DEFAULT);

            // Update the password in the database
            $update = $conn->prepare("UPDATE admin SET password_hash = ? WHERE admin_id = ?");
            $update->bind_param("si", $newHash, $admin_id);
            $update->execute();

            // Optional: Log this action in a system_logs table
            $log = $conn->prepare("INSERT INTO system_logs (action, description) VALUES (?, ?)");
            $action = "Password Changed";
            $desc = "Admin (ID: $admin_id) updated their password.";
            $log->bind_param("ss", $action, $desc);
            $log->execute();

            // Redirect back to security page with success
            header("Location: admin_security.php?success=1");
            exit();
        } else {
            die("❌ Current password is incorrect.");
        }
    } else {
        die("❌ Admin not found.");
    }
}
?>
