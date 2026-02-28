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

// Fetch all products (only in-stock items)
$products_query = "SELECT * FROM products WHERE quantity > 0 ORDER BY created_at DESC";
$products_result = $conn->query($products_query);

// Buffer products for split layout (featured + list)
$all_products = [];
if ($products_result && $products_result->num_rows > 0) {
    $idx = 0;
    while ($row = $products_result->fetch_assoc()) {
        $img_index = ($idx % 3) + 1;
        $row['_image'] = (!empty($row['image_url']) && file_exists($row['image_url']))
            ? htmlspecialchars($row['image_url'])
            : 'pastry' . $img_index . '.png';
        $row['_index'] = $idx;
        $all_products[] = $row;
        $idx++;
    }
}

$featured   = $all_products[0] ?? null;
$menu_items = array_slice($all_products, 1);

// Time-based greeting
$hour = (int)date('H');
if ($hour < 12)      $greeting = "Good Morning";
elseif ($hour < 17)  $greeting = "Good Afternoon";
else                 $greeting = "Good Evening";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly — Menu</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="dashboard-body">

    <!-- Header -->
    <header class="dash-header">
        <a href="#" class="logo dash-logo">Loafly</a>

        <nav class="dash-nav">
            <a href="#menu" class="dash-nav-link">Menu</a>
        </nav>

        <div class="dash-actions">
            <a href="cart.php" class="cart-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="17" height="17" fill="currentColor">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                Cart
                <span class="cart-badge"><?php echo $cart_count; ?></span>
            </a>
            <span class="dash-greeting">Hi, <?php echo htmlspecialchars($user_name); ?></span>
            <a href="logout.php" class="dash-logout">Logout</a>
        </div>
    </header>

    <!-- Greeting Bar -->
    <div class="greeting-bar">
        <div class="greeting-left">
            <span class="greeting-wave">👋</span>
            <span class="greeting-text"><?php echo $greeting; ?>, <strong><?php echo htmlspecialchars($user_name); ?></strong>! Here's what's fresh today.</span>
        </div>
        <div class="greeting-pills">
            <span class="greeting-pill">
                <span class="pill-dot pill-green"></span>
                Baked Fresh Today
            </span>
            <a href="cart.php" class="greeting-pill greeting-pill-link">
                🛒 <?php echo $cart_count; ?> item<?php echo $cart_count != 1 ? 's' : ''; ?> in cart
            </a>
        </div>
    </div>

    <main class="dash-main" id="menu">

        <?php if (!empty($all_products)): ?>

        <!-- Featured Product -->
        <?php if ($featured): ?>
        <?php
            $f_low   = $featured['quantity'] <= 5;
            $f_badge = $f_low ? 'Low Stock' : 'Today\'s Pick';
            $f_cls   = $f_low ? 'badge-low' : 'badge-pick';
        ?>
        <section class="featured-section">
            <div class="section-eyebrow-row">
                <span class="section-eyebrow">Featured</span>
            </div>
            <div class="featured-card">
                <div class="featured-content">
                    <span class="featured-badge <?php echo $f_cls; ?>"><?php echo $f_badge; ?></span>
                    <h2 class="featured-name"><?php echo htmlspecialchars($featured['name']); ?></h2>
                    <p class="featured-desc"><?php echo htmlspecialchars($featured['description']); ?></p>
                    <div class="featured-meta">
                        <span class="featured-price">$<?php echo number_format($featured['price'], 2); ?></span>
                        <span class="featured-stock"><?php echo $featured['quantity']; ?> left</span>
                    </div>
                    <button class="featured-cta add-to-cart"
                            data-name="<?php echo htmlspecialchars($featured['name']); ?>"
                            data-price="<?php echo $featured['price']; ?>">
                        Add to Cart
                    </button>
                </div>
                <div class="featured-image-wrap">
                    <img src="<?php echo $featured['_image']; ?>"
                         alt="<?php echo htmlspecialchars($featured['name']); ?>"
                         class="featured-img">
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Menu List -->
        <?php if (!empty($menu_items)): ?>
        <section class="menu-section">
            <div class="section-eyebrow-row">
                <span class="section-eyebrow">Full Menu</span>
                <h2 class="menu-heading">Today's Selection</h2>
                <p class="menu-subhead">All items baked fresh every morning</p>
            </div>

            <div class="menu-list">
                <?php foreach ($menu_items as $i => $item):
                    $badge_text  = '';
                    $badge_class = '';
                    if ($item['quantity'] <= 5) {
                        $badge_text  = 'Low Stock';
                        $badge_class = 'item-badge-low';
                    } elseif ($i === 0) {
                        $badge_text  = 'New';
                        $badge_class = 'item-badge-new';
                    }
                ?>
                <div class="menu-item fade-item" style="--item-delay: <?php echo $i * 0.06; ?>s">
                    <div class="item-thumb">
                        <img src="<?php echo $item['_image']; ?>"
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php if ($badge_text): ?>
                        <span class="item-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="item-info">
                        <h3 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="item-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                        <span class="item-qty"><?php echo $item['quantity']; ?> available</span>
                    </div>
                    <div class="item-action">
                        <span class="item-price">$<?php echo number_format($item['price'], 2); ?></span>
                        <button class="item-add-btn add-to-cart"
                                data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                data-price="<?php echo $item['price']; ?>">
                            + Add
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php elseif ($featured): ?>
        <!-- Only one product exists - show a note -->
        <section class="menu-section">
            <p class="only-one-note">More items coming soon — check back tomorrow!</p>
        </section>
        <?php endif; ?>

        <?php else: ?>
        <!-- Empty State -->
        <section class="empty-section">
            <div class="empty-card">
                <div class="empty-icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="64" height="64">
                        <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                    </svg>
                </div>
                <h3 class="empty-title">Nothing in the Oven Yet</h3>
                <p class="empty-sub">Our bakers are working on fresh treats — check back soon!</p>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; 2026 Loafly. All rights reserved. Made with love and flour.</p>
    </footer>

    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Staggered list item fade-in
        const fadeObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.08 });
        document.querySelectorAll('.fade-item').forEach(el => fadeObserver.observe(el));

        // Add to Cart
        function handleAddToCart(button, cartBadgeSelector) {
            const itemName  = button.getAttribute('data-name');
            const itemPrice = button.getAttribute('data-price');
            const origText  = button.textContent;

            button.disabled = true;
            button.textContent = '...';

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_name=${encodeURIComponent(itemName)}&item_price=${itemPrice}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.cart-badge').textContent = data.cart_count;
                    document.querySelector('.greeting-pill-link').textContent =
                        `🛒 ${data.cart_count} item${data.cart_count != 1 ? 's' : ''} in cart`;
                    button.textContent = '✓ Added';
                    button.classList.add('btn-added');
                    setTimeout(() => {
                        button.textContent = origText;
                        button.classList.remove('btn-added');
                        button.disabled = false;
                    }, 2000);
                } else {
                    alert('Error adding to cart');
                    button.disabled = false;
                    button.textContent = origText;
                }
            })
            .catch(() => {
                button.disabled = false;
                button.textContent = origText;
            });
        }

        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() { handleAddToCart(this); });
        });
    </script>
</body>
</html>
