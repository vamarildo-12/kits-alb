<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'web';

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Start session to check if the user is logged in
session_start();

// Verify if the user is logged in by checking the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the logged-in user_id from the session
$user_id = $_SESSION['user_id'];

// Get the product_id and quantity from the request
$requestPayload = json_decode(file_get_contents('php://input'), true);
$product_id = $requestPayload['product_id'] ?? null;
$quantity_to_remove = $requestPayload['quantity'] ?? null;

// Validate input
if (!$product_id || !$quantity_to_remove || !is_numeric($quantity_to_remove) || $quantity_to_remove <= 0) {
    echo json_encode(['error' => 'Invalid product ID or quantity']);
    exit;
}

// First, check if the user has enough items of this product in their cart
$countSql = "SELECT COUNT(*) as total FROM shopping_cart 
             WHERE user_id = ? AND product_id = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param('is', $user_id, $product_id);
$countStmt->execute();
$result = $countStmt->get_result();
$row = $result->fetch_assoc();
$total_items = $row['total'];
$countStmt->close();

if ($total_items < $quantity_to_remove) {
    echo json_encode(['error' => 'Not enough items in cart to remove']);
    exit;
}

// Delete the specified number of most recently added items
$deleteSql = "DELETE FROM shopping_cart 
              WHERE id IN (
                  SELECT id FROM (
                      SELECT id 
                      FROM shopping_cart 
                      WHERE user_id = ? AND product_id = ? 
                      ORDER BY created_at DESC 
                      LIMIT ?
                  ) as temp
              )";

$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param('isi', $user_id, $product_id, $quantity_to_remove);
$success = $deleteStmt->execute();
$affected_rows = $deleteStmt->affected_rows;
$deleteStmt->close();

if ($success && $affected_rows > 0) {
    // Get remaining quantity
    $remainingSql = "SELECT COUNT(*) as remaining FROM shopping_cart 
                    WHERE user_id = ? AND product_id = ?";
    $remainingStmt = $conn->prepare($remainingSql);
    $remainingStmt->bind_param('is', $user_id, $product_id);
    $remainingStmt->execute();
    $result = $remainingStmt->get_result();
    $row = $result->fetch_assoc();
    $remaining_quantity = $row['remaining'];
    $remainingStmt->close();

    echo json_encode([
        'status' => 'Product removed from cart',
        'removed_quantity' => $quantity_to_remove,
        'remaining_quantity' => $remaining_quantity
    ]);
} else {
    echo json_encode(['error' => 'Failed to remove products from cart']);
}

$conn->close();
?>