<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "web"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit; // Stop the script if connection fails
}

// Start session to check if the user is logged in
session_start();

// Verify if the user is logged in by checking the session
if (!isset($_SESSION['user_id'])) {
    // If not logged in, send an error message
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit; // Stop the script if user is not logged in
}

// Get the logged-in user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch product details and quantities for the current user
$sql = "
    SELECT 
        p.id AS product_id,
        p.image AS product_image,
        p.name AS product_name,
        p.priceCents,
        COUNT(c.product_id) AS quantity,
        GROUP_CONCAT(DISTINCT c.size) AS sizes
    FROM products p
    JOIN shopping_cart c ON p.id = c.product_id
    WHERE c.user_id = ?  -- Select cart items for the current user
    GROUP BY c.product_id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Bind the user_id to the prepared statement
$stmt->execute();

$result = $stmt->get_result();

// Check if the query executed successfully
if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = [
        'productId' => $row['product_id'],
        'image' => $row['product_image'],
        'name' => $row['product_name'],
        'priceCents' => $row['priceCents'],
        'quantity' => $row['quantity'],
        'sizes' => explode(',', $row['sizes']) // Parse sizes as an array
    ];
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the cart data as JSON
echo json_encode($cartItems);
?>
