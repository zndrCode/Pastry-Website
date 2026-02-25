<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    
    // Delete the item from cart
    $delete_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $item_id, $user_id);
    
    if ($delete_stmt->execute()) {
        // Get updated cart count
        $count_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $cart_count = $count_data['total'] ?? 0;
        $count_stmt->close();
        
        // Get updated cart total
        $total_stmt = $conn->prepare("SELECT SUM(item_price * quantity) as total FROM cart WHERE user_id = ?");
        $total_stmt->bind_param("i", $user_id);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_data = $total_result->fetch_assoc();
        $cart_total = $total_data['total'] ?? 0;
        $total_stmt->close();
        
        echo json_encode([
            'success' => true, 
            'cart_count' => $cart_count,
            'cart_total' => number_format($cart_total, 2)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing item']);
    }
    
    $delete_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>