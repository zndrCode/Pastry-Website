<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Fresh Baked Daily</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/landing.css">
</head>
<body>
    <!-- Header -->
    <header class="landing-header">
        <h1 class="logo">Loafly</h1>
        <nav class="nav-menu">
            <a href="#about" class="nav-link">About</a>
            <a href="#contact" class="nav-link">Contact</a>
        </nav>
        <div class="header-actions">
            <a href="login.php" class="login-btn">Login</a>
            <a href="signup.php" class="signup-btn">Sign Up</a>
        </div>
    </header>
    
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h2 class="hero-title">Fresh Baked Goodness</h2>
                <p class="hero-subtitle">Artisan pastries crafted with love, delivered warm to your door every morning</p>
                <div class="hero-buttons">
                    <a href="signup.php" class="hero-cta primary">Get Started</a>
                </div>
            </div>
        </section>
        
        <!-- Features Section -->
        <section class="features-section">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🥐</div>
                    <h3 class="feature-title">Fresh Daily</h3>
                    <p class="feature-description">Baked fresh every morning using premium ingredients and traditional techniques</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3 class="feature-title">Fast Delivery</h3>
                    <p class="feature-description">Get your favorites delivered warm to your door within hours</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h3 class="feature-title">Premium Quality</h3>
                    <p class="feature-description">Only the finest ingredients in every bite, guaranteed satisfaction</p>
                </div>
            </div>
        </section>
        
        <!-- About Section -->
        <section class="about-section" id="about">
            <div class="about-container">
                <div class="about-content">
                    <h3 class="section-title">Our Story</h3>
                    <p class="about-text">
                        Since 2020, Loafly has been bringing the warmth of freshly baked pastries to our community. 
                        We believe in traditional baking methods, premium ingredients, and the joy that comes from 
                        sharing delicious food with the people you love.
                    </p>
                    <p class="about-text">
                        Every morning, our bakers arrive before dawn to craft each pastry with care and precision. 
                        From flaky croissants to delicate danishes, every item tells a story of dedication and passion.
                    </p>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number">4+</span>
                        <span class="stat-label">Years</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Varieties</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">Happy Customers</span>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Contact Section -->
        <section class="contact-section" id="contact">
            <h3 class="section-title">Visit Us</h3>
            <div class="contact-grid">
                <div class="contact-block">
                    <h4>Location</h4>
                    <p>Mintal<br>Davao City<br>Northern Mindanao, Philippines</p>
                </div>
                <div class="contact-block">
                    <h4>Hours</h4>
                    <p>Mon-Fri: 6:00 AM - 8:00 PM<</p>
                </div>
                <div class="contact-block">
                    <h4>Contact</h4>
                    <p>Phone: (123)<br>Email: hello@loafly.com</p>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="final-cta">
            <h2 class="cta-heading">Start Your Morning Right</h2>
            <p class="cta-subtext">Join hundreds of happy customers enjoying fresh pastries daily</p>
            <a href="signup.php" class="cta-button large">Create Your Account</a>
        </section>
    </div>
    
    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; 2026 Loafly. All rights reserved. Made with love and flour.</p>
    </footer>
    
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>