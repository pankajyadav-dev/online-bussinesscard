<?php

$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';
require_once '../../config/database.php';

if (!isLoggedIn()) {
    setMessage("You must be logged in to access this page.", "error");
    header("Location: /pages/auth/login.php");
    exit;
}

$errors = [];
$password_errors = [];
$otp_requested = isset($_SESSION['otp_requested']) && $_SESSION['otp_requested'] === true;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['update_profile'])) {
        $name = sanitizeInput($_POST['name']);
        $phone = sanitizeInput($_POST['phone']);
        $company = sanitizeInput($_POST['company']);
        $job_title = sanitizeInput($_POST['job_title']);
        $address = sanitizeInput($_POST['address']);
        $website = sanitizeInput($_POST['website']);
        
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET name = ?, phone = ?, company = ?, job_title = ?, address = ?, website = ? 
                    WHERE id = ?
                ");
                
                $result = $stmt->execute([$name, $phone, $company, $job_title, $address, $website, $_SESSION['user_id']]);
                
                if ($result) {
                    setMessage("Your profile has been updated successfully.", "success");
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                } else {
                    $errors[] = "Failed to update profile. Please try again.";
                }
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['change_password'])) {
        $currentpassword = sanitizeInput($_POST['current_password']);
        $newpassword = sanitizeInput($_POST['new_password']);
        $confirmpassword = sanitizeInput($_POST['confirm_password']);
        $password_errors = []; 
        
        if ($newpassword !== $confirmpassword) {
            $password_errors[] = "New password and confirm password do not match";
        }
        
        if (!password_verify($currentpassword, $user['password'])) {
            $password_errors[] = "Current password is incorrect";
        }
        
        if (empty($password_errors)) {
            try {
                $hashnewpassword = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $passchangeresult = $stmt->execute([$hashnewpassword, $_SESSION['user_id']]);
                if ($passchangeresult) {
                    setMessage("Your password has been updated successfully.", "success");
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                } else {
                    $password_errors[] = "Failed to update password. Please try again.";
                }
            } catch (PDOException $e) {
                $password_errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
    if (isset($_POST['delete_account'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            session_destroy();
            header("Location: dashboard.php"); 
            exit;
        } catch (PDOException $e) {
            $errors[] = "Failed to delete account: " . $e->getMessage();
        }
    }
}
?>

<div class="max-w-4xl mx-auto py-6 px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Account Settings</h2>
        <a href="/pages/profile/dashboard.php" class="text-blue-600 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['message']; ?></span>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-bold mb-4">Profile Information</h3>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="update_profile" value="1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                    <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <p class="text-gray-500 text-sm mt-1">Email cannot be changed</p>
                </div>
                
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="mb-4">
                    <label for="company" class="block text-gray-700 font-medium mb-2">Company</label>
                    <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['company'] ?? ''); ?>">
                </div>
                
                <div class="mb-4">
                    <label for="job_title" class="block text-gray-700 font-medium mb-2">Job Title</label>
                    <input type="text" id="job_title" name="job_title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['job_title'] ?? ''); ?>">
                </div>
                
                <div class="mb-4">
                    <label for="website" class="block text-gray-700 font-medium mb-2">Website</label>
                    <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                <textarea id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Profile</button>
        </form>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4">Change Password</h3>
        
        <?php if (!empty($password_errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($password_errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="mb-4">
        <input type="hidden" name="change_password" value="1">
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
            
            <button type="submit" name="change_password" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Change Password
            </button>
        </form>
    </div>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-bold mb-4 text-red-600">Delete Account</h3>
        <p class="text-gray-700 mb-4">Warning: This action is irreversible. Deleting your account will permanently remove your data.</p>
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account permanently? This action cannot be undone.');">
        <input type="hidden" name="delete_account" value="1">
            <button type="submit" name="delete_account" class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                Delete Account
            </button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
