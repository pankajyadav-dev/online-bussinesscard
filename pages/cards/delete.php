<?php
// Define base URL for assets
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';

// Check if user is logged in
if(!isLoggedIn()) {
    setMessage("You must be logged in to access this page.", "error");
    header("Location: /pages/auth/login.php");
    exit;
}

// Check if card ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage("Invalid card ID.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card_id = (int)$_GET['id'];

// Check if the card exists and belongs to the user
$stmt = $pdo->prepare("SELECT * FROM user_cards WHERE id = ? AND user_id = ?");
$stmt->execute([$card_id, $_SESSION['user_id']]);

if($stmt->rowCount() == 0) {
    setMessage("Card not found or you don't have permission to delete it.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

// Process deletion if confirmed
if(isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $stmt = $pdo->prepare("DELETE FROM user_cards WHERE id = ? AND user_id = ?");
    
    if($stmt->execute([$card_id, $_SESSION['user_id']])) {
        setMessage("Your business card has been deleted successfully.", "success");
    } else {
        setMessage("Failed to delete business card. Please try again.", "error");
    }
    
    header("Location: /pages/profile/dashboard.php");
    exit;
}

// Get card information for confirmation page
$stmt = $pdo->prepare("
    SELECT uc.*, cd.name as design_name
    FROM user_cards uc 
    JOIN card_designs cd ON uc.design_id = cd.id 
    WHERE uc.id = ?
");
$stmt->execute([$card_id]);
$card = $stmt->fetch();

// Parse the custom fields
$custom_fields = json_decode($card['custom_fields'], true);
?>

<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Delete Business Card</h2>
        <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
            <p class="font-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Warning</p>
            <p>You are about to delete the business card "<?php echo $card['card_name']; ?>". This action cannot be undone.</p>
        </div>
        
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-3">Card Details</h3>
            <ul class="list-disc list-inside text-gray-700">
                <li>Card Name: <?php echo $card['card_name']; ?></li>
                <li>Design: <?php echo $card['design_name']; ?></li>
                <li>Created: <?php echo date('F j, Y', strtotime($card['created_at'])); ?></li>
                <li>Name on Card: <?php echo $custom_fields['name']; ?></li>
                <li>Job Title: <?php echo $custom_fields['job_title']; ?></li>
                <li>Company: <?php echo $custom_fields['company']; ?></li>
            </ul>
        </div>
        
        <div class="flex space-x-4">
            <a href="?id=<?php echo $card_id; ?>&confirm=yes" class="bg-red-600 text-white py-2 px-6 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                <i class="fas fa-trash mr-1"></i> Delete Card
            </a>
            <a href="/pages/profile/dashboard.php" class="bg-gray-200 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-300">
                Cancel
            </a>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 