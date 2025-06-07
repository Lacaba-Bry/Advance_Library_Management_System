<?php
session_start();
require_once '../../backend/config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$accountId = $_SESSION['user_id'];

// Get posted data
$fullname = $_POST['fullname'] ?? '';
$pronouns = $_POST['pronouns'] ?? '';
$about = $_POST['about'] ?? '';
$website = $_POST['website'] ?? '';
$location = $_POST['location'] ?? '';

// --- 1. Update fullname in `register` table
$updateRegister = $conn->prepare("UPDATE register r 
    JOIN accountlist a ON r.Register_ID = a.Register_ID 
    SET r.Fullname = ? 
    WHERE a.Account_ID = ?");
$updateRegister->bind_param("si", $fullname, $accountId);
$updateRegister->execute();
$updateRegister->close();

// --- 2. Update profile info in `profiles` table
$updateProfile = $conn->prepare("UPDATE profiles 
    SET Pronouns = ?, Website = ?, Location = ?, Description = ? 
    WHERE Account_ID = ?");
$updateProfile->bind_param("ssssi", $pronouns, $website, $location, $about, $accountId);
$updateProfile->execute();
$updateProfile->close();

// Optional: Handle profile picture upload here

header("Location: ../../profile.php?success=profile_updated");
exit();
