<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header with Logo and Logout -->
    <header class="landing-header">
        <h1 class="logo">Loafly</h1>
        <nav class="nav-menu">
            <a href="#menu" class="nav-link">Menu</a>
            <a href="#about" class="nav-link">About</a>
            <a href="#contact" class="nav-link">Contact</a>
        </nav>
        <div class="header-actions">
            <span class="user-greeting">Hello, <?php echo htmlspecialchars($user_name); ?>!</span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </header>
    
    <div class="main-content">
        <div class="landing-container">
            <!-- Hero Section -->
            <section class="hero-section">
                <h2 class="hero-title">Fresh Baked Daily</h2>
                <p class="hero-subtitle">Discover our delicious selection of artisan pastries, baked with love every morning</p>
                <a href="#menu" class="hero-cta">Explore Our Menu</a>
            </section>
            
            <!-- Featured Banner -->
            <section class="featured-banner">
                <div class="featured-content">
                    <span class="featured-badge">Special Offer</span>
                    <h3 class="featured-title">Weekend Special: Buy 3, Get 1 Free!</h3>
                    <p class="featured-text">Valid on all croissants and danishes this Saturday & Sunday</p>
                </div>
            </section>
            
            <!-- Category Tabs -->
            <section class="category-section">
                <h3 class="section-title">Our Menu</h3>
                <div class="category-tabs" id="menu">
                    <button class="category-tab active">All</button>
                    <button class="category-tab">Croissants</button>
                    <button class="category-tab">Danishes</button>
                    <button class="category-tab">Muffins</button>
                    <button class="category-tab">Specialty</button>
                </div>
            </section>
            
            <!-- Pastry Cards Grid -->
            <section class="pastry-cards">
                <div class="pastry-card">
                    <div class="card-badge">Popular</div>
                    <div class="card-image-wrapper">
                        <img src="pastry1.png" alt="Croissant" class="card-image">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Classic Croissant</h3>
                        <p class="card-description">Buttery, flaky layers of perfection baked fresh every morning</p>
                        <div class="card-footer">
                            <span class="card-price">$3.50</span>
                            <button class="card-button">Add to Order</button>
                        </div>
                    </div>
                </div>
                
                <div class="pastry-card">
                    <div class="card-badge new">New</div>
                    <div class="card-image-wrapper">
                        <img src="pastry2.png" alt="Danish" class="card-image">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Sugar Danish</h3>
                        <p class="card-description">Sweet and tender pastry dusted with powdered sugar</p>
                        <div class="card-footer">
                            <span class="card-price">$4.00</span>
                            <button class="card-button">Add to Order</button>
                        </div>
                    </div>
                </div>
                
                <div class="pastry-card">
                    <div class="card-image-wrapper">
                        <img src="pastry3.png" alt="Assorted" class="card-image">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Pastry Assortment</h3>
                        <p class="card-description">A delightful selection of our finest baked goods</p>
                        <div class="card-footer">
                            <span class="card-price">$12.00</span>
                            <button class="card-button">Add to Order</button>
                        </div>
                    </div>
                </div>
                
                <div class="pastry-card">
                    <div class="card-image-wrapper">
                        <img src="pastry1.png" alt="Almond Croissant" class="card-image">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Almond Croissant</h3>
                        <p class="card-description">Classic croissant filled with almond cream and topped with sliced almonds</p>
                        <div class="card-footer">
                            <span class="card-price">$4.50</span>
                            <button class="card-button">Add to Order</button>
                        </div>
                    </div>
                </div>
                
                <div class="pastry-card">
                    <div class="card-image-wrapper">
                        <img src="pastry2.png" alt="Fruit Danish" class="card-image">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Fruit Danish</h3>
                        <p class="card-description">Flaky pastry topped with seasonal fruits and glaze</p>
                        <div class="card-footer">
                            <span class="card-price">$4.25</span>
                            <button class="card-button">Add to Order</button>
                        </div>
                    </div>
                </div>
                
                <div class="pastry-card">
                    <div class="card-image-wrapper">
                        <img src="pastry3.png" alt="Baker's Choice" class="card-image">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Baker's Choice Box</h3>
                        <p class="card-description">Let our bakers surprise you with today's best selections</p>
                        <div class="card-footer">
                            <span class="card-price">$15.00</span>
                            <button class="card-button">Add to Order</button>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- About Section -->
            <section class="about-section" id="about">
                <div class="about-content">
                    <h3 class="section-title">Our Story</h3>
                    <p class="about-text">
                        At Loafly, we believe that every pastry tells a story. Since 2020, we've been crafting artisan 
                        baked goods using traditional techniques and the finest ingredients. Our bakers start before dawn 
                        each day to ensure you enjoy the freshest, most delicious pastries possible.
                    </p>
                    <p class="about-text">
                        From our signature buttery croissants to our delicate danishes, every item is made with passion 
                        and dedication. We're proud to serve our community with treats that bring joy to your day.
                    </p>
                </div>
                <div class="about-stats">
                    <div class="stat-item">
                        <span class="stat-number">4+</span>
                        <span class="stat-label">Years Baking</span>
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
            </section>
            
            <!-- Contact Section - Bento Grid -->
            <section class="contact-section" id="contact">
                <h3 class="section-title">Visit Us</h3>
                <div class="bento-grid">
                    <div class="bento-item bento-large">
                        <div>
                            <div class="bento-icon">📍</div>
                            <h4>Our Location</h4>
                            <p>123 Baker Street<br>Tangub City<br>Northern Mindanao, Philippines</p>
                        </div>
                        <a href="https://maps.google.com" target="_blank" class="bento-link">Get Directions →</a>
                    </div>
                    
                    <div class="bento-item bento-tall">
                        <div>
                            <div class="bento-icon">🕐</div>
                            <h4>Opening Hours</h4>
                        </div>
                        <div class="hours-list">
                            <div class="hours-item">
                                <span>Mon - Fri</span>
                                <span>6:00 AM - 8:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span>Saturday</span>
                                <span>7:00 AM - 9:00 PM</span>
                            </div>
                            <div class="hours-item">
                                <span>Sunday</span>
                                <span>7:00 AM - 9:00 PM</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bento-item bento-medium">
                        <div>
                            <div class="bento-icon">📞</div>
                            <h4>Call Us</h4>
                            <p class="contact-detail">(123) 456-7890</p>
                        </div>
                        <a href="tel:1234567890" class="bento-link">Call Now →</a>
                    </div>
                    
                    <div class="bento-item bento-medium">
                        <div>
                            <div class="bento-icon">✉️</div>
                            <h4>Email Us</h4>
                            <p class="contact-detail">hello@loafly.com</p>
                        </div>
                        <a href="mailto:hello@loafly.com" class="bento-link">Send Email →</a>
                    </div>
                    
                    <div class="bento-item bento-wide bento-highlight">
                        <div class="bento-content-row">
                            <div>
                                <h4>Order for Pickup</h4>
                                <p>Skip the line! Order online and pick up at your convenience</p>
                            </div>
                            <button class="order-button">Order Now</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; 2026 Loafly. All rights reserved. Made with ❤️ and flour.</p>
    </footer>
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Optional: Add active state to navigation based on scroll position
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>