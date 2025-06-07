<?php
session_start();  // Start the session to manage logged-in users
require_once('../../backend/config/config.php');  // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to rent a book.");
}

// Get the ISBN and Rent Duration from POST
$isbn = $_POST['isbn'] ?? null;
$rentDuration = $_POST['rentDuration'] ?? null;  // Duration chosen by the user
$userId = $_SESSION['user_id'];  // Logged-in user's ID

if (!$isbn || !$rentDuration) {
    die("Invalid request. Please select a valid book and rent duration.");
}

// Retrieve the Book ID and other details from the database
$stmt = $conn->prepare("SELECT * FROM books WHERE ISBN = ?");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    die("Book not found.");
}

// Check if the stock is available
if ($book['Stock'] <= 0) {
    die("This book is out of stock. You cannot rent it at the moment.");
}

$bookId = $book['Book_ID'];  // Book ID
$currentDate = date('Y-m-d H:i:s');  // Get current date and time in PHP format (for Rent_Date)
$returnDate = date('Y-m-d', strtotime("+$rentDuration days"));  // Calculate return date

// Insert the rental information into the rentals table
$stmt = $conn->prepare("INSERT INTO rent (Account_ID, Book_ID, Rent_Date, Return_Date, Status) 
                        VALUES (?, ?, ?, ?, 'ongoing')");
$stmt->bind_param("iiss", $userId, $bookId, $currentDate, $returnDate);

if ($stmt->execute()) {
    echo "Rental successfully inserted.<br>";  // Debugging: Rental insert check
} else {
    echo "Error inserting rental: " . $stmt->error . "<br>";  // Debugging: Rental insert error
}

$stmt->close();

// Update the stock for the rented book
$stmt = $conn->prepare("UPDATE books SET Stock = Stock - 1 WHERE Book_ID = ?");
$stmt->bind_param("i", $bookId);

if ($stmt->execute()) {
    echo "Stock updated.<br>";  // Debugging: Stock update check
} else {
    echo "Error updating stock: " . $stmt->error . "<br>";  // Debugging: Stock update error
}

$stmt->close();

// addrent.php (after successful rent)

// Get the return date
$returnDate = date('Y-m-d', strtotime("+$rentDuration days"));

// Redirect to the preview page with the return_date as a parameter
header("Location: preview.php?isbn=" . urlencode($isbn) . "&return_date=" . urlencode($returnDate));
exit;

?>
