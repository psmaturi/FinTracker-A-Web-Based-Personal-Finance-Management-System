<?php
include 'db_connect.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];

    // Validate required fields
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        die("Error: All required fields must be filled.");
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        die("Error: Passwords do not match.");
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile picture upload
    

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        die("Error: Email is already registered.");
    }
    $stmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password_hash, phone, dob, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fullName, $email, $username, $passwordHash, $phone, $dob, $profilePicPath);

    if ($stmt->execute()) {
        header("Location: login.html");
        exit(); // Ensure script stops execution after redirection
    } else {
        echo "Error: " . $stmt->error;
    }
    

    $stmt->close();
    $conn->close();
}
?>
