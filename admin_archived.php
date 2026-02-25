<?php
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : ($_SESSION['user_id'] == 1);
if (!$is_admin) {
    header("Location: dashboard.php");
    exit();
}

// ── RESTORE action ──────────────────────────────────────────────────────────
if (isset($_GET['restore'])) {
    $archive_id = intval($_GET['restore']);

    $sel = $conn->prepare("SELECT * FROM archived_products WHERE id = ?");
    $sel->bind_param("i", $archive_id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();

    if ($row) {
        // Re-insert into products (new auto-increment ID)
        $ins = $conn->prepare(
            "INSERT INTO products (name, description, price, quantity, image_url, created_at)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $ins->bind_param(
            "ssdiss",
            $row['name'],
            $row['description'],
            $row['price'],
            $row['quantity'],
            $row['image_url'],
            $row['original_created_at']
        );

        if ($ins->execute()) {
            // Remove from archive
            $del = $conn->prepare("DELETE FROM archived_products WHERE id = ?");
            $del->bind_param("i", $archive_id);
            $del->execute();
            $del->close();
            $_SESSION['success_message'] = "Product \"{$row['name']}\" has been restored to the store!";
        } else {
            $_SESSION['error_message'] = "Failed to restore product. Please try again.";
        }
        $ins->close();
    } else {
        $_SESSION['error_message'] = "Archived product not found.";
    }

    header("Location: admin_archived.php");
    exit();
}

// ── PERMANENT DELETE action ─────────────────────────────────────────────────
if (isset($_GET['purge'])) {
    $archive_id = intval($_GET['purge']);

    $sel = $conn->prepare("SELECT name FROM archived_products WHERE id = ?");
    $sel->bind_param("i", $archive_id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();

    if ($row) {
        $del = $conn->prepare("DELETE FROM archived_products WHERE id = ?");
        $del->bind_param("i", $archive_id);
        if ($del->execute()) {
            $_SESSION['success_message'] = "Product \"{$row['name']}\" permanently deleted.";
        } else {
            $_SESSION['error_message'] = "Failed to permanently delete product.";
        }
        $del->close();
    } else {
        $_SESSION['error_message'] = "Archived product not found.";
    }

    header("Location: admin_archived.php");
    exit();
}

// ── Messages ────────────────────────────────────────────────────────────────
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message   = isset($_SESSION['error_message'])   ? $_SESSION['error_message']   : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// ── Fetch archived products ─────────────────────────────────────────────────
$archived_result = $conn->query(
    "SELECT ap.*, u.full_name as deleted_by_name
     FROM archived_products ap
     LEFT JOIN users u ON ap.deleted_by = u.id
     ORDER BY ap.deleted_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Products - Loafly Admin</title>
    <link rel="stylesheet" href="css/shared.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="logo">Loafly</h1>
                <span class="admin-badge">Admin</span>
            </div>

            <nav class="header-nav">
                <a href="admin_dashboard.php" class="header-nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                        <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                    </svg>
                    Products
                </a>
                <a href="admin_archived.php" class="header-nav-link active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                        <path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/>
                    </svg>
                    Archived
                </a>
            </nav>

            <div class="header-right">
                <a href="dashboard.php" class="nav-link">View Store</a>
                <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- ===== MAIN WRAPPER ===== -->
    <div class="admin-wrapper">

        <!-- Page Intro -->
        <div class="page-intro">
            <p class="page-intro-eyebrow">Archive</p>
            <h2>Archived Products</h2>
            <p>Products removed from the store. You can restore or permanently delete them.</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- ===== TABLE SECTION ===== -->
        <div class="table-section">
            <div class="table-toolbar">
                <div class="table-toolbar-left">
                    <h2>Archive Records</h2>
                    <p>
                        <?php echo $archived_result->num_rows; ?>
                        <?php echo $archived_result->num_rows === 1 ? 'product' : 'products'; ?> archived
                    </p>
                </div>
                <a href="admin_dashboard.php" class="btn-back-to-store">
                    ← Back to Products
                </a>
            </div>

            <div class="products-table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Orig. ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Archived Date</th>
                            <th>Archived By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($archived_result->num_rows > 0): ?>
                            <?php while ($product = $archived_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="product-image-cell">
                                        <?php if (!empty($product['image_url']) && file_exists($product['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                 class="product-thumbnail archived-thumbnail">
                                        <?php else: ?>
                                            <div class="product-no-image">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="product-id">#<?php echo htmlspecialchars($product['original_product_id']); ?></td>
                                    <td class="product-name"><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="product-price">$<?php echo number_format($product['price'], 2); ?></td>
                                    <td class="product-quantity"><?php echo htmlspecialchars($product['quantity']); ?></td>
                                    <td class="archive-date">
                                        <?php echo date('M j, Y', strtotime($product['deleted_at'])); ?>
                                        <span class="archive-time"><?php echo date('g:i A', strtotime($product['deleted_at'])); ?></span>
                                    </td>
                                    <td class="product-name">
                                        <?php echo $product['deleted_by_name']
                                            ? htmlspecialchars($product['deleted_by_name'])
                                            : 'Admin #' . $product['deleted_by']; ?>
                                    </td>
                                    <td class="product-actions">
                                        <button class="btn-action btn-restore"
                                            onclick="confirmRestore(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>')">
                                            Restore
                                        </button>
                                        <button class="btn-action btn-danger"
                                            onclick="confirmPurge(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="no-products">
                                    <div class="empty-state">
                                        <span class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="64" height="64" fill="currentColor">
                                                <path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/>
                                            </svg>
                                        </span>
                                        <p>No archived products. Deleted products will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /.admin-wrapper -->

    <script>
        function confirmRestore(archiveId, productName) {
            if (confirm(`Restore "${productName}" back to the active store?`)) {
                window.location.href = `admin_archived.php?restore=${archiveId}`;
            }
        }

        function confirmPurge(archiveId, productName) {
            if (confirm(`Permanently delete "${productName}"? This cannot be undone.`)) {
                window.location.href = `admin_archived.php?purge=${archiveId}`;
            }
        }
    </script>
</body>
</html>
