<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Sanitize and validate inputs
  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  $password = trim($_POST['password']);
  $name = trim($_POST['name']);
  $plan_id = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 1; // Default to Free plan (1)

  // Validate Email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit();
  }

  // Validate Password
  if (strlen($password) < 3) {
    echo "<script>alert('Password must be at least 8 characters.'); window.history.back();</script>";
    exit();
  }

  // Hash the password
  $passwordHash = password_hash($password, PASSWORD_DEFAULT);

  // Check if email already exists
  $checkEmail = $conn->prepare("SELECT Register_ID FROM register WHERE Email = ?");
  $checkEmail->bind_param("s", $email);
  $checkEmail->execute();
  $checkEmail->store_result();

  if ($checkEmail->num_rows > 0) {
    echo "<script>alert('Email is already in use.'); window.history.back();</script>";
    exit();
  }
  $checkEmail->close();

  // Insert new user into register table
  $stmt = $conn->prepare("INSERT INTO register (Email, Password, Fullname, Date_Created) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
  $stmt->bind_param("sss", $email, $passwordHash, $name);

  if ($stmt->execute()) {
    // Get the last inserted Register_ID
    $register_id = $stmt->insert_id;

    // Insert into accountlist with the selected plan
    $stmt2 = $conn->prepare("INSERT INTO accountlist (Email, Password, Register_ID, Plan_ID) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("ssii", $email, $passwordHash, $register_id, $plan_id);

    if ($stmt2->execute()) {
      echo "<script>alert('Registration successful! You have been assigned a plan.'); window.location.href = '../index.php';</script>";
    } else {
      echo "<script>alert('Error inserting into accountlist.'); window.history.back();</script>";
    }

    $stmt2->close();
    exit();
  } else {
    // Log the error in server logs and notify user
    error_log("Registration Error: " . $stmt->error);
    echo "<script>alert('Error during registration, please try again later.'); window.history.back();</script>";
  }

  $stmt->close();
  $conn->close();
}
?>
