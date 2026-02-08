<?php
require_once 'config.php';

// If already logged in as admin, redirect to admin dashboard
if (isset($_SESSION['user_id'])) {
    $is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : ($_SESSION['user_id'] == 1);
    if ($is_admin) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Regular user trying to access admin login - redirect to customer dashboard
        header("Location: dashboard.php");
        exit();
    }
}

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Loafly</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body class="admin-login-body">
    <div class="admin-login-container">
        <!-- Back to Store Link -->
        <a href="index.php" class="back-link">← Back to Store</a>
        
        <!-- Login Card -->
        <div class="admin-login-card">
            <div class="admin-header">
                <div class="admin-icon">🔐</div>
                <h1 class="admin-title">Admin Portal</h1>
                <p class="admin-subtitle">Loafly Management System</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>⚠️ Access Denied</strong><br>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="admin_login_process.php" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="email">
                        <span class="label-icon">👤</span>
                        Administrator Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="admin@loafly.com"
                        autocomplete="email"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <span class="label-icon">🔑</span>
                        Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="Enter your admin password"
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="admin-login-btn">
                    <span>Access Admin Panel</span>
                    <span class="btn-arrow">→</span>
                </button>
            </form>
            
            <div class="admin-footer">
                <p class="security-notice">
                    <span class="notice-icon">🛡️</span>
                    This area is restricted to authorized administrators only
                </p>
                <p class="customer-link">
                    Customer? <a href="login.php">Login here</a>
                </p>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="login-footer">
            <p>&copy; 2026 Loafly Admin Portal • Secure Authentication</p>
        </div>
    </div>
</body>
</html>
