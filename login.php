<?php
require_once 'config.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Get error/success messages
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="logo">Loafly</h1>
    
    <div class="main-content">
        <div class="auth-container">
            <div class="auth-card">
                <h2 class="auth-title">Login</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form id="loginForm" action="login_process.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="auth-button">Sign In</button>
                </form>
                
                <div class="auth-link">
                    <a href="signup.php">Create an account</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pastry Footer -->
    <div class="pastry-footer">
        <div class="pastry-images">
            <div class="pastry-image-container">
                <img src="pastry1.png" alt="Pastry 1" class="pastry-image pastry1">
            </div>
            <div class="pastry-image-container">
                <img src="pastry2.png" alt="Pastry 2" class="pastry-image pastry2">
            </div>
            <div class="pastry-image-container">
                <img src="pastry3.png" alt="Pastry 3" class="pastry-image pastry3">
            </div>
        </div>
    </div>
</body>
</html>