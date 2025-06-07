<?php
session_start(); // Start the session

// Include necessary files
require_once('../../../backend/config/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php'); // Redirect to login if not logged in
    exit();
}

// Get the book's ISBN and price
$isbn = $_POST['isbn'] ?? null;
$price = $_POST['price'] ?? null;

// Check if the ISBN and price are provided
if ($isbn && $price) {
    // Check if the book exists
    $stmt = $conn->prepare("SELECT * FROM books WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$book) {
        die("Book not found.");
    }

    // Check if the user has sufficient balance (assuming a balance column in the account table)
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT balance FROM accountlist WHERE Account_ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $userBalance = $result['balance'] ?? 0;

    // Check if the user has enough balance
    if ($userBalance < $price) {
        echo "You do not have enough funds to purchase this book.";
        exit();
    }

    // Proceed with the payment
    // Deduct the price from the user's balance
    $newBalance = $userBalance - $price;
    $stmt = $conn->prepare("UPDATE accountlist SET balance = ? WHERE Account_ID = ?");
    $stmt->bind_param("di", $newBalance, $userId);
    $stmt->execute();
    $stmt->close();

    // Log the purchase into a transaction table
    $stmt = $conn->prepare("INSERT INTO transactions (Account_ID, Book_ID, Transaction_Type, Amount, Date) VALUES (?, ?, 'Purchase', ?, NOW())");
    $stmt->bind_param("iid", $userId, $book['Book_ID'], $price);
    $stmt->execute();
    $stmt->close();

    // Optional: If you're integrating with a payment gateway like PayPal or Stripe,
    // you would redirect to that payment page here.
    // For this example, let's assume the payment is processed successfully.

    echo "Payment successfully processed! You have bought the book: " . htmlspecialchars($book['Title']);

    // You can redirect the user to their dashboard or book page after successful purchase.
    header("Location: ../../../book/" . $isbn . "_story.php");
    exit();
} else {
    // If ISBN or price is not set, redirect to the home page or show an error
    echo "Invalid book or payment details.";
    exit();
}
?>
