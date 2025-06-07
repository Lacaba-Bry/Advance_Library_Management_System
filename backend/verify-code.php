<?php
session_start();

// Check if email is provided via URL
if (isset($_GET['email'])) {
    $email = $_GET['email'];  // Get email from URL parameter
    echo "A verification code has been sent to: $email";
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the entered code from the form
    $enteredCode = $_POST['code'];

    // Compare the entered code with the stored one in session
    if ($enteredCode == $_SESSION['reset_code']) {
        // If the codes match, redirect to password change page
        header('Location: change-password.php?email=' . urlencode($email));  // Redirect with email as parameter
        exit();
    } else {
        echo "Invalid verification code. Please try again.";
    }
}
?>

<form action="verify-code.php" method="POST">
    <input type="text" name="code" placeholder="Enter the 6-digit code" required />
    <button type="submit">Verify Code</button>
</form>
