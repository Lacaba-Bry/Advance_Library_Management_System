<?php
session_start();

// Ensure the email is passed in the POST request
if (isset($_POST['email'])) {
    $email = $_POST['email'];  // Get the email from the POST data
} else {
    echo "Error: No email provided.";
    exit();
}

// Check if the form is submitted to change the password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the new password and confirm password from the form
    $newPassword = $_POST['new_password']; 
    $confirmPassword = $_POST['confirm_password']; 

    // Check if the passwords match
    if ($newPassword === $confirmPassword) {
        // Hash the new password securely
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Include your database connection
        require_once './config/config.php';  // Your database connection file

        // Prepare the SQL query to update the password in both tables (only password)
        $stmt1 = $conn->prepare("UPDATE accountlist SET Password = ? WHERE email = ?");
        $stmt1->bind_param("ss", $hashedPassword, $email);  // Update the password in accountlist

        $stmt2 = $conn->prepare("UPDATE register SET Password = ? WHERE email = ?");
        $stmt2->bind_param("ss", $hashedPassword, $email);  // Update the password in register

        // Execute both queries
        if ($stmt1->execute() && $stmt2->execute()) {
            // Check if any rows were affected
            if ($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0) {
                // If the password is successfully updated, update the session and redirect the user to login
                $_SESSION['password_updated'] = true;
                header("Location: login.php?message=Password updated successfully. Please log in again.");
                exit();
            } else {
                echo "No rows were updated. Please check if the email exists in the database.";
            }
        } else {
            echo "Error updating password: " . $conn->error;  // Show the error if any
        }

        $stmt1->close();
        $stmt2->close();
        $conn->close();
    } else {
        echo "Passwords do not match. Please try again.";
    }
}
?>
