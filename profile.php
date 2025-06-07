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


// Fetch rented books for this user
$rentQuery = $conn->prepare("
    SELECT b.Book_Cover, b.Title, b.Author, r.Rent_Date, r.Return_Date, b.ISBN, b.Book_ID, b.Plan_type
    FROM rent r
    JOIN books b ON r.Book_ID = b.Book_ID
    WHERE r.Account_ID = ? AND r.Status = 'Ongoing'
");

$rentQuery->bind_param("i", $accountId);
$rentQuery->execute();
$rentResult = $rentQuery->get_result();

// Check if any rented books were found
$rentedBooks = [];
while ($row = $rentResult->fetch_assoc()) {
    $rentedBooks[] = $row;
}


$rentQuery->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($fullname); ?>'s Profile</title>
    <link rel="stylesheet" href="css/index/profile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            .table-rentals {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.table-rentals th, .table-rentals td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.table-rentals img {
    max-height: 80px;
    border-radius: 5px;
}

.table-rentals .btn {
    margin-right: 5px;
    padding: 5px 10px;
    font-size: 0.85rem;
}

.modalx {
    position: fixed;
    display: flex;
    justify-content: center;
    align-items: center;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 999;
}

.modal-contentx {
    background: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
    max-width: 400px;
}
.button-groupx {
    margin-top: 20px;
    display: flex;
    justify-content: space-around;
}
.alert-success {
    background-color: #d4edda;
    padding: 10px 20px;
    border: 1px solid #c3e6cb;
    color: #155724;
    border-radius: 4px;
}

        </style>
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
        <a href="#" class="tab" data-tab="conversations-tab">Books</a>
        <a href="#" class="tab" data-tab="following-tab">Following</a>
    </div>
    <div class="profile-actions">
        <div class="plan-bubble"><p>⚡ Upgrade plan</p></div>
     <button id="editProfileBtn" class="edit-profile-btn">Edit Profile</button>
    </div>
</div>
<hr class="tab-underline" />
<!-- Success Message (Add it here) -->
<?php if (isset($_GET['success']) && $_GET['success'] === 'profile_updated'): ?>
    <div class="alert alert-success" style="margin: 20px;">
        ✅ Profile updated successfully!
    </div>
<?php endif; ?>

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
    <h3>Rented Books</h3>
    <?php if (count($rentedBooks) > 0): ?>
        <table class="table-rentals">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Rent Date</th>
                    <th>Return Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                  <?php foreach ($rentedBooks as $book): ?>
    <?php
        $planType = strtolower($book['Plan_type']);
        $filename = basename($book['Book_Cover']);
        $baseDir = $_SERVER['DOCUMENT_ROOT'] . "/BryanCodeX/Book/";
        $imagePath = $baseDir . ucfirst($planType) . "/Book_Cover/" . $filename;
        $imageUrl = "/BryanCodeX/Book/" . ucfirst($planType) . "/Book_Cover/" . $filename;
        error_log("profile.php: File Exists: " . file_exists($imagePath));
    ?>
    <tr>
        <td><img src="<?php echo $imageUrl; ?>" alt="Cover" style="height: 50px;"></td> 
        <td><?php echo htmlspecialchars($book['Title']); ?></td>
                        <td><?php echo htmlspecialchars($book['Author']); ?></td>
                        <td><?php echo date("F j, Y", strtotime($book['Rent_Date'])); ?></td>
                        <td><?php echo date("F j, Y", strtotime($book['Return_Date'])); ?></td>
                        <td>
                            <a href="preview/<?php echo htmlspecialchars($book['ISBN']); ?>.php" class="btn btn-sm btn-info">View</a>
                        <button class="btn btn-danger btn-sm return-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">Return</button>
                        <?php if (isset($_GET['success']) && $_GET['success'] === 'returned'): ?>
                            <div class="alert alert-success" style="margin: 20px;">
                                ✅ Book returned successfully!
                            </div>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You haven't rented any books yet.</p>
    <?php endif; ?>
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
        <form action="process/index/Edit_Profile.php" method="POST" enctype="multipart/form-data">


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
                        <p>The information you enter here, including username, profile photo, will be visible to other users.</p>
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









<div id="confirmReturnModal" class="modalx" style="display:none;">
  <div class="modal-contentx">
    <h3>Return Book</h3>
    <p>Are you sure you want to return this book?</p>
    <div class="button-groupx">
      <!-- This link now points to return_book.php -->
      <a id="confirmReturnLink" href="#" class="btn btn-success">Yes, Return</a>
      <button class="btn btn-secondary" id="cancelReturn">Cancel</button>
    </div>
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
const saveChangesBtn = document.querySelector("#editProfileModal .btn-save"); // Get the Save Changes button

// Open the modal when the "Edit Profile" button is clicked
editProfileBtn.addEventListener("click", function() {
    modal.style.display = "flex";
});

// Close the modal when the "X" is clicked
closeModal.addEventListener("click", function(e) {
    e.preventDefault(); // Prevent the link from navigating
    modal.style.display = "none";
});

// Prevent closing the modal when clicking inside it
modal.addEventListener("click", function(e) {
    e.stopPropagation(); // Stop the click from propagating to the window
});

// Close the modal if the user clicks outside of it
window.addEventListener("click", function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

// Prevent the form from submitting and closing the modal
saveChangesBtn.addEventListener("click", function(e) {
    // Prevent the default action of the button
    e.preventDefault();

    // Submit the form
    const form = document.querySelector("#editProfileModal form");
    form.submit();
});


document.querySelectorAll(".return-btn").forEach(button => {
    button.addEventListener("click", function () {
        const bookId = this.getAttribute("data-book-id");
        const modal = document.getElementById("confirmReturnModal");
        const confirmLink = document.getElementById("confirmReturnLink");

        // Correctly encode the bookId and point to return_book.php
        const returnUrl = `process/index/return_book.php?book_id=${encodeURIComponent(bookId)}`;

        confirmLink.href = returnUrl;
        modal.style.display = "flex";
    });
});


</script>

</body>
</html>