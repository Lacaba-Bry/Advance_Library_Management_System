function redirectToChangePassword() {
    var email = '<?php echo urlencode($_SESSION['reset_email']); ?>'; // Get the email from the session
    window.location.href = "change-password.php?email=" + email; // Redirect to change password page
}
