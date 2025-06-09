<?php
require_once('../../backend/config/config.php'); // Database connection


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the book ID and quantity to restock from the form
    $bookId = $_POST['book_id'];
    $restockQuantity = $_POST['restockQuantity'];

    // Update the stock in the books table
    $stmt = $conn->prepare("UPDATE books SET Stock = Stock + ? WHERE Book_ID = ?");
    $stmt->bind_param("ii", $restockQuantity, $bookId);

    if ($stmt->execute()) {
        echo "Successfully restocked {$restockQuantity} units.";

        // Fetch users who have reserved this book
        $reservationStmt = $conn->prepare("SELECT r.Account_ID, a.Email FROM reservations r
                                           JOIN register a ON r.Account_ID = a.Register_ID
                                           WHERE r.Book_ID = ? AND r.Status = 'pending'");
        $reservationStmt->bind_param("i", $bookId);
        $reservationStmt->execute();
        $reservationResult = $reservationStmt->get_result();

        // Send email notification to each user who has reserved the book
        while ($reservation = $reservationResult->fetch_assoc()) {
            $userEmail = $reservation['Email'];
            $subject = "Book Available for Rent";
            $message = "Good news! The book you reserved is now available for rent. You can rent it now through the site.";

            // Send email
            if (mail($userEmail, $subject, $message)) {
                echo "Email sent to {$userEmail}.<br>";
            } else {
                echo "Failed to send email to {$userEmail}.<br>";
            }
        }

        // Close reservation query
        $reservationStmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
