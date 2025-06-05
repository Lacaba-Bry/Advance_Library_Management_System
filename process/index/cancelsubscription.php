<?php
session_start();
require_once('../../backend/config/config.php');  // Include database connection

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $accountId = $_SESSION['user_id'];

    // Start a transaction to ensure both updates are handled correctly
    $conn->begin_transaction();

    try {
        // Update the user's plan to Free and remove the expiration date in user_plans
        $stmt1 = $conn->prepare("UPDATE user_plans 
                                 SET Plan_ID = (SELECT Plan_ID FROM plans WHERE Plan_Name = 'Free'), 
                                     Expiration_Date = NULL 
                                 WHERE Account_ID = ?");
        $stmt1->bind_param("i", $accountId);
        $stmt1->execute();

        // Update the user's plan to Free in accountlist table
        $stmt2 = $conn->prepare("UPDATE accountlist 
                                 SET Plan_ID = (SELECT Plan_ID FROM plans WHERE Plan_Name = 'Free') 
                                 WHERE Account_ID = ?");
        $stmt2->bind_param("i", $accountId);
        $stmt2->execute();

        // Commit the transaction
        $conn->commit();

        // Fetch updated plan from the database
        $stmt3 = $conn->prepare("SELECT p.Plan_Name FROM plans p 
                                 JOIN accountlist a ON p.Plan_ID = a.Plan_ID 
                                 WHERE a.Account_ID = ?");
        $stmt3->bind_param("i", $accountId);
        $stmt3->execute();
        $stmt3->bind_result($planName);
        $stmt3->fetch();

        // Update the session to reflect the new plan
        $_SESSION['user_type'] = $planName;

        // Close statements
        $stmt1->close();
        $stmt2->close();
        $stmt3->close();

        // Return success message
        echo json_encode(['success' => true, 'plan' => $planName]);
    } catch (Exception $e) {
        // Rollback the transaction if any query fails
        $conn->rollback();
        // Log the error and return failure message
        error_log("Error updating plan to Free for Account_ID: $accountId. " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
}
?>
