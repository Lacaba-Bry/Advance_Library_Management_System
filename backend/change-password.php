<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Enable error reporting (remove after debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['new_password'], $_POST['confirm_password'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    echo "Email: " . htmlspecialchars($email) . "<br>"; // Debugging
    echo "New Password: " . htmlspecialchars($newPassword) . "<br>"; // Debugging
    echo "Confirm Password: " . htmlspecialchars($confirmPassword) . "<br>"; // Debugging

    if ($newPassword !== $confirmPassword) {
        $_SESSION['reset_status'] = 'password_mismatch';
        header('Location: ../index.php?email=' . urlencode($email) . '&showChangePasswordModal=2');
        exit();
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update password in accountlist table
    $sql1 = "UPDATE accountlist SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("ss", $hashedPassword, $email);

    echo "SQL 1: " . htmlspecialchars($sql1) . "<br>"; // Debugging

    if ($stmt->execute()) {
        echo "accountlist update successful<br>"; // Debugging
    } else {
        $error = "Error updating password in accountlist: " . $stmt->error;
        error_log($error); // Log the error for debugging
        $_SESSION['reset_status'] = 'db_error';
        header('Location: ../index.php?email=' . urlencode($email) . '&showChangePasswordModal=2');
        exit();
    }

    // Update password in register table
    $sql2 = "UPDATE register SET password = ? WHERE email = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ss", $hashedPassword, $email);

    echo "SQL 2: " . htmlspecialchars($sql2) . "<br>"; // Debugging

    if ($stmt2->execute()) {
        echo "register update successful<br>"; // Debugging
        $_SESSION['reset_status'] = 'success';
        header('Location: ../index.php');
        exit();
    } else {
        $error = "Error updating password in register: " . $stmt2->error;
        error_log($error); // Log the error for debugging
        $_SESSION['reset_status'] = 'db_error';
        header('Location: ../index.php?email=' . urlencode($email) . '&showChangePasswordModal=2');
        exit();
    }
} else {
    // Handle cases where the form wasn't submitted correctly
    echo "Form not submitted correctly<br>"; // Debugging
    header('Location: ../index.php'); // Or display an error
    exit();
}
?>