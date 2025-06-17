<?php
// Start session
session_start();

// Include database connection
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Extract the data from the request
$name = $data['name'] ?? null;
$username = $data['username'] ?? null;
$dob = $data['dob'] ?? null;
$email = $data['email'] ?? null;
$currentPassword = $data['currentPassword'] ?? null;
$newPassword = $data['newPassword'] ?? null;
$confirmPassword = $data['confirmPassword'] ?? null;

// Initialize an array to collect validation errors
$errors = [];

// Validate the provided data
if (empty($name) || empty($username) || empty($dob) || empty($email)) {
    $errors[] = "All fields except password are required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!empty($currentPassword) && !empty($newPassword)) {
    if (strlen($newPassword) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($newPassword !== $confirmPassword) {
        $errors[] = "New password and confirm password do not match.";
    }
}

// If there are validation errors, return them to the client
if (!empty($errors)) {
    echo json_encode(['error' => implode(" ", $errors)]);
    exit();
}

// Update user details in the database
try {
    $conn->begin_transaction(); // Start a transaction

    // Check if the password needs to be updated
    if (!empty($currentPassword) && !empty($newPassword)) {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!password_verify($currentPassword, $user['password'])) {
            echo json_encode(['error' => 'Current password is incorrect']);
            exit();
        }

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashedPassword, $user_id);
        $stmt->execute();
    }

    // Update other personal details (name, username, dob, email)
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, dob = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $name, $username, $dob, $email, $user_id);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    // Return success response
    echo json_encode(['success' => 'Profile updated successfully']);
} catch (Exception $e) {
    // Rollback transaction if there is an error
    $conn->rollback();
    echo json_encode(['error' => 'An error occurred while updating profile']);
}
?>