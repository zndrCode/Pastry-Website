<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check admin status from session or fallback to user_id check
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : ($_SESSION['user_id'] == 1);

if (!$is_admin) {
    header("Location: dashboard.php");
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    $admin_id = $_SESSION['user_id'];
    
    // Get product data before archiving
    $product_stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product_data = $product_result->fetch_assoc();
    $product_stmt->close();
    
    if ($product_data) {
        // Archive the product (copy to archived_products table)
        $archive_stmt = $conn->prepare("
            INSERT INTO archived_products 
            (original_product_id, name, description, price, quantity, image_url, deleted_by, original_created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $archive_stmt->bind_param(
            "issdisis",
            $product_data['id'],
            $product_data['name'],
            $product_data['description'],
            $product_data['price'],
            $product_data['quantity'],
            $product_data['image_url'],
            $admin_id,
            $product_data['created_at']
        );
        
        if ($archive_stmt->execute()) {
            // Now delete from products table
            $delete_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $delete_stmt->bind_param("i", $product_id);
            
            if ($delete_stmt->execute()) {
                // Note: We keep the image file for archive purposes
                // Uncomment below if you want to delete the image file:
                // if (!empty($product_data['image_url']) && file_exists($product_data['image_url'])) {
                //     unlink($product_data['image_url']);
                // }
                
                $_SESSION['success_message'] = "Product archived successfully!";
            } else {
                $_SESSION['error_message'] = "Error deleting product from active list.";
            }
            $delete_stmt->close();
        } else {
            $_SESSION['error_message'] = "Error archiving product.";
        }
        $archive_stmt->close();
    } else {
        $_SESSION['error_message'] = "Product not found.";
    }
    
    header("Location: admin_dashboard.php");
    exit();
}

// Get success/error messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Fetch all products
$products_query = "SELECT * FROM products ORDER BY created_at DESC";
$products_result = $conn->query($products_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Loafly</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Enhanced Dashboard Styles */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--color-brown);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 145, 72, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            border-color: var(--color-primary);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 145, 72, 0.15);
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--color-primary);
        }

        .stat-info h3 {
            font-size: 0.85rem;
            color: var(--color-gray-dark);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-cream);
            line-height: 1;
        }

        .stat-subtitle {
            font-size: 0.85rem;
            color: var(--color-tan);
            margin-top: 0.5rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header {
            margin-bottom: 2rem;
        }

        .section-header h2 {
            font-size: 1.75rem;
            color: var(--color-cream);
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: var(--color-gray-dark);
            font-size: 0.95rem;
        }

        /* Better table styling */
        .products-table-container {
            background: var(--color-brown);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 145, 72, 0.15);
        }

        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="header-content">
            <h1 class="logo">Loafly Admin</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="nav-link">View Store</a>
                <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="sidebar-link active">
                    <span class="link-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                        </svg>
                    </span>
                    <span>Products</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Section Header -->
            <div class="section-header">
                <h2>Dashboard Overview</h2>
                <p>Welcome back! Here's what's happening with your pastry store.</p>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Dashboard Stats -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Total Products</h3>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $products_result->num_rows; ?></div>
                    <p class="stat-subtitle">Active in your store</p>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>In Stock</h3>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php 
                        $in_stock = 0;
                        $temp_result = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity > 0");
                        if ($temp_result) {
                            $row = $temp_result->fetch_assoc();
                            $in_stock = $row['count'];
                        }
                        echo $in_stock;
                        ?>
                    </div>
                    <p class="stat-subtitle">Products available</p>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <h3>Out of Stock</h3>
                        </div>
                    </div>
                    <div class="stat-value">
                        <?php 
                        $out_of_stock = 0;
                        $temp_result = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity = 0");
                        if ($temp_result) {
                            $row = $temp_result->fetch_assoc();
                            $out_of_stock = $row['count'];
                        }
                        echo $out_of_stock;
                        ?>
                    </div>
                    <p class="stat-subtitle">Need restocking</p>
                </div>
            </div>

            <div class="page-header">
                <div>
                    <h2 class="page-title">Product Management</h2>
                    <p class="page-subtitle">Manage your pastry products and inventory</p>
                </div>
                <button class="btn-add-product" onclick="openAddModal()">
                    <span>+</span> Add New Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="products-table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products_result->num_rows > 0): ?>
                            <?php while ($product = $products_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="product-image-cell">
                                        <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Product" class="product-thumbnail">
                                        <?php else: ?>
                                            <div class="product-no-image">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="product-id">#<?php echo htmlspecialchars($product['id']); ?></td>
                                    <td class="product-name"><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="product-desc"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></td>
                                    <td class="product-price">$<?php echo number_format($product['price'], 2); ?></td>
                                    <td class="product-quantity"><?php echo htmlspecialchars($product['quantity']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $product['quantity'] > 0 ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo $product['quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                        </span>
                                    </td>
                                    <td class="product-actions">
                                        <button class="btn-action btn-edit" onclick='openEditModal(<?php echo json_encode($product); ?>)'>Edit</button>
                                        <button class="btn-action btn-delete" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="no-products">
                                    <div class="empty-state">
                                        <span class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="64" height="64" fill="currentColor">
                                                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                                            </svg>
                                        </span>
                                        <p>No products yet. Add your first product to get started!</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Product</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="productForm" action="admin_products.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="product_id">
                <input type="hidden" id="formAction" name="action" value="add">
                <input type="hidden" id="existingImage" name="existing_image">
                
                <div class="form-group">
                    <label for="productImage">Product Image</label>
                    <div class="image-upload-container">
                        <div class="image-preview" id="imagePreview">
                            <span class="preview-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="currentColor" style="display: block; margin: 0 auto 8px;">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                                Click to upload image
                            </span>
                            <img id="previewImg" style="display: none;">
                        </div>
                        <input type="file" id="productImage" name="product_image" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <p class="upload-hint">Recommended: 800x600px, Max 5MB (JPG, PNG, GIF)</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="productName">Product Name <span class="required">*</span></label>
                    <input type="text" id="productName" name="product_name" required placeholder="e.g., Classic Croissant">
                </div>

                <div class="form-group">
                    <label for="productDescription">Description <span class="required">*</span></label>
                    <textarea id="productDescription" name="product_description" required rows="4" placeholder="Describe your product..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="productPrice">Price ($) <span class="required">*</span></label>
                        <input type="number" id="productPrice" name="product_price" step="0.01" min="0" required placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="productQuantity">Quantity <span class="required">*</span></label>
                        <input type="number" id="productQuantity" name="product_quantity" min="0" required placeholder="0">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image Preview
        const imageInput = document.getElementById('productImage');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const previewPlaceholder = imagePreview.querySelector('.preview-placeholder');

        imagePreview.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    previewPlaceholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        // Modal Functions
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('existingImage').value = '';
            previewImg.style.display = 'none';
            previewPlaceholder.style.display = 'block';
            document.getElementById('productModal').style.display = 'flex';
        }

        function openEditModal(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productQuantity').value = product.quantity;
            document.getElementById('existingImage').value = product.image_url || '';
            
            // Show existing image
            if (product.image_url) {
                previewImg.src = product.image_url;
                previewImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            } else {
                previewImg.style.display = 'none';
                previewPlaceholder.style.display = 'block';
            }
            
            document.getElementById('productModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
            document.getElementById('productForm').reset();
            previewImg.style.display = 'none';
            previewPlaceholder.style.display = 'block';
        }

        function confirmDelete(productId, productName) {
            if (confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
                window.location.href = `admin_dashboard.php?delete=${productId}`;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>