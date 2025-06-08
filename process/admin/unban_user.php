<?php
require_once(__DIR__ . '../../../backend/config/config.php');  // Corrected path
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    // Step 1: Delete the user from the banned_users table
    $query = "DELETE FROM banned_users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "User unbanned successfully!"; // Specific success message
    } else {
        echo "Error unbanning user!";
    }
    $stmt->close();
}
?>