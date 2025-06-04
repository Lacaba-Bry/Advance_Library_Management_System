<?php
session_start();
require_once __DIR__ . '/backend/config/config.php';
include 'reusable/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$accountId = $_SESSION['user_id'];

// Fetch profile data including pronouns, website, and location
$stmt = $conn->prepare("
    SELECT r.Fullname, r.Email, r.Date_Created, p.Plan_Name, pr.Description, pr.Avatar, pr.Pronouns, pr.Website, pr.Location
    FROM accountlist a
    JOIN register r ON a.Register_ID = r.Register_ID
    LEFT JOIN plans p ON a.Plan_ID = p.Plan_ID
    LEFT JOIN profiles pr ON a.Account_ID = pr.Account_ID
    WHERE a.Account_ID = ?
");
$stmt->bind_param("i", $accountId);
$stmt->execute();
$stmt->bind_result($fullname, $email, $dateCreated, $planName, $description, $avatar, $pronouns, $website, $location);
$stmt->fetch();
$stmt->close();

// Set default values if variables are not set (null values from DB)
$avatar = $avatar ?: 'defaultprofile.jpg';
$pronouns = $pronouns ?: '';  // Default to empty string if pronouns are not set
$website = $website ?: '';    // Default to empty string if website is not set
$location = $location ?: '';  // Default to empty string if location is not set
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
    <link rel="stylesheet" href="css/index/profilex.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<!-- Profile Header -->
<div class="profile-header <?php echo $planBorderClass . ' ' . strtolower($planName); ?>">

    <img src="image/profile/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="avatar">
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
     <button id="editProfileBtn" class="edit-profile-btn">Edit Profile</button>
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


<!-- Modal for Editing Profile -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Currently editing your profile</h2>
            <div class="button-group">
            <button type="submit" class="btn-save">Save Changes</button>
            <span class="modal-close" id="closeModal"><a href="profile.php" class="btn-cancel">Cancel</a></span>
               </div>
        </div>
        <form action="save_profile.php" method="POST" enctype="multipart/form-data">
    

        
            <div class="form-group profile-pic-container">
                <label for="profilePicture" class="profile-picture-label">
                    <img src="../image/default-avatar.png" id="profilePicPreview" class="profile-pic-preview">
                </label>
                <input type="file" id="profilePicture" name="profilePicture" style="display: none;" accept="image/*">
            </div>
 
            <div class="Fname">
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
            </div>
                <div class="below">
                    <div class="belowcontainer">
                        <p>The information you enter here, including username, profile photo, will be visible to other users.</p?>
                    <div class="form-row">
                        <label for="pronouns">Pronouns</label>
                        <input type="text" id="pronouns" name="pronouns" value="<?php echo htmlspecialchars($pronouns); ?>">
                    </div>

                    <div class="form-row">
                        <label for="about">About</label>
                        <textarea id="about" name="about"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="form-row">
                        <label for="website">My Website</label>
                        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($website); ?>">
                    </div>

                    <div class="form-row">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>">
                    </div>
                    </div>
                </div>

        </form>
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
// Get the modal and buttons
const modal = document.getElementById("editProfileModal");
const editProfileBtn = document.getElementById("editProfileBtn");
const closeModal = document.getElementById("closeModal");

// Open the modal when the "Edit Profile" button is clicked
editProfileBtn.addEventListener("click", function() {
    modal.style.display = "flex";
});

// Close the modal when the "X" is clicked
closeModal.addEventListener("click", function() {
    modal.style.display = "none";
});

// Close the modal if the user clicks outside of it
window.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});


</script>

</body>
</html>