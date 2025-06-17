<?php
// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch user details from the database
include 'db_connect.php';
$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT username, email, full_name, dob FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Finance Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --accent-color: #4895ef;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --success-color: #4bb543;
    --error-color: #ff3333;
    --warning-color: #ffcc00;
    --border-radius: 12px;
    --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color:#1f2f33;
    color: var(--dark-color);
    line-height: 1.6;
    padding: 20px;
}

.profile-container {
    max-width: 800px;
    margin: 0 auto;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    animation: fadeIn 0.5s ease;
}

.profile-header {
    text-align: center;
    padding: 30px 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    position: relative;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    position: relative;
    border-radius: 50%;
    border: 4px solid white;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    transition: var(--transition);
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.edit-avatar-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: var(--accent-color);
    color: white;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.edit-avatar-btn:hover {
    background-color: var(--secondary-color);
    transform: scale(1.1);
}

.profile-header h1 {
    font-size: 28px;
    margin-bottom: 5px;
    font-weight: 600;
}

.profile-header .username {
    font-size: 16px;
    opacity: 0.9;
}

.profile-details {
    padding: 30px;
}

.detail-card {
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
    overflow: hidden;
    transition: var(--transition);
}

.detail-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.detail-header {
    display: flex;
    align-items: center;
    padding: 20px;
    background-color: var(--light-color);
    border-bottom: 1px solid #eee;
}

.detail-header i {
    font-size: 20px;
    color: var(--primary-color);
    margin-right: 15px;
}

.detail-header h3 {
    font-size: 18px;
    font-weight: 600;
    flex-grow: 1;
}

.edit-btn {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 5px;
}

.edit-btn:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
}

.detail-content {
    padding: 20px;
}

.detail-item {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
    flex-wrap: wrap;
    align-items: center;
}

.detail-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #666;
    width: 150px;
    flex-shrink: 0;
}

.detail-value {
    flex-grow: 1;
    font-weight: 400;
}

.detail-input {
    flex-grow: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: var(--transition);
    display: none;
}

.detail-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
}

.password-input-group {
    width: 100%;
    margin-top: 10px;
    display: none;
}

.password-input-group .detail-input {
    margin-bottom: 10px;
    width: 100%;
}

.action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 20px;
}

