<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

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
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-page">

        <!-- Brand Header -->
        <div class="auth-header">
            <h1 class="auth-brand-logo">Loafly</h1>
            <p class="auth-brand-tagline">Join our community of pastry lovers</p>
            <div class="auth-badges">
                <span class="auth-badge">Exclusive Offers</span>
                <span class="auth-badge">Rewards Program</span>
                <span class="auth-badge">Easy Ordering</span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="auth-card">
            <h2 class="form-title">Create Account</h2>
            <p class="form-subtitle">Start your journey with Loafly today</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="signup_process.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required
                           placeholder="John Doe"
                           value="<?php echo isset($form_data['fullName']) ? htmlspecialchars($form_data['fullName']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required
                           placeholder="you@example.com"
                           value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="At least 8 characters">
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required
                           placeholder="Re-enter password">
                </div>

                <button type="submit" class="auth-button">Create Account</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>

    </div>
</body>
</html>
