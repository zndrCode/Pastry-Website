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
    <title>Loafly — Fresh Baked Daily</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/landing.css">
</head>
<body class="landing-body">

    <!-- Header -->
    <header class="landing-header">
        <a href="#" class="logo nav-logo">Loafly</a>
        <nav class="nav-menu">
            <a href="#about" class="nav-link">About</a>
            <a href="#menu" class="nav-link">Menu</a>
            <a href="#contact" class="nav-link">Contact</a>
        </nav>
        <div class="header-actions">
            <a href="login.php" class="login-btn">Login</a>
            <a href="signup.php" class="signup-btn">Sign Up</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-text-col">
            <div class="hero-eyebrow">
                <span class="eyebrow-dot"></span>
                <span>Baked Fresh Every Morning</span>
            </div>
            <h1 class="hero-title">
                Where Every<br>
                Bite Tells a<br>
                <em class="hero-accent">Story</em>
            </h1>
            <p class="hero-subtitle">
                Artisan pastries crafted with love and tradition,
                delivered warm to your door every morning.
            </p>
            <div class="hero-cta-row">
                <a href="signup.php" class="btn-hero-primary">Get Started</a>
                <a href="#menu" class="btn-hero-ghost">View Menu &rarr;</a>
            </div>
            <div class="hero-trust">
                <span class="trust-stars">★★★★★</span>
                <span class="trust-text">Loved by <strong>1,000+</strong> customers in Davao City</span>
            </div>
        </div>

        <div class="hero-image-col">
            <div class="hero-blob-wrap">
                <div class="hero-blob">
                    <img src="pastry1.png" alt="Assorted fresh pastries from Loafly" class="hero-pastry-img">
                </div>
                <!-- Floating badges -->
                <div class="hero-badge badge-top-right">
                    <span class="badge-icon">🥐</span>
                    <div class="badge-info">
                        <span class="badge-label">Daily Fresh</span>
                        <span class="badge-sub">Baked at dawn</span>
                    </div>
                </div>
                <div class="hero-badge badge-bottom-left">
                    <span class="badge-icon">⭐</span>
                    <div class="badge-info">
                        <span class="badge-label">Est. 2020</span>
                        <span class="badge-sub">Mintal, Davao</span>
                    </div>
                </div>
                <div class="hero-ring hero-ring-1"></div>
                <div class="hero-ring hero-ring-2"></div>
            </div>
        </div>

        <!-- Decorative scroll hint -->
        <div class="scroll-hint">
            <div class="scroll-line"></div>
            <span>Scroll</span>
        </div>
    </section>

    <!-- Marquee Strip -->
    <div class="marquee-strip">
        <div class="marquee-inner">
            <span>✦ FRESH DAILY</span>
            <span>✦ HANDCRAFTED</span>
            <span>✦ PREMIUM INGREDIENTS</span>
            <span>✦ DELIVERED WARM</span>
            <span>✦ SINCE 2020</span>
            <span>✦ ARTISAN BAKED</span>
            <!-- duplicate for seamless loop -->
            <span>✦ FRESH DAILY</span>
            <span>✦ HANDCRAFTED</span>
            <span>✦ PREMIUM INGREDIENTS</span>
            <span>✦ DELIVERED WARM</span>
            <span>✦ SINCE 2020</span>
            <span>✦ ARTISAN BAKED</span>
        </div>
    </div>

    <!-- Features Section (Numbered Editorial) -->
    <section class="features-section">
        <div class="features-header">
            <span class="section-eyebrow">Why Loafly</span>
            <h2 class="section-heading">The Loafly Difference</h2>
        </div>
        <div class="features-grid">
            <div class="feature-item">
                <span class="feature-num">01</span>
                <div class="feature-body">
                    <div class="feature-icon-wrap">🥐</div>
                    <h3 class="feature-title">Fresh Every Morning</h3>
                    <p class="feature-desc">Our bakers arrive before dawn to craft each pastry using traditional techniques and the finest ingredients — no shortcuts, ever.</p>
                </div>
            </div>
            <div class="feature-item">
                <span class="feature-num">02</span>
                <div class="feature-body">
                    <div class="feature-icon-wrap">🚚</div>
                    <h3 class="feature-title">Warm at Your Door</h3>
                    <p class="feature-desc">From our oven to your table within hours. We carefully package every order so it arrives just as warm and fresh as the moment it was baked.</p>
                </div>
            </div>
            <div class="feature-item">
                <span class="feature-num">03</span>
                <div class="feature-body">
                    <div class="feature-icon-wrap">⭐</div>
                    <h3 class="feature-title">Premium Quality</h3>
                    <p class="feature-desc">We source only the finest local and imported ingredients. Every bite is a commitment to excellence and a guarantee of satisfaction.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Pastry Showcase -->
    <section class="showcase-section" id="menu">
        <div class="showcase-header">
            <span class="section-eyebrow">Our Selection</span>
            <h2 class="section-heading">Made Fresh, <em>Daily</em></h2>
            <p class="showcase-sub">Over 50 varieties baked every morning — from flaky croissants to delicate danishes.</p>
        </div>
        <div class="bento-grid">
            <div class="bento-cell bento-main">
                <img src="pastry1.png" alt="Assorted Loafly pastries">
                <div class="bento-overlay">
                    <span class="bento-tag">Today's Selection</span>
                    <p class="bento-caption">Fresh croissants, tarts & more</p>
                </div>
            </div>
            <div class="bento-cell bento-side">
                <img src="pastry2.png" alt="Sugar-dusted puff pastries">
                <div class="bento-overlay">
                    <span class="bento-tag">Fan Favourite</span>
                    <p class="bento-caption">Puff Pastry Triangles</p>
                </div>
            </div>
            <div class="bento-cell bento-stat-a">
                <span class="bento-big-num">50<span class="bento-plus">+</span></span>
                <p class="bento-stat-label">Varieties available every day</p>
            </div>
            <div class="bento-cell bento-stat-b">
                <span class="bento-big-num">6<span class="bento-am">am</span></span>
                <p class="bento-stat-label">Ready for pickup &amp; delivery</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="about-stats-bar">
            <div class="stat-block">
                <span class="stat-num">4+</span>
                <span class="stat-lbl">Years of Baking</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-block">
                <span class="stat-num">50+</span>
                <span class="stat-lbl">Pastry Varieties</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-block">
                <span class="stat-num">1000+</span>
                <span class="stat-lbl">Happy Customers</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-block">
                <span class="stat-num">100%</span>
                <span class="stat-lbl">Made with Love</span>
            </div>
        </div>
        <div class="about-content-wrap">
            <div class="about-tag">
                <span class="section-eyebrow">Our Story</span>
            </div>
            <h2 class="about-heading">A Passion for the<br>Perfect Pastry</h2>
            <div class="about-text-cols">
                <p class="about-para">
                    Since 2020, Loafly has been bringing the warmth of freshly baked pastries to our community in Mintal, Davao City. We believe in traditional baking methods, premium ingredients, and the simple joy that comes from sharing delicious food.
                </p>
                <p class="about-para">
                    Every morning, our bakers arrive before dawn to craft each pastry with care and precision. From flaky croissants to delicate danishes, every item tells a story of dedication and passion for the craft.
                </p>
            </div>
            <a href="signup.php" class="about-cta">Join the Loafly Family &rarr;</a>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="contact-inner">
            <div class="contact-label">
                <span class="section-eyebrow">Find Us</span>
                <h2 class="section-heading">Visit Our Bakery</h2>
            </div>
            <div class="contact-cards">
                <div class="contact-card">
                    <div class="contact-icon">📍</div>
                    <h4 class="contact-card-title">Location</h4>
                    <p class="contact-card-text">Mintal, Davao City<br>Northern Mindanao, Philippines</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">🕐</div>
                    <h4 class="contact-card-title">Hours</h4>
                    <p class="contact-card-text">Mon – Fri<br>6:00 AM – 8:00 PM</p>
                </div>
                <div class="contact-card">
                    <div class="contact-icon">📬</div>
                    <h4 class="contact-card-title">Contact</h4>
                    <p class="contact-card-text">hello@loafly.com<br>Phone: (123) 456-7890</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="final-cta">
        <div class="cta-bg-shape"></div>
        <div class="cta-inner">
            <span class="section-eyebrow cta-eyebrow">Ready to order?</span>
            <h2 class="cta-heading">Start Your Morning<br><em>the Right Way</em></h2>
            <p class="cta-subtext">Join hundreds of happy customers enjoying artisan pastries delivered warm every morning.</p>
            <div class="cta-actions">
                <a href="signup.php" class="cta-btn-main">Create Your Account</a>
                <a href="login.php" class="cta-btn-outline">Already a member? Login</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <span class="logo footer-logo">Loafly</span>
            <p class="footer-copy">&copy; 2026 Loafly. All rights reserved. Made with love and flour.</p>
            <div class="footer-links">
                <a href="#about">About</a>
                <a href="#menu">Menu</a>
                <a href="#contact">Contact</a>
            </div>
        </div>
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

        // Scroll-triggered fade-in
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.feature-item, .bento-cell, .stat-block, .contact-card').forEach(el => {
            el.classList.add('fade-up');
            observer.observe(el);
        });
    </script>
</body>
</html>
