<?php
session_start();
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  // Check if the email belongs to an admin first
  $adminStmt = $conn->prepare("SELECT Admin_ID, Password FROM admin WHERE Email = ?");
  $adminStmt->bind_param("s", $email);
  $adminStmt->execute();
  $adminStmt->store_result();

  if ($adminStmt->num_rows === 1) {
    // Admin found
    $adminStmt->bind_result($admin_id, $admin_password);
    $adminStmt->fetch();

    // Directly compare password (no hashing required for admin)
    if ($password === $admin_password) {
      // Set session variables for admin
      $_SESSION['admin_id'] = $admin_id;
      $_SESSION['admin_email'] = $email; // Store email of admin

      echo "<script>
        alert('Welcome Admin!');
        window.location.href = '../admin.php';
      </script>";
      exit;
    } else {
      echo "<script>alert('Incorrect admin password.'); window.history.back();</script>";
      exit;
    }
  }
  $adminStmt->close(); // Close admin query if not admin

  // If not admin, proceed with regular user login (using hashed password)
  $stmt = $conn->prepare("SELECT Account_ID, Password FROM accountlist WHERE Email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows == 1) {
    // User found
    $stmt->bind_result($account_id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      // Set session variables for user
      $_SESSION['user_id'] = $account_id;
      $_SESSION['user_email'] = $email; // Store email of user

      echo "<script>
        alert('Welcome User!');
        window.location.href = '../home.php';
      </script>";
      exit;
    } else {
      echo "<script>alert('Incorrect password.'); window.history.back();</script>";
    }
  } else {
    echo "<script>alert('No user found with this email.'); window.history.back();</script>";
  }

  $stmt->close();
  $conn->close();
}
?>
