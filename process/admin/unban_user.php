<?php

// unban_user.php
require_once(__DIR__ . '/../../backend/config/config.php');  // Corrected path


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    // Query to unban the user
    $sql = "UPDATE users SET status = 'active' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "User has been unbanned successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}


?>