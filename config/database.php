<?php
// Database configuration
$host = "localhost";
$dbname = "business_card_db";
$username = "root"; // Change as needed
$password = ""; // Change as needed

$base_dir = dirname(__DIR__);

// Define a constant for database connection
define('DB_CONNECTION_ESTABLISHED', true);
// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?> 