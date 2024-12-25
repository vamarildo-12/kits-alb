<?php
session_start(); // Start the session

// Destroy the session to log the user out
session_unset();
session_destroy();

// Redirect to the homepage or login page
header("Location: index.php"); // Redirect to homepage or login page
exit;
?>
