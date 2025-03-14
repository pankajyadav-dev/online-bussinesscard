<?php
$host = "localhost";
$dbname = "business_card_db";
$username = "root"; 
$password = ""; 
$base_dir = dirname(__DIR__);

define('DB_CONNECTION_ESTABLISHED', true);
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?> 