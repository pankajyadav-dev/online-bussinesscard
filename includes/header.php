<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Card Creator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/style.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="<?php echo BASE_URL; ?>/" class="text-xl font-bold text-blue-600">Business Card Creator</a>
            
            <div class="space-x-4">
                <?php if(isLoggedIn()): ?>
                    <a href="<?php echo url('pages/cards/designs.php'); ?>" class="text-gray-700 hover:text-blue-600">Card Designs</a>
                    <a href="<?php echo url('pages/profile/dashboard.php'); ?>" class="text-gray-700 hover:text-blue-600">My Cards</a>
                    <a href="<?php echo url('pages/profile/account.php'); ?>" class="text-gray-700 hover:text-blue-600">Profile</a>
                    <a href="<?php echo url('pages/auth/logout.php'); ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</a>
                <?php else: ?>
                    <a href="<?php echo url('pages/auth/login.php'); ?>" class="text-gray-700 hover:text-blue-600">Login</a>
                    <a href="<?php echo url('pages/auth/register.php'); ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    
    <main class="container mx-auto px-4 py-6">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-4 rounded <?php echo $_SESSION['message_type'] == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?> 
    </main>