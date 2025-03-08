<?php
require_once __DIR__ . '/../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Set a flash message
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Generate random string for verification code
function generateVerificationCode($length = 6) {
    return substr(str_shuffle("0123456789"), 0, $length);
}

// Send email using PHPMailer
function sendEmail($to, $subject, $body) {
    // Enable error display for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    // Check if composer autoload exists
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        error_log("PHPMailer autoload file not found at: " . $autoloadPath);
        return false;
    }
    
    require $autoloadPath;
    
    // Import required PHPMailer classes

    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 0; // Enable verbose debug output (0 for production)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Change to your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'siri963690@gmail.com'; // CHANGE THIS to your actual email
        $mail->Password   = 'npso otex mvuk cbhe'; // CHANGE THIS to your actual app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Business Card Creator'); // CHANGE THIS
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        error_log("Email sent successfully to: " . $to);
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email: " . $mail->ErrorInfo);
        return false;
    }
}
// function sendEmail($to, $subject, $body) {
//     require './vendor/autoload.php'; // Make sure you have PHPMailer installed via Composer
    
//     $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
//     try {
//         // Server settings
//         $mail->isSMTP();
//         $mail->Host       = 'smtp.gmail.com'; // Change to your SMTP server
//         $mail->SMTPAuth   = true;
//         $mail->Username   = 'siri963690@gmail.com'; // SMTP username
//         $mail->Password   = 'npso otex mvuk cbhe'; // SMTP password
//         $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port       = 587;
        
//         // Recipients
//         $mail->setFrom('businesscard@gmail.com', 'Business Card Creator');
//         $mail->addAddress($to);
        
//         // Content
//         $mail->isHTML(true);
//         $mail->Subject = $subject;
//         $mail->Body    = $body;
        
//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         return false;
//     }
// }

// Send OTP verification email
function sendVerificationEmail($email, $name, $code) {
    $subject = "Email Verification - Business Card Creator";
    $body = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #3b82f6;'>Business Card Creator - Email Verification</h2>
                <p>Hello $name,</p>
                <p>Thank you for registering with us. Please use the following verification code to complete your registration:</p>
                <div style='margin: 20px 0; padding: 15px; background-color: #f3f4f6; border-radius: 5px; text-align: center;'>
                    <h3 style='font-size: 24px; margin: 0;'>$code</h3>
                </div>
                <p>This code will expire in 10 minutes.</p>
                <p>If you did not request this verification, please ignore this email.</p>
                <p>Regards,<br>Business Card Creator Team</p>
            </div>
        </body>
        </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

// Generate QR code for a card
function generateQRCode($cardId) {
    // Return the URL that will be used to generate QR code in JavaScript
    global $base_url;
    return $base_url . "/pages/cards/view.php?id=" . $cardId . "&share=true";
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?> 