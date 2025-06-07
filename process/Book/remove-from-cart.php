<?php
session_start();
require_once('../../backend/config/config.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cartId = (int)$_POST['cart_id'];  // Get the cart ID from the request

    // Ensure user is logged in
    if (isset($_SESSION['user_id'])) {
        $accountId = $_SESSION['user_id'];

        // Prepare the query to delete the book from the cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE Cart_ID = ? AND Account_ID = ?");
        $stmt->bind_param("ii", $cartId, $accountId);

        if ($stmt->execute()) {
            echo "success";  // Return success response to the AJAX call
        } else {
            echo "error";  // Return error response if deletion fails
        }

        $stmt->close();
    } else {
        echo "error";  // Return error if user is not logged in
    }
}
?>
