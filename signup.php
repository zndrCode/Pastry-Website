<?php
require_once 'config.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Get errors and form data
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['errors']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="logo">Loafly</h1>
    
    <div class="main-content">
        <div class="auth-container">
            <div class="auth-card">
                <h2 class="auth-title">Create Account</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form id="signupForm" action="signup_process.php" method="POST">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" required 
                               placeholder="Enter your full name"
                               value="<?php echo isset($form_data['fullName']) ? htmlspecialchars($form_data['fullName']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               placeholder="Enter your email"
                               value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Create a password">
                        <span class="password-hint">Must be at least 8 characters</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required 
                               placeholder="Confirm your password">
                    </div>
                    
                    <button type="submit" class="auth-button">Sign Up</button>
                </form>
                
                <div class="auth-link">
                    Already have an account? <a href="login.php">Log in here</a>
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