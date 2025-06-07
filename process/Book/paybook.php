<?php
session_start();
require_once('../../backend/config/config.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];
    $cardNumber = $_POST['cardNumber'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];

    // Log incoming data for debugging
    error_log("Received payment details: ISBN = $isbn, Price = $price, Card Number = $cardNumber, Expiry Date = $expiryDate, CVV = $cvv");

    // Validate the input data
    if (empty($cardNumber) || empty($expiryDate) || empty($cvv)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Fetch book details based on ISBN to get Book_ID and other relevant data
    $stmt = $conn->prepare("SELECT Book_ID, Price FROM books WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Check if the book exists
    if (!$book) {
        echo json_encode(['success' => false, 'message' => 'Book not found.']);
        exit();
    }

    $Book_ID = $book['Book_ID'];
    $price = $book['Price']; // Get the price of the book from the database

    // Simulate processing the payment (this would usually involve an actual payment gateway like Stripe, PayPal, etc.)
    // Here, we'll just assume the payment was successful.
    $paymentSuccess = true; // Set this based on actual payment processing logic

    if ($paymentSuccess) {
        // Deduct the book from stock
        $updateStockStmt = $conn->prepare("UPDATE books SET Stock = Stock - 1 WHERE ISBN = ?");
        $updateStockStmt->bind_param("s", $isbn);
        $updateStockStmt->execute();
        $updateStockStmt->close();

        // Get user ID from session
        $userId = $_SESSION['user_id'];

        // Prepare the SQL query to insert the transaction details
        $insertStmt = $conn->prepare("INSERT INTO transaction_book (user_id, book_id, price, purchase_date) VALUES (?, ?, ?, NOW())");
        $insertStmt->bind_param("iid", $userId, $Book_ID, $price);
        $insertStmt->execute();
        $insertStmt->close();

        // Log the transaction (for debugging)
        error_log("Transaction successful for Book ISBN: $isbn, User ID: $userId");

        echo json_encode(['success' => true, 'message' => 'Payment successful! You now have permanent access to this book.']);
    } else {
        // If payment failed
        echo json_encode(['success' => false, 'message' => 'Payment failed. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
