# LOAFLY ADMIN DASHBOARD - SETUP GUIDE

## 📋 Files Created

1. **admin_dashboard.php** - Main admin interface with product listing
2. **admin_products.php** - Backend handler for CRUD operations
3. **css/admin.css** - Styling for admin panel
4. **database_setup.sql** - Database schema and sample data

---

## 🚀 Installation Steps

### Step 1: Run the Database Setup

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Select your database: `loafly_db`
3. Click on the **SQL** tab
4. Copy and paste the contents of `database_setup.sql`
5. Click **Go** to execute

This will create:
- A `products` table with all necessary columns
- Sample product data to get you started

### Step 2: Upload Files to Your Project

Copy these files to your Loafly project directory:

```
loafly/
├── admin_dashboard.php     (NEW)
├── admin_products.php      (NEW)
├── database_setup.sql      (NEW - for reference)
└── css/
    └── admin.css          (NEW)
```

### Step 3: Set Up Admin Access

**Option A: Simple Method (Temporary)**
The current code makes user_id = 1 the admin. If you're the first user, you're automatically the admin!

**Option B: Add Admin Column (Recommended)**

1. Run this SQL to add admin column to users table:
```sql
ALTER TABLE `users` ADD COLUMN `is_admin` TINYINT(1) DEFAULT 0;
```

2. Make your user an admin:
```sql
UPDATE `users` SET `is_admin` = 1 WHERE `id` = 1;
```

3. Update `admin_dashboard.php` and `admin_products.php` (line ~10):

Change from:
```php
$is_admin = ($_SESSION['user_id'] == 1);
```

To:
```php
// Check if user has admin privileges
$admin_check = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$admin_check->bind_param("i", $_SESSION['user_id']);
$admin_check->execute();
$result = $admin_check->get_result();
$user = $result->fetch_assoc();
$is_admin = ($user && $user['is_admin'] == 1);
$admin_check->close();
```

---

## 🎯 How to Access the Admin Dashboard

1. **Login to your account** (must be user_id = 1 or have is_admin = 1)
2. Navigate to: `http://localhost/loafly/admin_dashboard.php`
3. You should see the admin interface!

---

## ✨ Features Included

### ✅ Full CRUD Functionality

**CREATE (Add Products)**
- Click "Add New Product" button
- Fill in: Product Name, Description, Price, Quantity
- All fields are required
- Price validation (must be > 0)
- Quantity validation (cannot be negative)

**READ (View Products)**
- Table displays all products with:
  - Product ID
  - Name
  - Description (truncated)
  - Price (formatted)
  - Quantity
  - Stock Status (In Stock / Out of Stock)
  - Actions (Edit/Delete)

**UPDATE (Edit Products)**
- Click "Edit" button on any product
- Modify any field
- Save changes

**DELETE (Remove Products)**
- Click "Delete" button
- Confirmation dialog appears
- Product is permanently deleted

### 🎨 UI Features

- **Responsive Design** - Works on desktop, tablet, and mobile
- **Modal Forms** - Clean popup forms for add/edit
- **Status Badges** - Visual indicators for stock status
- **Empty State** - Friendly message when no products exist
- **Success/Error Messages** - User feedback for all actions
- **Smooth Animations** - Professional transitions
- **Keyboard Support** - Close modal with Escape key

---

## 🔗 Updating the Customer Dashboard

To show products from the database instead of hardcoded items, update `dashboard.php`:

Replace the hardcoded pastry cards section with:

```php
<?php
// Fetch products from database
$products_query = "SELECT * FROM products WHERE quantity > 0 ORDER BY created_at DESC LIMIT 6";
$products_result = $conn->query($products_query);
?>

<div class="pastry-cards">
    <?php while ($product = $products_result->fetch_assoc()): ?>
        <div class="pastry-card">
            <?php if ($product['quantity'] < 10): ?>
                <div class="card-badge">Low Stock</div>
            <?php endif; ?>
            
            <div class="card-image-wrapper">
                <!-- You can add image support later -->
                <img src="<?php echo $product['image_url'] ?? 'pastry1.png'; ?>" 
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
```

---

## 🛠️ Customization Options

### Change Color Scheme
Edit `css/admin.css` and modify the CSS variables in `shared.css`

### Add Product Images
1. Add image upload field to the form
2. Process image upload in `admin_products.php`
3. Store filename in `image_url` column

### Add More Admin Features
- Order management
- User management
- Analytics dashboard
- Inventory alerts
- Sales reports

---

## 🐛 Troubleshooting

**Problem: "Access Denied" when trying to access admin**
- Make sure you're logged in as user_id = 1
- OR add the is_admin column and set it to 1 for your user

**Problem: Products table doesn't exist**
- Run the `database_setup.sql` file in phpMyAdmin

**Problem: CSS not loading**
- Make sure `css/admin.css` is in the correct folder
- Check file permissions

**Problem: Cannot delete/edit products**
- Check that your session is active
- Verify database connection in config.php

---

## 📝 Database Schema Reference

```sql
products table:
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR 255, NOT NULL)
- description (TEXT, NOT NULL)
- price (DECIMAL 10,2, NOT NULL)
- quantity (INT, NOT NULL, DEFAULT 0)
- image_url (VARCHAR 255, NULLABLE)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## 🎉 You're All Set!

Your admin dashboard is ready to use! You can now:
- ✅ Add new pastry products
- ✅ Edit existing products
- ✅ Update prices and quantities
- ✅ Delete products
- ✅ Manage inventory

For questions or issues, check the troubleshooting section above!
