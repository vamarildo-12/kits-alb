<?php
session_start();

// Set the content type to JSON
header('Content-Type: application/json');

// Prevent caching of the response
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: 0');
header('Pragma: no-cache');

// Check if the session contains 'user_id' to determine if the user is logged in
$response = [
    'isLoggedIn' => isset($_SESSION['user_id']),
    'message' => isset($_SESSION['user_id']) ? 'User is logged in' : 'User is not logged in'
];

// Output the JSON response
echo json_encode($response);
exit;
?>
