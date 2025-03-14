<?php
require_once __DIR__ . '/../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function generateVerificationCode($length = 6) {
    return substr(str_shuffle("0123456789"), 0, $length);
}

function sendEmail($to, $subject, $body) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        error_log("PHPMailer autoload file not found at: " . $autoloadPath);
        return false;
    }
    
    require $autoloadPath;
    

    
    $mail = new PHPMailer(true);
    
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'siri963690@gmail.com'; 
        $mail->Password   = 'npso otex mvuk cbhe'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('your-email@gmail.com', 'Business Card Creator'); 
        $mail->addAddress($to);
        
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

function generateQRCode($cardId) {
    global $base_url;
    return $base_url . "/pages/cards/view.php?id=" . $cardId . "&share=true";
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?> 