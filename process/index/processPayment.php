<?php
session_start();
require_once('../../backend/config/config.php');  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Retrieve the payment details from the form
    $cardNumber = $_POST['cardNumber'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];

    // Step 2: Simulate payment processing
    if ($cardNumber && $expiryDate && $cvv) {
        // Simulate payment success (you'd integrate with a payment provider like Stripe or PayPal here)
        $paymentSuccess = true;

        // Step 3: If payment is successful, renew the Premium plan for 1 month
        if ($paymentSuccess) {
            // Get the Account ID from session
            $accountId = $_SESSION['user_id'];

            // Set the new expiration date (1 month from today)
            $newExpiryDate = (new DateTime())->modify('+1 month')->format('Y-m-d');

            // Step 4: Update the user plan expiration date in the database
            $stmt = $conn->prepare("UPDATE user_plans SET Expiration_Date = ? WHERE Account_ID = ?");
            $stmt->bind_param("si", $newExpiryDate, $accountId);
            if ($stmt->execute()) {
                // Plan renewed successfully
                $stmt->close();

                // Step 5: Redirect the user to the home page with a success message
                header("Location: home.php?success=plan_renewed");
                exit();
            } else {
                // If update fails, handle failure gracefully
                $stmt->close();
                header("Location: home.php?error=database_error");
                exit();
            }
        } else {
            // If payment fails, demote the user to the Free plan
            $stmt = $conn->prepare("UPDATE user_plans SET Plan_ID = (SELECT Plan_ID FROM plans WHERE Plan_Name = 'Free') WHERE Account_ID = ?");
            $stmt->bind_param("i", $accountId);
            $stmt->execute();
            $stmt->close();

            // Redirect to home with error message
            header("Location: home.php?error=payment_failed");
            exit();
        }
    } else {
        // If the payment details are invalid, handle it (optional)
        header("Location: home.php?error=invalid_payment_details");
        exit();
    }
} else {
    // If not a POST request, redirect to home
    header("Location: home.php");
    exit();
}
?>