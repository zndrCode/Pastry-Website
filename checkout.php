<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get cart items
$cart_stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? ORDER BY added_at DESC");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);
$cart_stmt->close();

// If cart is empty, redirect back
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['item_price'] * $item['quantity'];
}
$delivery_fee = 2.50;
$total = $subtotal + $delivery_fee;

// Handle form submission
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = trim($_POST['full_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $notes        = trim($_POST['notes'] ?? '');

    // Basic validation
    if (empty($full_name))  $errors[] = 'Full name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (empty($phone))      $errors[] = 'Phone number is required.';
    if (empty($address))    $errors[] = 'Delivery address is required.';
    if (empty($city))       $errors[] = 'City is required.';

    if (empty($errors)) {
        // In a real app you'd save the order to a DB here.
        // For now we just clear the cart and show success.
        $del_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $del_stmt->bind_param("i", $user_id);
        $del_stmt->execute();
        $del_stmt->close();
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Checkout</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>

    <!-- Header -->
    <header class="landing-header">
        <a href="dashboard.php" class="logo">Loafly</a>
        <div class="header-actions">
            <span class="user-greeting">Hi, <?php echo htmlspecialchars($user_name); ?></span>
            <a href="cart.php" class="logout-link">← Back to Cart</a>
        </div>
    </header>

    <?php if ($success): ?>
    <!-- ===== SUCCESS STATE ===== -->
    <div class="checkout-success-wrap">
        <div class="success-card">
            <div class="success-icon">🎉</div>
            <h2 class="success-title">Order Placed!</h2>
            <p class="success-msg">Thank you, <?php echo htmlspecialchars($user_name); ?>! Your pastries are on their way. We'll notify you once they're out for delivery.</p>
            <div class="success-summary">
                <span>Order Total</span>
                <strong>$<?php echo number_format($total, 2); ?></strong>
            </div>
            <a href="dashboard.php" class="hero-cta success-cta">Back to Menu</a>
        </div>
    </div>

    <?php else: ?>
    <!-- ===== CHECKOUT LAYOUT ===== -->
    <div class="checkout-wrap">

        <div class="checkout-left">
            <div class="checkout-section-label">Step 1 of 1</div>
            <h1 class="checkout-title">Checkout</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error checkout-alert">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="checkout.php" class="checkout-form" id="checkoutForm">

                <!-- Contact -->
                <div class="form-block">
                    <h3 class="form-block-title">
                        <span class="form-block-num">01</span> Contact Info
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name"
                                   placeholder="Juan dela Cruz"
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email"
                                   placeholder="juan@email.com"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               placeholder="+63 912 345 6789"
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                               required>
                    </div>
                </div>

                <!-- Delivery -->
                <div class="form-block">
                    <h3 class="form-block-title">
                        <span class="form-block-num">02</span> Delivery Details
                    </h3>
                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <input type="text" id="address" name="address"
                               placeholder="123 Rizal Street, Barangay San Miguel"
                               value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                               required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city"
                                   placeholder="Davao City"
                                   value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                   required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Order Notes <span class="label-optional">(optional)</span></label>
                        <textarea id="notes" name="notes" rows="3"
                                  placeholder="Any special instructions, landmark, or requests..."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Payment note -->
                <div class="form-block payment-block">
                    <h3 class="form-block-title">
                        <span class="form-block-num">03</span> Payment
                    </h3>
                    <div class="payment-cod">
                        <div class="cod-icon">💵</div>
                        <div>
                            <strong>Cash on Delivery</strong>
                            <p>Pay in cash when your order arrives at your door.</p>
                        </div>
                        <div class="cod-check">✓</div>
                    </div>
                </div>

                <button type="submit" class="place-order-btn" id="placeOrderBtn">
                    <span>Place Order</span>
                    <span class="btn-arrow">→</span>
                </button>

            </form>
        </div>

        <!-- ===== RIGHT: Order Summary ===== -->
        <aside class="checkout-right">
            <div class="order-summary-card">
                <h3 class="summary-heading">Order Summary</h3>

                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div class="summary-item-info">
                                <span class="summary-item-name"><?php echo htmlspecialchars($item['item_name']); ?></span>
                                <span class="summary-item-qty">× <?php echo $item['quantity']; ?></span>
                            </div>
                            <span class="summary-item-price">$<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-line">
                    <span>Delivery Fee</span>
                    <span>$<?php echo number_format($delivery_fee, 2); ?></span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total-line">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>

                <div class="summary-badge">
                    🛡️ Secure Checkout
                </div>
            </div>
        </aside>

    </div>
    <?php endif; ?>

    <footer class="site-footer">
        <p>&copy; 2026 Loafly. All rights reserved. Made with love and flour.</p>
    </footer>

    <script>
        // Button loading state on submit
        document.getElementById('checkoutForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('placeOrderBtn');
            btn.disabled = true;
            btn.innerHTML = '<span>Placing Order...</span>';
        });
    </script>
</body>
</html>
