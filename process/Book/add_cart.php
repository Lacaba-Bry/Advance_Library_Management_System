<?php
session_start();  // Start the session
require_once('../../backend/config/config.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add books to the cart.']);
    exit();
}

$userId = $_SESSION['user_id'];  // Get the logged-in user's ID
$bookId = $_POST['book_id'];  // Get the book ID from the form submission

// Sanitize input to prevent SQL injection
$bookId = filter_var($bookId, FILTER_VALIDATE_INT);

if (!$bookId) {
    echo json_encode(['success' => false, 'message' => 'Invalid book ID.']);
    exit();
}

// Check if the book is already in the cart for the user
$stmt = $conn->prepare("SELECT * FROM cart WHERE Account_ID = ? AND Book_ID = ?");
$stmt->bind_param("ii", $userId, $bookId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the book is already in the cart, return a message
    echo json_encode(['success' => false, 'message' => 'This book is already in your cart.']);
} else {
    // If the book is not in the cart, add it
    $stmt = $conn->prepare("INSERT INTO cart (Account_ID, Book_ID) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $bookId);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Book added to the cart.']);
}

$stmt->close();
$conn->close();
?>
