<?php 
$base_url = './';
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'config/config.php';
?>

<div class="bg-blue-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Create Professional Business Cards</h1>
        <p class="text-xl mb-8">Design, customize, and share your business cards in minutes</p>
        
        <?php if(!isLoggedIn()): ?>
            <div class="flex justify-center space-x-4">
                <a href="<?php echo url('pages/auth/register.php'); ?>" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition">Get Started</a>
                <a href="<?php echo url('pages/auth/login.php'); ?>" class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-bold hover:bg-white hover:text-blue-600 transition">Log In</a>
            </div>
        <?php else: ?>
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition">Create New Card</a>
        <?php endif; ?>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Create an Account</h3>
                <p class="text-gray-600">Sign up and verify your email to get started with our business card creator.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-pencil-alt text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Design Your Card</h3>
                <p class="text-gray-600">Choose from various templates and customize your business card with your information.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-share-alt text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Share with Everyone</h3>
                <p class="text-gray-600">Share your business card via email or QR code to connect with others.</p>
            </div>
        </div>
    </div>
</div>

<div class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Our Card Designs</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="h-48 bg-blue-600 flex items-center justify-center">
                    <span class="text-white text-lg font-bold">Professional Design</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Professional Templates</h3>
                    <p class="text-gray-600 mb-4">Perfect for corporate environments and formal business settings.</p>
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php?category=Professional'); ?>" class="text-blue-600 font-bold hover:underline">Browse Professional</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="h-48 bg-pink-500 flex items-center justify-center">
                    <span class="text-white text-lg font-bold">Creative Design</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Creative Templates</h3>
                    <p class="text-gray-600 mb-4">Stand out with unique and eye-catching business card designs.</p>
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php?category=Creative'); ?>" class="text-blue-600 font-bold hover:underline">Browse Creative</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="h-48 bg-gray-800 flex items-center justify-center">
                    <span class="text-white text-lg font-bold">Minimalist Design</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">Minimalist Templates</h3>
                    <p class="text-gray-600 mb-4">Clean and simple designs that focus on essential information.</p>
                    <?php if(isLoggedIn()): ?>
                        <a href="<?php echo url('pages/cards/designs.php?category=Minimalist'); ?>" class="text-blue-600 font-bold hover:underline">Browse Minimalist</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if(!isLoggedIn()): ?>
            <div class="text-center mt-12">
                <a href="pages/auth/register.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Get Started Now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 