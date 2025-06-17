<!-- index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Finance Tracker</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="wrapper">
    <div class="login_box">
        <div class="login-header">
            <span style="text-align: center;">Admin Login</span>
        </div>
        <form id="adminLoginForm" action="admin_login.php" method="POST">
            <div class="input_box">
                <input type="text" id="username" name="username" class="input-field" required>
                <label for="username" class="label">Username</label>
                <i class="bx bx-user icon"></i>
            </div>
            <div class="input_box">
                <input type="password" id="password" name="password" class="input-field" required>
                <label for="password" class="label">Password</label>
                <i class="bx bx-lock-alt icon"></i>
            </div>
            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <div class="forgot">
                    <a href="#">Forgot password?</a>
                </div>
            </div>
            <div class="input_box">
                <button type="submit" class="input-submit">Login</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
