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

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['item_price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loafly - Your Cart</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <header class="landing-header">
        <h1 class="logo">Loafly</h1>
        <div class="header-actions">
            <span class="user-greeting">Hi, <?php echo htmlspecialchars($user_name); ?>!</span>
            <a href="dashboard.php" class="logout-link">Continue Shopping</a>
        </div>
    </header>
    
    <div class="cart-container">
        <a href="dashboard.php" class="back-link">← Back to Menu</a>
        
        <div class="cart-header">
            <h1 class="cart-title">Your Cart</h1>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <p>Your cart is empty</p>
                <a href="dashboard.php" class="hero-cta">Browse Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-items" id="cartItems">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-item-id="<?php echo $item['id']; ?>">
                        <div class="item-info">
                            <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                            <p class="item-price">$<?php echo number_format($item['item_price'], 2); ?> each</p>
                            <p class="item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                        <div class="item-actions">
                            <div class="item-total">
                                <strong>$<?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></strong>
                            </div>
                            <button class="remove-btn" data-item-id="<?php echo $item['id']; ?>" data-item-name="<?php echo htmlspecialchars($item['item_name']); ?>">
                                Remove
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery Fee</span>
                    <span>$2.50</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span id="grandTotal">$<?php echo number_format($total + 2.50, 2); ?></span>
                </div>
                <button class="checkout-button" onclick="alert('Checkout feature coming soon!')">
                    Proceed to Checkout
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <footer class="site-footer">
        <p>&copy; 2026 Loafly. All rights reserved.</p>
    </footer>
    
    <script>
        // Remove from cart functionality
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const itemName = this.getAttribute('data-item-name');
                
                if (!confirm(`Remove "${itemName}" from cart?`)) {
                    return;
                }
                
                // Disable button
                this.disabled = true;
                this.textContent = 'Removing...';
                
                // Send remove request
                fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `item_id=${itemId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove item from DOM with animation
                        const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
                        cartItem.style.opacity = '0';
                        cartItem.style.transform = 'translateX(-100%)';
                        
                        setTimeout(() => {
                            cartItem.remove();
                            
                            // Update totals
                            const subtotal = parseFloat(data.cart_total);
                            const deliveryFee = 2.50;
                            const grandTotal = subtotal + deliveryFee;
                            
                            document.getElementById('subtotal').textContent = `$${data.cart_total}`;
                            document.getElementById('grandTotal').textContent = `$${grandTotal.toFixed(2)}`;
                            
                            // Check if cart is empty
                            if (data.cart_count === 0) {
                                location.reload(); // Reload to show empty cart message
                            }
                        }, 300);
                    } else {
                        alert('Error removing item from cart');
                        this.disabled = false;
                        this.textContent = 'Remove';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item from cart');
                    this.disabled = false;
                    this.textContent = 'Remove';
                });
            });
        });
    </script>
</body>
</html>