<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $plan_id = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 1;

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
    if (!$checkEmail) {
        error_log("Prepare failed: " . $conn->error);
        echo "<script>alert('Database error. Please try again later.'); window.history.back();</script>";
        exit();
    }
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo "<script>alert('Email is already in use.'); window.history.back();</script>";
        exit();
    }
    $checkEmail->close();

    // *** TRANSACTION HANDLING ***
    $conn->begin_transaction();

    $stmt = null;
    $stmt2 = null;
    $stmt3 = null;
    $stmt4 = null;
    try {
        // Insert new user into register table
        $stmt = $conn->prepare("INSERT INTO register (Email, Password, Fullname, Date_Created) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
        if (!$stmt) {
            throw new Exception("Prepare failed for register: " . $conn->error);
        }
        $stmt->bind_param("sss", $email, $passwordHash, $name);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into register: " . $stmt->error);
        }
        $register_id = $stmt->insert_id;
        error_log("Register ID: " . $register_id);

        // Insert into accountlist with the selected plan
        $stmt2 = $conn->prepare("INSERT INTO accountlist (Email, Password, Register_ID, Plan_ID) VALUES (?, ?, ?, ?)");
        if (!$stmt2) {
            throw new Exception("Prepare failed for accountlist: " . $conn->error);
        }
        $stmt2->bind_param("ssii", $email, $passwordHash, $register_id, $plan_id);
        if (!$stmt2->execute()) {
            throw new Exception("Error inserting into accountlist: " . $stmt2->error);
        }
        $account_id = $stmt2->insert_id;
        error_log("Account ID: " . $account_id);

        // Insert into profiles table
        $stmt3 = $conn->prepare("INSERT INTO profiles (Account_ID, Register_ID, Fullname) VALUES (?, ?, ?)");
        if (!$stmt3) {
            throw new Exception("Prepare failed for profiles: " . $conn->error);
        }
        $stmt3->bind_param("iis", $account_id, $register_id, $name);
        if (!$stmt3->execute()) {
            throw new Exception("Error inserting into profiles: " . $stmt3->error);
        }

        // Determine start and expiration dates based on plan
        $start_date = date('Y-m-d');
        $expiration_date = null;

        if ($plan_id == 2) {
            $expiration_date = date('Y-m-d', strtotime('+1 month'));
            error_log("Premium Plan Expiration Date: " . $expiration_date);
        }

        // Insert into user_plans table (conditionally)
        if ($plan_id == 2 || $plan_id == 3) {
            $stmt4 = $conn->prepare("INSERT INTO user_plans (Account_ID, Start_Date, Expiration_Date) VALUES (?, ?, ?)");
            if (!$stmt4) {
                throw new Exception("Prepare failed for user_plans: " . $conn->error);
            }
            $stmt4->bind_param("iss", $account_id, $start_date, $expiration_date);
            if (!$stmt4->execute()) {
                throw new Exception("Error inserting into user_plans: " . $stmt4->error);
            }
        }

        $conn->commit();
        header("Location: ../index.php?registration=success"); // Redirect on success
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Registration Error: " . $e->getMessage());
        error_log("Account ID: " . $account_id);
        error_log("Register ID: " . $register_id);

        echo "<script>alert('Error during registration, please try again later: " . htmlspecialchars($e->getMessage()) . "'); window.history.back();</script>";
    } finally {
        if ($stmt) $stmt->close();
        if ($stmt2) $stmt2->close();
        if ($stmt3) $stmt3->close();
        if ($stmt4) $stmt4->close();
        $conn->close();
    }
}