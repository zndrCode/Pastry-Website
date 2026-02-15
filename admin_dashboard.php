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
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="header-content">
            <h1 class="logo">Loafly Admin</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="nav-link">View Store</a>
                <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="sidebar-link active">
                    <span class="link-icon">📦</span>
                    <span>Products</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Product Management</h2>
                    <p class="page-subtitle">Manage your pastry products and inventory</p>
                </div>
                <button class="btn-add-product" onclick="openAddModal()">
                    <span>+</span> Add New Product
                </button>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

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
                                            <div class="product-no-image">📷</div>
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
                                        <span class="empty-icon">📦</span>
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
                            <span class="preview-placeholder">📷 Click to upload image</span>
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