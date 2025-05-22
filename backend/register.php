<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $email = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $name = trim($_POST['name']);
  $studentnum = trim($_POST['studentnum']);

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

  // Check if student number already exists
  $checkStudent = $conn->prepare("SELECT Register_ID FROM register WHERE Student_Number = ?");
  $checkStudent->bind_param("s", $studentnum);
  $checkStudent->execute();
  $checkStudent->store_result();

  if ($checkStudent->num_rows > 0) {
    echo "<script>alert('Student number is already in use.'); window.history.back();</script>";
    exit();
  }
  $checkStudent->close();

  // Insert new user if all checks pass
  $stmt = $conn->prepare("INSERT INTO register (Email, Password, Fullname, Student_Number, Date_Created) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
  $stmt->bind_param("sssi", $email, $password, $name, $studentnum);

  if ($stmt->execute()) {
    echo "<script>alert('Registration successful!'); window.location.href = '../index.php';</script>";
    exit();
  } else {
    echo "<script>alert('Error: " . $stmt->error . "');</script>";
  }

  $stmt->close();
}
?>
