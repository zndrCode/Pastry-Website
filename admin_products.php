<?php
require_once 'config.php';

/**
 * Resize image to maximum dimensions while maintaining aspect ratio
 * @param string $file_path Path to the image file
 * @param int $max_width Maximum width in pixels
 * @param int $max_height Maximum height in pixels
 * @return bool Success status
 */
function resizeImage($file_path, $max_width = 800, $max_height = 600) {
    // Get image info
    $image_info = getimagesize($file_path);
    if (!$image_info) {
        return false;
    }
    
    list($width, $height, $type) = $image_info;
    
    // Check if resize is needed
    if ($width <= $max_width && $height <= $max_height) {
        return true; // Image is already smaller, no resize needed
    }
    
    // Calculate new dimensions maintaining aspect ratio
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = round($width * $ratio);
    $new_height = round($height * $ratio);
    
    // Create image resource based on type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($file_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($file_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($file_path);
            break;
        case IMAGETYPE_WEBP:
            // Check if WebP is supported
            if (function_exists('imagecreatefromwebp')) {
                $source = imagecreatefromwebp($file_path);
            } else {
                // WebP not supported, skip resize
                return true;
            }
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Create new image
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Resize
    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Save resized image
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($new_image, $file_path, 85); // 85% quality
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($new_image, $file_path, 6); // Compression level 6
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($new_image, $file_path);
            break;
        case IMAGETYPE_WEBP:
            // Check if WebP is supported
            if (function_exists('imagewebp')) {
                $success = imagewebp($new_image, $file_path, 85); // 85% quality
            } else {
                $success = true; // Skip save, keep original
            }
            break;
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($new_image);
    
    return $success;
}

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

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    // Validate inputs
    $product_name = trim($_POST['product_name']);
    $product_description = trim($_POST['product_description']);
    $product_price = floatval($_POST['product_price']);
    $product_quantity = intval($_POST['product_quantity']);
    $existing_image = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    
    // Validation
    $errors = [];
    if (empty($product_name)) {
        $errors[] = "Product name is required.";
    }
    if (empty($product_description)) {
        $errors[] = "Product description is required.";
    }
    if ($product_price <= 0) {
        $errors[] = "Product price must be greater than 0.";
    }
    if ($product_quantity < 0) {
        $errors[] = "Product quantity cannot be negative.";
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header("Location: admin_dashboard.php");
        exit();
    }
    
    // Handle image upload
    $image_path = $existing_image; // Keep existing image by default
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $file_type = $_FILES['product_image']['type'];
        $file_size = $_FILES['product_image']['size'];
        $file_tmp = $_FILES['product_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        
        // Validate file type
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            header("Location: admin_dashboard.php");
            exit();
        }
        
        // Validate file size
        if ($file_size > $max_size) {
            $_SESSION['error_message'] = "File size exceeds 5MB limit.";
            header("Location: admin_dashboard.php");
            exit();
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = 'uploads/products/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $new_filename = uniqid('product_') . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Note: Automatic resizing is disabled (GD library not available)
            // To enable: Install GD library and uncomment the line below
            // $resized = resizeImage($upload_path, 800, 600);
            
            // Delete old image if updating and new image uploaded
            if ($action == 'edit' && !empty($existing_image) && file_exists($existing_image)) {
                unlink($existing_image);
            }
            $image_path = $upload_path;
        } else {
            $_SESSION['error_message'] = "Failed to upload image.";
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    
    // Add new product
    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $product_name, $product_description, $product_price, $product_quantity, $image_path);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Product added successfully!";
        } else {
            $_SESSION['error_message'] = "Error adding product: " . $conn->error;
        }
        $stmt->close();
    }
    
    // Edit existing product
    elseif ($action == 'edit') {
        $product_id = intval($_POST['product_id']);
        
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssdisi", $product_name, $product_description, $product_price, $product_quantity, $image_path, $product_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Product updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating product: " . $conn->error;
        }
        $stmt->close();
    }
    
    header("Location: admin_dashboard.php");
    exit();
}

// If accessed directly, redirect to dashboard
header("Location: admin_dashboard.php");
exit();
?>