.save-btn {
    background-color: var(--success-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.save-btn:hover {
    background-color: #3a9a33;
    transform: translateY(-2px);
}

.cancel-btn {
    background-color: #1f2f33;
    color: #666;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.cancel-btn:hover {
    background-color:#1f2f33 ;
    transform: translateY(-2px);
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: var(--border-radius);
    color: white;
    font-weight: 500;
    box-shadow: var(--box-shadow);
    transform: translateX(150%);
    transition: transform 0.3s ease;
    z-index: 1000;
}

.notification.success {
    background-color: var(--success-color);
}

.notification.error {
    background-color: var(--error-color);
}

.notification.show {
    transform: translateX(0);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-card {
    animation: slideUp 0.5s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-container {
        margin: 0 10px;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .detail-label {
        width: 100%;
        margin-bottom: 5px;
    }
    
    .detail-value, .detail-input {
        width: 100%;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .save-btn, .cancel-btn {
        width: 100%;
        justify-content: center;
    }
}.sidebar {
    width: 260px;
    background-color: #1f2f33;
    color: white;
    height: 100vh;
    padding: 20px;
    box-shadow: 4px 4px 8px rgba(255, 255, 255, 1);
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 15px;
    cursor: pointer;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
    line-height: 40px;
    margin-bottom: 5px;
}

.sidebar ul li:hover {
    box-shadow: 2px 2px 8px rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: block;
    font-weight: bold;
}

/* Main Content */
.main-content {
    margin-left: 260px;
    padding: 40px;
    flex-grow: 1;
    background-color: #1f2f33;
    opacity: 0.9;
}

.section {
    margin-bottom: 25px;
    padding: 25px;
    background: white;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.section:hover {
    transform: translateY(-5px);
    box-shadow: 6px 6px 8px rgba(0, 0, 0, 0);
}

h3 {
    margin-top: 0;
    color: #2c3e50;
    font-weight: 600;
}

/* Buttons */
.view-btn {
    padding: 12px 24px;
    border: none;
    background: linear-gradient(45deg, #3498db, #8e44ad);
    color: white;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: background 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.view-btn:hover {
    opacity: 0.7;
    transform: scale(1.05);
}
.logout-btn {
  display: block;
  margin-top: 30px;
  text-align: center;
  background-color: #ff4444;
  color: white;
  padding: 10px;
  border-radius: 5px;
  text-decoration: none;
  transition: 0.3s;
  width: 100%;
  cursor: pointer;
}

.logout-btn:hover {
  background-color: #ff2222;
  opacity: 0.8;
  color: white; /* Keep text color white during hover */
  box-shadow: none; /* Ensure no outline or shadow */
}
</style>
</head>
<body>
    <div class="sidebar">
        <img src="LOGO.png" alt="Logo" class="Logo" width="100" height="50" />
        <h2>USER DASHBOARD</h2>
        <ul>
            <li><a href="budget.php">💰 Budget Tracking</a></li>
            <li><a href="goaltracker.html">🎯 Goal Tracking</a></li>
            <li><a href="inv.html">📈 Investment</a></li>
            <li><a href="resultpage.php">📊 Results</a></li>
            <a href="logout.php" class="logout-btn">Logout</a>
        </ul>
    </div>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&background=random" alt="User Avatar" id="user-avatar">
                <button class="edit-avatar-btn" id="change-avatar-btn">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <h1 id="user-name"><?php echo $user['full_name']; ?></h1>
            <p class="username" id="user-username">@<?php echo $user['username']; ?></p>
        </div>

        <div class="profile-details">
    <div class="profile-details-row">
    <!-- Left: Personal Info -->
    <div class="detail-card animate-card">
        <div class="detail-header">
            <i class="fas fa-id-card"></i>
            <h3>Personal Information</h3>
            <button class="edit-btn" id="edit-personal-btn">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>
        <div class="detail-content">
            <div class="detail-item">
                <span class="detail-label">Full Name</span>
                <span class="detail-value" id="detail-name"><?php echo $user['full_name']; ?></span>
                <input type="text" class="detail-input" id="input-name" value="<?php echo $user['full_name']; ?>" style="display: none;">
            </div>
            <div class="detail-item">
                <span class="detail-label">Username</span>
                <span class="detail-value" id="detail-username"><?php echo $user['username']; ?></span>
                <input type="text" class="detail-input" id="input-username" value="<?php echo $user['username']; ?>" style="display: none;">
            </div>
            <div class="detail-item">
                <span class="detail-label">Date of Birth</span>
                <span class="detail-value" id="detail-dob"><?php echo $user['dob']; ?></span>
                <input type="date" class="detail-input" id="input-dob" value="<?php echo $user['dob']; ?>" style="display: none;">
            </div>
        </div>
    </div>

    <!-- Right: Security -->
    <div class="detail-card animate-card">
        <div class="detail-header">
            <i class="fas fa-lock"></i>
            <h3>Security</h3>
            <button class="edit-btn" id="edit-security-btn">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>
        <div class="detail-content">
            <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value" id="detail-email"><?php echo $user['email']; ?></span>
                <input type="email" class="detail-input" id="input-email" value="<?php echo $user['email']; ?>" style="display: none;">
            </div>
            <div class="detail-item">
                <span class="detail-label">Password</span>
                <span class="detail-value">••••••••</span>
                <div class="password-input-group" style="display: none;">
                    <input type="password" class="detail-input" id="input-current-password" placeholder="Current Password">
                    <input type="password" class="detail-input" id="input-new-password" placeholder="New Password">
                    <input type="password" class="detail-input" id="input-confirm-password" placeholder="Confirm Password">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="action-buttons" id="save-cancel-buttons" style="display: none;">
    <button class="save-btn" id="save-changes-btn">
        <i class="fas fa-save"></i> Save Changes
    </button>
    <button class="cancel-btn" id="cancel-changes-btn">
        <i class="fas fa-times"></i> Cancel
    </button>
</div>
<script src="user_details.js"></script>
</body>
</html>