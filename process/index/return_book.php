<?php
session_start();
require_once __DIR__ . '/../../backend/config/config.php'; // Adjust path as needed

error_log("return_book.php: Starting return process"); // Add this line

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("return_book.php: User not logged in");
    echo "<script>alert('You must be logged in to return a book.'); window.location.href='../../index.php';</script>";
    exit();
}

$accountId = $_SESSION['user_id'];

// Validate and sanitize the book_id
$bookId = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);
error_log("return_book.php: book_id from GET: " . $_GET['book_id']); // Log the raw value
error_log("return_book.php: Filtered book_id: " . $bookId); // Log the filtered value

if ($bookId === false || $bookId === null) {
    error_log("return_book.php: Invalid book ID");
    echo "<script>alert('Invalid book ID.'); window.location.href='../../profile.php';</script>";
    exit();
}

// Start a transaction to ensure data consistency
$conn->begin_transaction();

try {
    // 1. Check if the book is currently rented by the user (Status 'Ongoing')
    $checkQuery = $conn->prepare("SELECT * FROM rent WHERE Book_ID = ? AND Account_ID = ? AND Status = 'Ongoing'");
    $checkQuery->bind_param("ii", $bookId, $accountId);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult === false) {
        error_log("return_book.php: Error checking rental: " . $conn->error);
        $conn->rollback();
        echo "<script>alert('Database error. Please try again.'); window.location.href='../../profile.php';</script>";
        exit();
    }

    // If the book isn't found in the ongoing rentals, show an error
    if ($checkResult->num_rows === 0) {
        error_log("return_book.php: No ongoing rental found for book_id=" . $bookId . ", accountId=" . $accountId);
        $conn->rollback();
        echo "<script>alert('No ongoing rental found for this book.'); window.location.href='../../profile.php';</script>";
        exit();
    }

    // 2. Remove the book from the rentals table (mark as returned)
    $returnQuery = $conn->prepare("DELETE FROM rent WHERE Book_ID = ? AND Account_ID = ? AND Status = 'Ongoing'");
    $returnQuery->bind_param("ii", $bookId, $accountId);
    $returnQuery->execute();

    if ($returnQuery === false) {
        error_log("return_book.php: Error deleting rental: " . $conn->error);
        $conn->rollback();
        echo "<script>alert('Database error. Please try again.'); window.location.href='../../profile.php';</script>";
        exit();
    }

    // 3. Update the stock in the books table (increment the stock)
    $stockUpdateQuery = $conn->prepare("UPDATE books SET Stock = Stock + 1 WHERE Book_ID = ?");
    $stockUpdateQuery->bind_param("i", $bookId);
    $stockUpdateQuery->execute();

    if ($stockUpdateQuery === false) {
        error_log("return_book.php: Error updating stock: " . $conn->error);
        $conn->rollback();
        echo "<script>alert('Database error. Please try again.'); window.location.href='../../profile.php';</script>";
        exit();
    }

    // Commit the transaction
    $conn->commit();

    echo "<script>alert('Book returned successfully!'); window.location.href='../../profile.php?success=returned';</script>";
    exit();

} catch (Exception $e) {
    // Rollback the transaction if any error occurred
    $conn->rollback();

    // Log the error (replace with your logging mechanism)
    error_log("Return book transaction failed: " . $e->getMessage());

    echo "<script>alert('An error occurred while returning the book. Please try again.'); window.location.href='../../profile.php';</script>";
    exit();
}
?>