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

$conn->begin_transaction();

try {
    // Get the current cart count (locking the cart record if necessary)
    $query_count = "SELECT COUNT(*) AS total_items FROM shopping_cart WHERE user_id = ?";
    $stmt_count = $conn->prepare($query_count);
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $result = $stmt_count->get_result();
    $row = $result->fetch_assoc();
    $response['cart_count'] = (int)$row['total_items'];  // Count the total rows

    // Commit the transaction (if no errors)
    $conn->commit();
} catch (Exception $e) {
    // In case of error, rollback the transaction
    $conn->rollback();
    $response['cart_count'] = 0;
}

// Close connection
$conn->close();

// Log the response data for debugging
error_log("Response data: " . print_r($response, true));

// Echo the response back
echo json_encode($response);
?>
