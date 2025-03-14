<?php
$base_url = '../../';

require_once '../../includes/header.php';
require_once '../../includes/functions.php';

if(isLoggedIn()) {
    header("Location: /index.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    
    $errors = [];
    
    if(empty($name)) {
        $errors[] = "Name is required";
    }
    
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->rowCount() > 0) {
        $errors[] = "Email already exists";
    }
    
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $verification_code = generateVerificationCode();
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, verification_code) VALUES (?, ?, ?, ?)");
        
        if($stmt->execute([$name, $email, $hashed_password, $verification_code])) {
            $user_id = $pdo->lastInsertId();
            
            if(sendVerificationEmail($email, $name, $verification_code)) {
                $_SESSION['temp_user_id'] = $user_id;
                $_SESSION['temp_email'] = $email;
                
                header("Location: verify.php");
                exit;
            } else {
                $errors[] = "Failed to send verification email. Please try again.";
            }
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md my-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
    
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
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Full Name</label>
            <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($name) ? $name : ''; ?>" required>
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($email) ? $email : ''; ?>" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <p class="text-gray-500 text-sm mt-1">Password must be at least 6 characters</p>
        </div>
        
        <div class="mb-6">
            <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Register</button>
    </form>
    
    <p class="mt-4 text-center text-gray-600">
        Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Log In</a>
    </p>
</div>

<?php require_once '../../includes/footer.php'; ?> 