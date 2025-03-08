<?php
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

// Check if design_id is provided
if(!isset($_GET['design_id']) || empty($_GET['design_id'])) {
    setMessage("Please select a design first.", "error");
    header("Location: " . BASE_URL . "pages/cards/designs.php");
    exit;
}

$design_id = (int)$_GET['design_id'];

// Get design information
$stmt = $pdo->prepare("SELECT * FROM card_designs WHERE id = ?");
$stmt->execute([$design_id]);

if($stmt->rowCount() == 0) {
    setMessage("Invalid design selected.", "error");
    header("Location: " . BASE_URL . "pages/cards/designs.php");
    exit;
}

$design = $stmt->fetch();

// Get user information for pre-filling the form
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

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
    
    // If no errors, save the card
    if(empty($errors)) {
        // Prepare custom fields as JSON
        $custom_fields = json_encode([
            'name' => $name,
            'job_title' => $job_title,
            'company' => $company,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'address' => $address
        ]);
        
        // Insert into database
        $stmt = $pdo->prepare("
            INSERT INTO user_cards (user_id, design_id, card_name, custom_fields) 
            VALUES (?, ?, ?, ?)
        ");
        
        if($stmt->execute([$_SESSION['user_id'], $design_id, $card_name, $custom_fields])) {
            $card_id = $pdo->lastInsertId();
            setMessage("Your business card has been created successfully.", "success");
            header("Location: " . BASE_URL . "pages/cards/view.php?id=" . $card_id);
            exit;
        } else {
            $errors[] = "Failed to create business card. Please try again.";
        }
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Create New Business Card</h2>
        <a href="<?php echo url('pages/cards/designs.php'); ?>" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Designs
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
            <div class="md:w-1/3">
                <h3 class="text-xl font-bold mb-4">Selected Design</h3>
                <div class="border rounded-lg overflow-hidden shadow-md mb-4">
                    <div class="h-48 bg-blue-600 flex items-center justify-center">
                        <span class="text-white text-lg font-bold"><?php echo $design['name']; ?></span>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-lg"><?php echo $design['name']; ?></h4>
                        <p class="text-gray-600">Category: <?php echo $design['category']; ?></p>
                    </div>
                </div>
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
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?design_id=' . $design_id); ?>">
                    <div class="mb-4">
                        <label for="card_name" class="block text-gray-700 font-medium mb-2">Card Name <span class="text-red-500">*</span></label>
                        <input type="text" id="card_name" name="card_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($card_name) ? $card_name : 'My Business Card'; ?>" required>
                        <p class="text-gray-500 text-sm mt-1">This name is for your reference only</p>
                    </div>
                    
                    <hr class="my-6">
                    
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($name) ? $name : $user['name']; ?>" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="job_title" class="block text-gray-700 font-medium mb-2">Job Title</label>
                            <input type="text" id="job_title" name="job_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($job_title) ? $job_title : $user['job_title']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="company" class="block text-gray-700 font-medium mb-2">Company</label>
                            <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($company) ? $company : $user['company']; ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($phone) ? $phone : $user['phone']; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($email) ? $email : $user['email']; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="website" class="block text-gray-700 font-medium mb-2">Website</label>
                        <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($website) ? $website : $user['website']; ?>">
                    </div>
                    
                    <div class="mb-6">
                        <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                        <textarea id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"><?php echo isset($address) ? $address : $user['address']; ?></textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Create Business Card</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 