<?php

require_once(__DIR__ . '/../../backend/config/config.php');  // Corrected path


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    echo "Email received: " . htmlspecialchars($email);  // Debugging line

    // Proceed with the query
    $sql = "UPDATE users SET status = 'banned' WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo "User has been banned successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

?>
