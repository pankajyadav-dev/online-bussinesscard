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

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Process form submission to update profile
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Profile update form
    if(isset($_POST['update_profile'])) {
        $name = sanitizeInput($_POST['name']);
        $phone = sanitizeInput($_POST['phone']);
        $company = sanitizeInput($_POST['company']);
        $job_title = sanitizeInput($_POST['job_title']);
        $address = sanitizeInput($_POST['address']);
        $website = sanitizeInput($_POST['website']);
        
        // Validate inputs
        $errors = [];
        
        if(empty($name)) {
            $errors[] = "Name is required";
        }
        
        // If no errors, update profile
        if(empty($errors)) {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET name = ?, phone = ?, company = ?, job_title = ?, address = ?, website = ? 
                WHERE id = ?
            ");
            
            if($stmt->execute([$name, $phone, $company, $job_title, $address, $website, $_SESSION['user_id']])) {
                setMessage("Your profile has been updated successfully.", "success");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
    }
    
    // Password change form
    if(isset($_POST['change_password'])) {
        $current_password = sanitizeInput($_POST['current_password']);
        $new_password = sanitizeInput($_POST['new_password']);
        $confirm_password = sanitizeInput($_POST['confirm_password']);
        
        // Validate inputs
        $password_errors = [];
        
        if(empty($current_password)) {
            $password_errors[] = "Current password is required";
        }
        
        if(empty($new_password)) {
            $password_errors[] = "New password is required";
        } elseif(strlen($new_password) < 6) {
            $password_errors[] = "New password must be at least 6 characters";
        }
        
        if($new_password !== $confirm_password) {
            $password_errors[] = "New passwords do not match";
        }
        
        // Verify current password
        if(empty($password_errors)) {
            if(!password_verify($current_password, $user['password'])) {
                $password_errors[] = "Current password is incorrect";
            } else {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                
                if($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                    setMessage("Your password has been changed successfully.", "success");
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $password_errors[] = "Failed to change password. Please try again.";
                }
            }
        }
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Account Settings</h2>
        <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">Profile Information</h3>
        
        <?php if(isset($errors) && !empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $user['name']; ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                    <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" value="<?php echo $user['email']; ?>" disabled>
                    <p class="text-gray-500 text-sm mt-1">Email cannot be changed</p>
                </div>
                
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $user['phone']; ?>">
                </div>
                
                <div class="mb-4">
                    <label for="company" class="block text-gray-700 font-medium mb-2">Company</label>
                    <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $user['company']; ?>">
                </div>
                
                <div class="mb-4">
                    <label for="job_title" class="block text-gray-700 font-medium mb-2">Job Title</label>
                    <input type="text" id="job_title" name="job_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $user['job_title']; ?>">
                </div>
                
                <div class="mb-4">
                    <label for="website" class="block text-gray-700 font-medium mb-2">Website</label>
                    <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $user['website']; ?>">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                <textarea id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"><?php echo $user['address']; ?></textarea>
            </div>
            
            <button type="submit" name="update_profile" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Profile</button>
        </form>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">Change Password</h3>
        
        <?php if(isset($password_errors) && !empty($password_errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach($password_errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="current_password" class="block text-gray-700 font-medium mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 font-medium mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p class="text-gray-500 text-sm mt-1">Password must be at least 6 characters</p>
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <button type="submit" name="change_password" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Change Password</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?> 