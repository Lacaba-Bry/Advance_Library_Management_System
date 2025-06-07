<?php
session_start();
require_once('../../backend/config/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to leave a review.");
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the book ISBN and review text
    $isbn = $_POST['isbn'];
    $reviewText = $_POST['reviewText'];
    $userId = $_SESSION['user_id']; // Get the logged-in user's ID

    // Check if review text is empty
    if (empty($reviewText)) {
        die("Review text cannot be empty.");
    }

    // Sanitize review text to avoid any unwanted characters
    $reviewText = htmlspecialchars($reviewText);

    // Get the Book_ID based on the ISBN
    $stmt = $conn->prepare("SELECT Book_ID FROM books WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Check if book exists
    if ($book) {
        $Book_ID = $book['Book_ID'];

        // Get Profile_ID from the profiles table
        $profileStmt = $conn->prepare("SELECT Profile_ID FROM profiles WHERE Account_ID = ?");
        $profileStmt->bind_param("i", $userId);
        $profileStmt->execute();
        $profile = $profileStmt->get_result()->fetch_assoc();
        $profileStmt->close();

        // Check if Profile_ID exists for the given user
        if ($profile) {
            $Profile_ID = $profile['Profile_ID'];

            // Insert the review into the database
            $stmt = $conn->prepare("INSERT INTO reviews (Book_ID, Profile_ID, Review_Text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $Book_ID, $Profile_ID, $reviewText);

            if ($stmt->execute()) {
                // Successfully inserted the review, now redirect back to the book preview page
                header("Location: preview.php?isbn=" . urlencode($isbn));
                exit;
            } else {
                die("Error inserting review into the database.");
            }

            $stmt->close();
        } else {
            die("Profile not found for the given user.");
        }
    } else {
        die("Invalid book.");
    }
} else {
    die("Invalid request.");
}

$conn->close();
?>
