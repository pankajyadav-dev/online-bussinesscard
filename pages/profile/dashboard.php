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

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's cards
$stmt = $pdo->prepare("
    SELECT uc.*, cd.name as design_name, cd.category 
    FROM user_cards uc 
    JOIN card_designs cd ON uc.design_id = cd.id 
    WHERE uc.user_id = ? 
    ORDER BY uc.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cards = $stmt->fetchAll();
?>

<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">Welcome, <?php echo $user['name']; ?>!</h2>
            <p class="text-gray-600">Manage your business cards</p>
        </div>
        
        <div class="mt-4 md:mt-0">
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create New Card</a>
            <a href="<?php echo url('pages/profile/account.php'); ?>" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Edit Profile</a>
        </div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-xl font-bold mb-4">Your Business Cards</h3>
    
    <?php if(count($cards) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($cards as $card): ?>
                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                    <div class="h-40 bg-blue-600 flex items-center justify-center">
                        <span class="text-white text-lg font-bold"><?php echo $card['card_name']; ?></span>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold"><?php echo $card['card_name']; ?></h4>
                        <p class="text-gray-600 text-sm mb-3">Design: <?php echo $card['design_name']; ?></p>
                        
                        <div class="flex space-x-2">
                            <a href="<?php echo url('pages/cards/view.php?id=' . $card['id']); ?>" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            <a href="<?php echo url('pages/cards/edit.php?id=' . $card['id']); ?>" class="text-green-600 hover:underline text-sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <a href="<?php echo url('pages/cards/share.php?id=' . $card['id']); ?>" class="text-purple-600 hover:underline text-sm">
                                <i class="fas fa-share-alt mr-1"></i> Share
                            </a>
                            <a href="<?php echo url('pages/cards/delete.php?id=' . $card['id']); ?>" onclick="return confirm('Are you sure you want to delete this card?');" class="text-red-600 hover:underline text-sm">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <img src="<?php echo url('assets/empty-state.svg'); ?>" alt="No cards" class="w-64 mx-auto mb-4 opacity-50">
            <p class="text-gray-600 mb-4">You haven't created any business cards yet.</p>
            <a href="<?php echo url('pages/cards/designs.php'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Your First Card</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?> 