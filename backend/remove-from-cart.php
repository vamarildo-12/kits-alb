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
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Start session to check if the user is logged in
session_start();

// Verify if the user is logged in by checking the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Get the logged-in user_id from the session
$user_id = $_SESSION['user_id'];

// Get the product_id from the request
$requestPayload = json_decode(file_get_contents('php://input'), true);
$product_id = $requestPayload['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'error' => 'Product ID is required']);
    exit;
}

// Fetch the current count (quantity) of the product in the cart for the logged-in user
$sql = "SELECT COUNT(*) AS product_count FROM shopping_cart WHERE product_id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $product_id, $user_id); // Changed to 's' for varchar product_id
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to check product count']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->bind_result($product_count);
$stmt->fetch();
$stmt->close();

if ($product_count <= 0) {
    echo json_encode(['success' => false, 'error' => 'Product not found in cart for the user']);
    $conn->close();
    exit;
}

// Remove all instances of the product from the cart for the logged-in user
$deleteSql = "DELETE FROM shopping_cart WHERE product_id = ? AND user_id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param('si', $product_id, $user_id); // Changed to 's' for varchar product_id
$deleteResult = $deleteStmt->execute();
$affectedRows = $deleteStmt->affected_rows;
$deleteStmt->close();

// Check if the delete operation was successful
if ($deleteResult && $affectedRows > 0) {
    // Get the current cart total after deletion
    $countSql = "SELECT COUNT(*) as total FROM shopping_cart WHERE user_id = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param('i', $user_id);
    $countStmt->execute();
    $result = $countStmt->get_result();
    $row = $result->fetch_assoc();
    $new_total = $row['total'];
    $countStmt->close();
    
    echo json_encode([
        'success' => true,
        'action' => 'deleted_all',
        'quantity' => 0,
        'cart_total' => $new_total
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete product from cart']);
}

$conn->close();
?>