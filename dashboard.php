<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Get cart count
$cart_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
$cart_data = $cart_result->fetch_assoc();
$cart_count = $cart_data['total'] ?? 0;
$cart_stmt->close();

// Fetch all products from database (only in-stock items)
$products_query = "SELECT * FROM products WHERE quantity > 0 ORDER BY created_at DESC";
$products_result = $conn->query($products_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Home</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Header -->
    <header class="landing-header">
        <h1 class="logo">Loafly</h1>
        <nav class="nav-menu">
            <a href="#menu" class="nav-link">Menu</a>
            <a href="#about" class="nav-link">About</a>
            <a href="#contact" class="nav-link">Contact</a>
        </nav>
        <div class="header-actions">
            <a href="cart.php" class="cart-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align: middle; margin-right: 6px;">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                Cart <span class="cart-badge"><?php echo $cart_count; ?></span>
            </a>
            <span class="user-greeting">Hi, <?php echo htmlspecialchars($user_name); ?></span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </header>
    
    <div class="main-content">
        <div class="landing-container">
            <!-- Hero Section -->
            <section class="hero-section">
                <h2 class="hero-title">Artisan Baked Goods</h2>
                <p class="hero-subtitle">Handcrafted with premium ingredients, delivered warm to your door</p>
                <a href="#menu" class="hero-cta">Browse Menu</a>
            </section>
            
            <!-- Menu Section -->
            <section class="menu-section" id="menu">
                <h3 class="section-title">Today's Favorites</h3>
                <p class="section-subtitle">Our most popular handcrafted pastries</p>
                
                <?php if ($products_result->num_rows > 0): ?>
                    <div class="pastry-cards">
                        <?php 
                        $card_index = 0;
                        while ($product = $products_result->fetch_assoc()): 
                            $card_index++;
                            // Determine badge based on stock level
                            $show_badge = false;
                            $badge_text = '';
                            $badge_class = '';
                            
                            if ($card_index === 1) {
                                $show_badge = true;
                                $badge_text = 'Best Seller';
                                $badge_class = '';
                            } elseif ($product['quantity'] <= 5) {
                                $show_badge = true;
                                $badge_text = 'Low Stock';
                                $badge_class = 'low-stock';
                            } elseif ($card_index === 2) {
                                $show_badge = true;
                                $badge_text = 'New';
                                $badge_class = 'new';
                            }
                            
                            // Use uploaded image if available, otherwise use placeholder
                            if (!empty($product['image_url']) && file_exists($product['image_url'])) {
                                $image_url = htmlspecialchars($product['image_url']);
                            } else {
                                $image_url = 'pastry' . (($card_index % 3) + 1) . '.png';
                            }
                        ?>
                            <div class="pastry-card">
                                <?php if ($show_badge): ?>
                                    <div class="card-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></div>
                                <?php endif; ?>
                                
                                <div class="card-image-wrapper">
                                    <img src="<?php echo $image_url; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="card-image">
                                </div>
                                
                                <div class="card-content">
                                    <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="card-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                    
                                    <div class="card-footer">
                                        <span class="card-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <button class="card-button add-to-cart" 
                                                data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                                data-price="<?php echo $product['price']; ?>">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-products">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="96" height="96" fill="currentColor" style="opacity: 0.3;">
                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                            </svg>
                        </div>
                        <h3>No Products Available</h3>
                        <p>Check back soon! Our bakers are working on new delicious treats.</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- About Section -->
            <section class="about-section" id="about">
                <h3 class="section-title">About Loafly</h3>
                <div class="about-content">
                    <p class="about-text">
                        At Loafly, we believe in the art of traditional baking combined with modern flavors. 
                        Every pastry is handcrafted with premium ingredients, baked fresh daily, and delivered 
                        with care. Our passion for perfection means you get only the finest artisan baked goods.
                    </p>
                </div>
                <div class="about-stats">
                    <div class="stat-item">
                        <span class="stat-number">5+</span>
                        <span class="stat-label">Years of Excellence</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Unique Recipes</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">Happy Customers</span>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="contact-section" id="contact">
                <h3 class="section-title">Get In Touch</h3>
                <p class="section-subtitle">We'd love to hear from you</p>
                <div class="contact-grid">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" fill="var(--color-primary)">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                        <h4>Visit Us</h4>
                        <p>123 Bakery Lane<br>Sweet City, SC 12345</p>
                        <a href="#" class="contact-link">Get Directions</a>
                    </div>
                    <div class="contact-card">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" fill="var(--color-primary)">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                        </div>
                        <h4>Call Us</h4>
                        <p>Monday - Saturday<br>8:00 AM - 6:00 PM</p>
                        <a href="tel:+15551234567" class="contact-link">(555) 123-4567</a>
                    </div>
                    <div class="contact-card">
                        <div class="contact-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="48" height="48" fill="var(--color-primary)">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                        <h4>Email Us</h4>
                        <p>We respond within<br>24 hours</p>
                        <a href="mailto:hello@loafly.com" class="contact-link">hello@loafly.com</a>
                    </div>
                </div>
            </section>
        </div>
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
        
        // Add to Cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const itemName = this.getAttribute('data-name');
                const itemPrice = this.getAttribute('data-price');
                
                // Disable button temporarily
                this.disabled = true;
                this.textContent = 'Adding...';
                
                // Send to server
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `item_name=${encodeURIComponent(itemName)}&item_price=${itemPrice}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart badge
                        document.querySelector('.cart-badge').textContent = data.cart_count;
                        
                        // Change button text
                        this.textContent = 'Added! ✓';
                        this.classList.add('added');
                        
                        // Reset after 2 seconds
                        setTimeout(() => {
                            this.textContent = 'Add to Cart';
                            this.classList.remove('added');
                            this.disabled = false;
                        }, 2000);
                    } else {
                        alert('Error adding to cart');
                        this.disabled = false;
                        this.textContent = 'Add to Cart';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.disabled = false;
                    this.textContent = 'Add to Cart';
                });
            });
        });
    </script>
</body>
</html>