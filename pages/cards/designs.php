<?php
// Define base URL for assets
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';
require_once '../../config/config.php';

// Check if user is logged in
if(!isLoggedIn()) {
    setMessage("You must be logged in to access this page.", "error");
    header("Location: " . BASE_URL . "pages/auth/login.php");
    exit;
}

// Get the category filter if provided
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

// Get all card designs from database
if(!empty($category_filter)) {
    $stmt = $pdo->prepare("SELECT * FROM card_designs WHERE category = ? ORDER BY name");
    $stmt->execute([$category_filter]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM card_designs ORDER BY category, name");
    $stmt->execute();
}

$designs = $stmt->fetchAll();

// Group designs by category
$categories = [];
foreach($designs as $design) {
    if(!isset($categories[$design['category']])) {
        $categories[$design['category']] = [];
    }
    $categories[$design['category']][] = $design;
}
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Choose a Card Design</h2>
        <a href="<?php echo url('pages/profile/dashboard.php'); ?>" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    <?php if(!empty($category_filter)): ?>
        <div class="mb-6">
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                <i class="fas fa-times mr-1"></i> Clear Filter
            </a>
            <span class="ml-2 text-gray-600">Showing designs in the <strong><?php echo $category_filter; ?></strong> category</span>
        </div>
    <?php else: ?>
        <div class="mb-6 flex flex-wrap gap-2">
            <span class="text-gray-600 py-2">Filter by category:</span>
            <?php 
            $categories_list = ['Professional', 'Creative', 'Minimalist', 'Corporate'];
            foreach($categories_list as $cat): 
            ?>
                <a href="?category=<?php echo $cat; ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300"><?php echo $cat; ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if(count($designs) > 0): ?>
        <?php foreach($categories as $category => $cat_designs): ?>
            <?php if(empty($category_filter)): ?>
                <h3 class="text-xl font-bold mb-4 mt-8"><?php echo $category; ?> Designs</h3>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php foreach($cat_designs as $design): ?>
                    <div class="border rounded-lg overflow-hidden shadow-md hover:shadow-lg transition">
                        <div class="h-48 bg-blue-600 flex items-center justify-center">
                            <span class="text-white text-lg font-bold"><?php echo $design['name']; ?></span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-lg mb-2"><?php echo $design['name']; ?></h4>
                            <p class="text-gray-600 mb-4">Category: <?php echo $design['category']; ?></p>
                            <a href="<?php echo url('pages/cards/create.php?design_id=' . $design['id']); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Use This Design
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-8 bg-white rounded-lg shadow">
            <p class="text-gray-600 mb-4">No designs found in the selected category.</p>
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Show All Designs
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?> 