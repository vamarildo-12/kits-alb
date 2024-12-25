<?php
session_start(); // Start the session

// Set the timeout duration (15 minutes)
$timeout_duration = 900; // 15 minutes in seconds

// Check if the session has started and whether the last activity time exists
if (isset($_SESSION['last_activity'])) {
    // Calculate the session's idle time
    $inactive_time = time() - $_SESSION['last_activity'];

    // If inactive for more than 15 minutes, log out the user
    if ($inactive_time > $timeout_duration) {
        session_unset(); // Unset session variables
        session_destroy(); // Destroy the session
        header("Location: index.php"); // Redirect to homepage or login page
        exit;
    }
}

// Update last activity time on every page load
$_SESSION['last_activity'] = time();
?>
