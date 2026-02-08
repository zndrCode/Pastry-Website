<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields";
        header("Location: admin_login.php");
        exit();
    }
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password correct - now check if user is admin
            
            // Method 1: Check by user_id (user_id = 1 is admin)
            $is_admin = ($user['id'] == 1);
            
            // Method 2: If you added 'is_admin' column to users table, uncomment this instead:
            /*
            $admin_check = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
            $admin_check->bind_param("i", $user['id']);
            $admin_check->execute();
            $admin_result = $admin_check->get_result();
            $admin_data = $admin_result->fetch_assoc();
            $is_admin = ($admin_data && $admin_data['is_admin'] == 1);
            $admin_check->close();
            */
            
            // IMPORTANT: Only allow admin users to login through admin portal
            if ($is_admin) {
                // Create admin session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = true;
                
                // Redirect to admin dashboard
                header("Location: admin_dashboard.php");
                exit();
            } else {
                // Valid credentials but not an admin
                $_SESSION['error'] = "Access denied. This portal is for administrators only.";
                header("Location: admin_login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid credentials";
        }
    } else {
        $_SESSION['error'] = "Invalid credentials";
    }
    
    $stmt->close();
    header("Location: admin_login.php");
    exit();
}

$conn->close();
?>
