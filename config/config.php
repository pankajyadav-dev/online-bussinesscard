<?php
// Base URL Configuration
$base_url = 'http://localhost/'; // Change this to your domain in production

// Define constants for paths
define('BASE_URL', $base_url);
define('CSS_PATH', BASE_URL . 'css');
define('JS_PATH', BASE_URL . 'js'); 
define('ASSETS_PATH', BASE_URL . 'assets');

// Function to get URLs for different parts of the site
function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
?> 