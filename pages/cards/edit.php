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

// Get card information
$stmt = $pdo->prepare("
    SELECT uc.*, cd.name as design_name, cd.template_path, cd.category
    FROM user_cards uc 
    JOIN card_designs cd ON uc.design_id = cd.id 
    WHERE uc.id = ?
");
$stmt->execute([$card_id]);

if($stmt->rowCount() == 0) {
    setMessage("Card not found.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

$card = $stmt->fetch();

// Check if the user owns this card
if($card['user_id'] != $_SESSION['user_id']) {
    setMessage("You don't have permission to edit this card.", "error");
    header("Location: /pages/profile/dashboard.php");
    exit;
}

// Parse the custom fields
$custom_fields = json_decode($card['custom_fields'], true);

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_name = sanitizeInput($_POST['card_name']);
    $name = sanitizeInput($_POST['name']);
    $job_title = sanitizeInput($_POST['job_title']);
    $company = sanitizeInput($_POST['company']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $website = sanitizeInput($_POST['website']);
    $address = sanitizeInput($_POST['address']);
    
    // Validate inputs
    $errors = [];
    
    if(empty($card_name)) {
        $errors[] = "Card name is required";
    }
    
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
    // If no errors, update the card
    if(empty($errors)) {
        // Prepare custom fields as JSON
        $updated_custom_fields = json_encode([
            'name' => $name,
            'job_title' => $job_title,
            'company' => $company,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'address' => $address
        ]);
        
        // Update in database
        $stmt = $pdo->prepare("
            UPDATE user_cards 
            SET card_name = ?, custom_fields = ? 
            WHERE id = ? AND user_id = ?
        ");
        
        if($stmt->execute([$card_name, $updated_custom_fields, $card_id, $_SESSION['user_id']])) {
            setMessage("Your business card has been updated successfully.", "success");
            header("Location: /pages/cards/view.php?id=" . $card_id);
            exit;
        } else {
            $errors[] = "Failed to update business card. Please try again.";
        }
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Edit Business Card</h2>
        <div>
            <a href="/pages/cards/view.php?id=<?php echo $card_id; ?>" class="text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Back to Card
            </a>
            <a href="/pages/profile/dashboard.php" class="ml-4 text-blue-600 hover:underline">
                <i class="fas fa-th-large mr-1"></i> Dashboard
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="md:w-1/3">
                <h3 class="text-xl font-bold mb-4">Card Design</h3>
                <div class="border rounded-lg overflow-hidden shadow-md mb-4">
                    <?php 
                    // Different background colors based on design category
                    $bg_color = "bg-blue-600";
                    if($card['category'] == 'Creative') {
                        $bg_color = "bg-pink-500";
                    } elseif($card['category'] == 'Minimalist') {
                        $bg_color = "bg-gray-800";
                    } elseif($card['category'] == 'Corporate') {
                        $bg_color = "bg-gray-600";
                    }
                    ?>
                    
                    <div class="h-48 <?php echo $bg_color; ?> flex items-center justify-center">
                        <span class="text-white text-lg font-bold"><?php echo $card['design_name']; ?></span>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-lg"><?php echo $card['design_name']; ?></h4>
                        <p class="text-gray-600">Category: <?php echo $card['category']; ?></p>
                    </div>
                </div>
                <p class="text-gray-600 text-sm">
                    <i class="fas fa-info-circle mr-1"></i> To change the card design, create a new card with your preferred design.
                </p>
            </div>
            
            <div class="md:w-2/3">
                <h3 class="text-xl font-bold mb-4">Card Information</h3>
                
                <?php if(isset($errors) && !empty($errors)): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                        <ul class="list-disc list-inside">
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $card_id); ?>">
                    <div class="mb-4">
                        <label for="card_name" class="block text-gray-700 font-medium mb-2">Card Name <span class="text-red-500">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $card['card_name']; ?>" required>
                        <p class="text-gray-500 text-sm mt-1">This name is for your reference only</p>
                    </div>
                    
                    <hr class="my-6">
                    
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['name']; ?>" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="job_title" class="block text-gray-700 font-medium mb-2">Job Title</label>
                            <input type="text" id="job_title" name="job_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['job_title']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="company" class="block text-gray-700 font-medium mb-2">Company</label>
                            <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['company']; ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['phone']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['email']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="website" class="block text-gray-700 font-medium mb-2">Website</label>
                        <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $custom_fields['website']; ?>">
                    </div>
                    
                    <div class="mb-6">
                        <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"><?php echo $custom_fields['address']; ?></textarea>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Business Card</button>
                        <a href="/pages/cards/view.php?id=<?php echo $card_id; ?>" class="bg-gray-200 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-300">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 