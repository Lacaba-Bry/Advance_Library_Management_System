<?php
require_once(__DIR__ . '../../../backend/config/config.php');  // Corrected path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $reason = "Violation of community guidelines"; // Default reason, you can customize this.

    // Step 1: Find the user in the register table by email
    $sql = "SELECT Register_ID, Email FROM register WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists in register table
        $user = $result->fetch_assoc();
        $userId = $user['Register_ID'];  // Get the user ID (corrected to Register_ID)

        // Step 2: Insert the user into the banned_users table
        $banSql = "INSERT INTO banned_users (email, reason, date_banned, status) VALUES (?, ?, NOW(), 'banned')";
        $banStmt = $conn->prepare($banSql);
        if (!$banStmt) {
            echo "Prepare failed: " . $conn->error;
            exit;
        }
        $banStmt->bind_param("ss", $email, $reason); // Only bind email and reason

        if ($banStmt->execute()) {
            echo "User has been banned successfully!"; // Specific success message
        } else {
            echo "Error: " . $banStmt->error;
        }

        $banStmt->close();
    } else {
        echo "User not found in register table!";
    }

    $stmt->close();
}
?>