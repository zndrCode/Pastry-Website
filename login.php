<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

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
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-page">

        <!-- Brand Header -->
        <div class="auth-header">
            <h1 class="auth-brand-logo">Loafly</h1>
            <p class="auth-brand-tagline">Freshly baked happiness, delivered daily</p>
            <div class="auth-badges">
                <span class="auth-badge">Artisan Pastries</span>
                <span class="auth-badge">Baked Fresh Daily</span>
                <span class="auth-badge">Made with Love</span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="auth-card">
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">Sign in to your account to continue</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="login_process.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="you@example.com">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="auth-button">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                <p>Administrator? <a href="admin_login.php">Admin Login</a></p>
                <p>Go back? <a href="index.php">Yes</a></p>
            </div>
        </div>

    </div>
</body>
</html>
