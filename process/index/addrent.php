<?php
session_start();
require_once('../../backend/config/config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isbn = $_POST['isbn'];
    $rentDuration = $_POST['rentDuration'];
    $accountId = $_SESSION['user_id'];

    // Calculate return date
    $returnDate = date('Y-m-d', strtotime("+" . $rentDuration . " days"));

    // Fetch Book_ID based on ISBN
    $stmt = $conn->prepare("SELECT Book_ID, Stock FROM books WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        $bookId = $result['Book_ID'];
        $stock = $result['Stock'];

        // Check if book is in stock
        if ($stock > 0) {
            // Prepare and execute the rent insertion query
            $insertStmt = $conn->prepare("INSERT INTO rent (Account_ID, Book_ID, Rent_Date, Return_Date, Status) VALUES (?, ?, NOW(), ?, 'ongoing')");
            $insertStmt->bind_param("iis", $accountId, $bookId, $returnDate);

            if ($insertStmt->execute()) {
                // Update the stock in the books table
                $updateStmt = $conn->prepare("UPDATE books SET Stock = Stock - 1 WHERE Book_ID = ?");
                $updateStmt->bind_param("i", $bookId);
                $updateStmt->execute();
                $updateStmt->close();

                // Redirect back to the preview page
              header("Location: /BryanCodeX/Book/Free/Preview/" . urlencode($isbn) . ".php?rent_success=1");

                exit();
            } else {
                // Handle insertion error
                echo "Error: " . $insertStmt->error;
            }
            $insertStmt->close();
        } else {
            // Handle out of stock situation
            echo "Book is out of stock.";
        }
    } else {
        // Handle book not found
        echo "Book not found.";
    }
    $stmt->close();
} else {
    // Handle direct access to the script
    echo "Invalid request.";
}

$conn->close();
?>