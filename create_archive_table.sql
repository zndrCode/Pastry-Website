-- ================================================
-- PRODUCT ARCHIVE SYSTEM - DATABASE SETUP
-- ================================================

-- Create archived_products table
CREATE TABLE IF NOT EXISTS `archived_products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `original_product_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `quantity` INT NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `deleted_by` INT NOT NULL,
  `deleted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `original_created_at` TIMESTAMP NULL,
  `reason` VARCHAR(255) DEFAULT 'Deleted by admin',
  INDEX `idx_original_product_id` (`original_product_id`),
  INDEX `idx_deleted_by` (`deleted_by`),
  INDEX `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- VERIFICATION
-- ================================================
-- Check if table was created successfully:
DESCRIBE archived_products;

-- Expected structure:
-- | Field               | Type          | Null | Key | Default             | Extra          |
-- |---------------------|---------------|------|-----|---------------------|----------------|
-- | id                  | int           | NO   | PRI | NULL                | auto_increment |
-- | original_product_id | int           | NO   | MUL | NULL                |                |
-- | name                | varchar(255)  | NO   |     | NULL                |                |
-- | description         | text          | NO   |     | NULL                |                |
-- | price               | decimal(10,2) | NO   |     | NULL                |                |
-- | quantity            | int           | NO   |     | NULL                |                |
-- | image_url           | varchar(255)  | YES  |     | NULL                |                |
-- | deleted_by          | int           | NO   | MUL | NULL                |                |
-- | deleted_at          | timestamp     | YES  | MUL | CURRENT_TIMESTAMP   |                |
-- | original_created_at | timestamp     | YES  |     | NULL                |                |
-- | reason              | varchar(255)  | YES  |     | Deleted by admin    |                |

-- ================================================
-- OPTIONAL: View archived products
-- ================================================
-- To see all archived products:
-- SELECT * FROM archived_products ORDER BY deleted_at DESC;

-- To see who deleted what:
-- SELECT 
--     ap.name AS product_name,
--     ap.price,
--     u.full_name AS deleted_by,
--     ap.deleted_at
-- FROM archived_products ap
-- LEFT JOIN users u ON ap.deleted_by = u.id
-- ORDER BY ap.deleted_at DESC;

-- ================================================
-- DONE!
-- ================================================
-- Your archive system is ready!
-- Deleted products will now be saved in archived_products table.
