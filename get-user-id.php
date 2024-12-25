<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in (this assumes you store the user_id in the session)
if (isset($_SESSION['user_id'])) {
    // User is logged in, return the user ID
    $user_id = $_SESSION['user_id'];
    echo json_encode(['userId' => $user_id]);
} else {
    // User is not logged in, return null
    echo json_encode(['userId' => null]);
}
?>
