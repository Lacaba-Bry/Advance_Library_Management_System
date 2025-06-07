<?php
session_start();
require_once '../phpmailer/vendor/autoload.php';  // Path to PHPMailer autoloader
require_once './config/config.php';  // Your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // Get email from POST request

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM accountlist WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Generate a random 6-digit code
        $code = mt_rand(100000, 999999);

        // Store the code temporarily in the session
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_email'] = $email;

        // Send the verification code via email using PHPMailer
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bryan2025xx@gmail.com'; // Replace with your email           
            $mail->Password = 'vqnc hjlp kuqu dhmg'; // Replace with your email password (or use app password)            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Haven Library');
            $mail->addAddress($email);  // Send email to the user

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body    = "Your verification code for resetting your password is: <b>$code</b>";

            // Send the email
            $mail->send();

            // Redirect to the verification page with the email address
            header('Location: verify-code.php?email=' . urlencode($email));
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        // If the email is not found
        echo 'Email not found. Please check your email address and try again.';
    }
}
?>
