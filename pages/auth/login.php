<?php
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';
require_once '../../config/config.php';

// Check if user is already logged in
if(isLoggedIn()) {
    header("Location: " . BASE_URL);
    exit;
}

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    
    // Validate inputs
    if(empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, name, email, password, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Verify password
            if(password_verify($password, $user['password'])) {
                // Check if user is verified
                if($user['is_verified']) {
                    // Set user as logged in
                    $_SESSION['user_id'] = $user['id'];
                    
                    // Set success message
                    setMessage("Welcome back, " . $user['name'] . "!", "success");
                    
                    // Redirect to dashboard
                    header("Location: " . BASE_URL . "pages/profile/dashboard.php");
                    exit;
                } else {
                    // User is not verified, send to verification page
                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['temp_email'] = $user['email'];
                    
                    setMessage("Your email is not verified. Please verify your email to continue.", "error");
                    
                    header("Location: " . BASE_URL . "pages/auth/verify.php");
                    exit;
                }
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md mt-8">
    <h2 class="text-2xl font-bold mb-6 text-center">Log In to Your Account</h2>
    
    <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($email) ? $email : ''; ?>" required>
        </div>
        
        <div class="mb-6">
            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Log In</button>
    </form>
    
    <p class="mt-4 text-center text-gray-600">
        Don't have an account? <a href="<?php echo url('pages/auth/register.php'); ?>" class="text-blue-600 hover:underline">Register</a>
    </p>
</div>

<?php require_once '../../includes/footer.php'; ?> 