<?php
session_start();
include 'db_connect.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                echo "<script>window.location.href='dashboard.php';</script>";
                exit();
            } else {
                echo "<script>alert('Invalid password.'); window.location.href='login.html';</script>";
            }
        } else {
            echo "<script>alert('No user found with this username.'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Please enter both username and password.'); window.location.href='login.html';</script>";
    }
}
?>
