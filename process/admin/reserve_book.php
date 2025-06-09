<?php
session_start();
require_once('../../backend/config/config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to reserve a book.";
    exit();
}

// Get the user ID from session
$userId = $_SESSION['user_id'];

// Get the ISBN from the form submission
$isbn = $_POST['isbn'] ?? null;
if (!$isbn) {
    echo "Invalid request.";
    exit();
}

// Fetch book details based on ISBN
$stmt = $conn->prepare("SELECT * FROM books WHERE ISBN = ?");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    echo "Book not found.";
    exit();
}

// Check if the stock is available for reservation
if ($book['Stock'] > 0) {
    echo "Book is available for immediate rent. You can rent it directly.";
    exit();
}

// Check if the user has already reserved this book
$reservationCheckStmt = $conn->prepare("SELECT * FROM reservations WHERE Book_ID = ? AND Register_ID = ? AND Status = 'pending'");
$reservationCheckStmt->bind_param("ii", $book['Book_ID'], $userId);
$reservationCheckStmt->execute();
$reservationResult = $reservationCheckStmt->get_result();
$reservationCheckStmt->close();

if ($reservationResult->num_rows > 0) {
    echo "You have already reserved this book. You will be notified when it becomes available.";
    exit();
}

// Insert the reservation into the reservations table
$insertStmt = $conn->prepare("INSERT INTO reservations (Book_ID, Register_ID, Reservation_Date, Status) VALUES (?, ?, NOW(), 'pending')");
$insertStmt->bind_param("ii", $book['Book_ID'], $userId);

if ($insertStmt->execute()) {
    // Send email to the user notifying them about the reservation
    $userEmailStmt = $conn->prepare("SELECT r.Email FROM register r WHERE r.Register_ID = ?");
    $userEmailStmt->bind_param("i", $userId);
    $userEmailStmt->execute();
    $userEmailResult = $userEmailStmt->get_result()->fetch_assoc();
    $userEmailStmt->close();

    if ($userEmailResult) {
        $userEmail = $userEmailResult['Email'];
        $subject = "Reservation Confirmation";
        $message = "Your reservation for the book '{$book['Title']}' has been successfully placed. You will be notified when it is available.";
        mail($userEmail, $subject, $message);
    }

    // Redirect to the preview page with a success query parameter
    header("Location: /BryanCodeX/Book/Free/Preview/" . urlencode($isbn) . ".php?reservation_success=1");
    exit();
} else {
    echo "Error: Unable to reserve the book.";
}

$insertStmt->close();
$conn->close();
?>
