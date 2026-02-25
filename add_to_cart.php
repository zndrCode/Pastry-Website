<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_name = trim($_POST['item_name']);
    $item_price = floatval($_POST['item_price']);
    
    // Check if item already exists in cart
    $check_stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND item_name = ?");
    $check_stmt->bind_param("is", $user_id, $item_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $row['id']);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Insert new item
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, item_name, item_price, quantity) VALUES (?, ?, ?, 1)");
        $insert_stmt->bind_param("isd", $user_id, $item_name, $item_price);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $check_stmt->close();
    
    // Get updated cart count
    $count_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_data = $count_result->fetch_assoc();
    $cart_count = $count_data['total'] ?? 0;
    $count_stmt->close();
    
    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>