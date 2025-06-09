<?php
session_start();
require_once '../phpmailer/vendor/autoload.php';  // Path to PHPMailer autoloader
require_once './config/config.php';  // Your database connection


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Get the book ID and restock quantity from the form
$bookId = $_POST['book_id'];
$restockQuantity = $_POST['restockQuantity'];

// Update the stock in the books table
$stmt = $conn->prepare("UPDATE books SET Stock = Stock + ? WHERE Book_ID = ?");
$stmt->bind_param("ii", $restockQuantity, $bookId);

if ($stmt->execute()) {
    echo "Successfully restocked {$restockQuantity} units.";

    // Fetch users who have reserved this book and have pending status
    $reservationStmt = $conn->prepare("SELECT r.Register_ID, a.Email 
                                       FROM reservations r
                                       JOIN register a ON r.Register_ID = a.Register_ID
                                       WHERE r.Book_ID = ? AND r.Status = 'pending'");
    $reservationStmt->bind_param("i", $bookId);
    $reservationStmt->execute();
    $reservationResult = $reservationStmt->get_result();

    // Create the PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Set up SMTP (configure your SMTP settings)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'bryan2025xx@gmail.com'; // SMTP username
        $mail->Password = 'vqnc hjlp kuqu dhmg'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Set email format to HTML
        $mail->isHTML(true);
        $mail->setFrom('bryan2025xx@gmail.com', 'Reservation Note');
        $mail->Subject = 'Book Available for Rent';

        // Loop through all reserved users and send them an email
        while ($reservation = $reservationResult->fetch_assoc()) {
            $userEmail = $reservation['Email'];
            
            // Set email body content
            $message = "Good news! The book you reserved is now available for rent. You can rent it now through the site.";

            $mail->addAddress($userEmail); // Add recipient

            // Set email body
            $mail->Body = $message;

            // Send the email
            if (!$mail->send()) {
                echo "Failed to send email to {$userEmail}.<br>";
            } else {
                echo "Email sent to {$userEmail}.<br>";
            }

            // Clear the recipient for the next email
            $mail->clearAddresses();
        }

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Close reservation query
    $reservationStmt->close();

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
