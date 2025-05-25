<?php
session_start();
require_once __DIR__ . '/../backend/config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$accountId = $_SESSION['user_id'];

// Fetch profile data
$stmt = $conn->prepare("
    SELECT r.Fullname, r.Email, r.Date_Created, p.Plan_Name, pr.Description, pr.Avatar
    FROM accountlist a
    JOIN register r ON a.Register_ID = r.Register_ID
    LEFT JOIN plans p ON a.Plan_ID = p.Plan_ID
    LEFT JOIN profiles pr ON a.Account_ID = pr.Account_ID
    WHERE a.Account_ID = ?
");
$stmt->bind_param("i", $accountId);
$stmt->execute();
$stmt->bind_result($fullname, $email, $dateCreated, $planName, $description, $avatar);
$stmt->fetch();
$stmt->close();

// Default values
$avatar = $avatar ?: 'defaultprofile.jpg';
$planName = $planName ?: 'Free';
$description = $description ?: 'Help people get to know you';

// Determine plan class for styling
$planBorderClass = 'border-free';
if (strcasecmp($planName, 'Premium') === 0) {
    $planBorderClass = 'border-premium';
} elseif (strcasecmp($planName, 'VIP') === 0) {
    $planBorderClass = 'border-vip';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($fullname); ?>'s Profile</title>
    <link rel="stylesheet" href="../css/index/profile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- Profile Header -->
<div class="profile-header <?php echo $planBorderClass . ' ' . strtolower($planName); ?>">

    <img src="../image/profile/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar">
    <h1><?php echo htmlspecialchars($fullname); ?></h1>
    <p><?php echo htmlspecialchars($email); ?></p>
    <p class="plan-badge <?php echo strtolower($planName); ?>">
        <?php echo htmlspecialchars($planName); ?> Plan
        <?php if (strcasecmp($planName, 'VIP') === 0): ?>
            <i class="bi bi-gem"></i>
        <?php elseif (strcasecmp($planName, 'Premium') === 0): ?>
            <i class="bi bi-lightning-fill"></i>
        <?php endif; ?>
    </p>

    <!-- Stats -->
    <div class="profile-stats">
        <div class="stat">
            <strong>1</strong>
            <span>Reading List</span>
        </div>
        <div class="stat">
            <strong>0</strong>
            <span>Followers</span>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="profile-nav-bar">
    <div class="tabs">
        <a href="#" class="tab active" data-tab="about-tab">About</a>
        <a href="#" class="tab" data-tab="conversations-tab">Conversations</a>
        <a href="#" class="tab" data-tab="following-tab">Following</a>
    </div>
    <div class="profile-actions">
        <div class="plan-bubble"><p>⚡ Upgrade plan</p></div>
        <button class="edit-profile-btn">Edit Profile</button>
    </div>
</div>
<hr class="tab-underline" />

<!-- About Tab -->
<div id="about-tab" class="tab-section active-tab">
    <div class="about-grid">
        <div class="left-content profile-body">
            <p><?php echo htmlspecialchars($description); ?></p>
            <p>Joined on <?php echo date("F j, Y", strtotime($dateCreated)); ?></p>
            <div class="social-links">
                <p>Share Profile</p>
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-pinterest"></i></a>
            </div>
        </div>

        <div class="right-content">
            <div class="reading-list-box">
                <div class="reading-list-header">
                    <div class="title-placeholder"></div>
                    <div class="subtitle-placeholder"></div>
                </div>
                <div class="reading-thumbnails">
                    <div class="thumb"></div>
                    <div class="thumb"></div>
                    <div class="thumb"></div>
                    <div class="thumb"></div>
                </div>
                <div class="reading-list-button">
                    <button class="btn-create-list">Create Reading List</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conversations Tab -->
<div id="conversations-tab" class="tab-section" style="display: none;">
    <div class="centered-content profile-body">
        <h3>Conversations</h3>
        <p>No conversations yet.</p>
    </div>
</div>

<!-- Following Tab -->
<div id="following-tab" class="tab-section" style="display: none;">
    <div class="centered-content profile-body">
        <h3>Following</h3>
        <ul>
            <li>@UserOne</li>
            <li>@UserTwo</li>
            <li>@AuthorXYZ</li>
        </ul>
    </div>
</div>

<!-- JS Tab Navigation -->
<script>
const tabs = document.querySelectorAll('.tab');
const sections = document.querySelectorAll('.tab-section');

tabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        tabs.forEach(t => t.classList.remove('active'));
        sections.forEach(s => s.style.display = 'none');
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).style.display = 'flex';
    });
});
</script>

</body>
</html>
