<?php
// Define base URL for assets
$base_url = '../../';

session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the home page
header("Location: /index.php");
exit;
?> 