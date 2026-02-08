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
                🛒 Cart <span class="cart-badge"><?php echo $cart_count; ?></span>
            </a>
            <span class="user-greeting">Hi, <?php echo htmlspecialchars($user_name); ?>!</span>
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
                                        <div class="price-stock">
                                            <span class="card-price">$<?php echo number_format($product['price'], 2); ?></span>
                                            <span class="stock-info"><?php echo $product['quantity']; ?> available</span>
                                        </div>
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
                        <div class="empty-icon">🥐</div>
                        <h3>No Products Available</h3>
                        <p>Check back soon! Our bakers are working on new delicious treats.</p>
                    </div>
                <?php endif; ?>
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
            
            <!-- Contact Section -->
            <section class="contact-section" id="contact">
                <h3 class="section-title">Visit Our Bakery</h3>
                <div class="contact-grid">
                    <div class="contact-card">
                        <div class="contact-icon">📍</div>
                        <h4>Location</h4>
                        <p>123 Baker Street<br>Tangub City, Northern Mindanao<br>Philippines</p>
                        <a href="https://maps.google.com" target="_blank" class="contact-link">Get Directions →</a>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">🕐</div>
                        <h4>Hours</h4>
                        <p>Mon - Fri: 6:00 AM - 8:00 PM<br>Saturday: 7:00 AM - 9:00 PM<br>Sunday: 7:00 AM - 9:00 PM</p>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">📞</div>
                        <h4>Contact</h4>
                        <p>Phone: (123) 456-7890<br>Email: hello@loafly.com</p>
                        <a href="tel:1234567890" class="contact-link">Call Us →</a>
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
