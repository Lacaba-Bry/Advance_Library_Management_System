<?php 
require_once('../backend/config/config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['Title'];
    $author = $_POST['Author'];
    $publisher = $_POST['Publisher'];
    $isbn = $_POST['ISBN'];
    $genre = $_POST['Genre'];
    $Plan_type = $_POST['Plan_type'];
    $story_snippet = $_POST['Story_Snippet'];
    $description = $_POST['Description'];
    $story = $_POST['Story'];
    $price = isset($_POST['Price']) ? $_POST['Price'] : 0; // Handle Price input if Paid
    $stock = isset($_POST['Stock']) ? $_POST['Stock'] : 0; // Get Stock value

    // Determine Status based on Stock
    $status = ($stock > 0) ? "Available" : "Unavailable";

    // Plan_type is already set correctly, no need for further manipulation
    $prefix = $Plan_type;  // Directly using the Plan_type

    // Generate new Book_ID
    $query = "SELECT COUNT(*) as count FROM books WHERE Plan_type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $Plan_type);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $number = $result['count'] + 1;
    $book_id = $prefix . str_pad($number, 2, '0', STR_PAD_LEFT);
    $stmt->close();

    // Define the absolute path for book cover
    $baseDir = $_SERVER['DOCUMENT_ROOT'] . "/BryanCodeX/Book/"; // Absolute path to the root of your project
    $cover_folder = $baseDir . "$Plan_type/Book_Cover/"; // Absolute path for the cover folder
    $cover_name = basename($_FILES["Book_Cover"]["name"]);
    $cover_path = $cover_folder . $cover_name;

    // URL-encode the file name to handle special characters
    $encodedCoverName = urlencode($cover_name);
    $imageUrl = "/BryanCodeX/Book/" . ucfirst($Plan_type) . "/Book_Cover/" . $encodedCoverName;

    // Debugging: Check if the directory exists for the paid books
    if (!is_dir($cover_folder)) {
        echo "The cover directory doesn't exist: $cover_folder"; // Debugging output
        mkdir($cover_folder, 0777, true); // Create directory if doesn't exist
    }

    move_uploaded_file($_FILES["Book_Cover"]["tmp_name"], $cover_path);

    // Debugging: Check the cover path for the uploaded file
    echo "Cover path: $cover_path"; // Debugging output

    // Upload book file
    $file_folder = $baseDir . "$Plan_type/Files_Path/"; // Absolute path for book file folder
    $file_name = basename($_FILES["File_Path"]["name"]);
    $file_path = $file_folder . $file_name;

    // Make sure the folder exists for the file
    if (!is_dir($file_folder)) {
        mkdir($file_folder, 0777, true);
    }
    move_uploaded_file($_FILES["File_Path"]["tmp_name"], $file_path);

    // Insert into database with the correct number of placeholders
    $sql = "INSERT INTO books 
        (Book_ID, Title, Author, Publisher, ISBN, Genre, Plan_type, Price, Status, Stock, Book_Cover, File_Path, Story_Snippet, Description, Story)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Binding 15 values with 15 placeholders
    // Store the relative paths in the database
    $relativeCoverPath = "/BryanCodeX/Book/$Plan_type/Book_Cover/" . basename($cover_path); // Relative path for cover image
    $relativeFilePath = "/BryanCodeX/Book/$Plan_type/Files_Path/" . basename($file_path); // Relative path for the file

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssss", $book_id, $title, $author, $publisher, $isbn, $genre, $Plan_type, $price, $status, $stock, $relativeCoverPath, $relativeFilePath, $story_snippet, $description, $story);

    if ($stmt->execute()) {
        echo "Book added successfully with ID: $book_id";
    } else {
        echo "Error: " . $stmt->error;
    }

    // ✅ Auto-generate preview page
    $preview_folder = $baseDir . "$Plan_type/Preview/"; // Absolute path for preview folder
    if (!is_dir($preview_folder)) {
        mkdir($preview_folder, 0777, true);
    }
    $preview_filename = $preview_folder . $isbn . ".php";

    // Generate the correct path for the cover image
    $coverPath = "/BryanCodeX/Book/$Plan_type/Book_Cover/" . basename($cover_path);

$preview_template = <<<PHP
<?php
session_start();  // Start the session at the top of the script
require_once('../../../backend/config/config.php');
include '../../../reusable/header.php';

// Define the ISBN and prepare the query
\$isbn = '$isbn';
\$stmt = \$conn->prepare("SELECT * FROM books WHERE ISBN = ?");
\$stmt->bind_param("s", \$isbn);
\$stmt->execute();
\$book = \$stmt->get_result()->fetch_assoc();
\$stmt->close();

// Check if book exists
if (!\$book) {
    die("Book not found.");
}

// Fetch the book details
\$Book_ID = \$book['Book_ID'];
\$Plan_type = \$book['Plan_type'];
\$coverPath = "../../../Book/" . \$Plan_type . "/Book_Cover/" . basename(\$book['Book_Cover']);
\$stock = \$book['Stock'];



// Get the user ID from the session if the user is logged in
\$userId = \$_SESSION['user_id'] ?? null;  // Use null coalescing to handle an undefined session variable


\$stock = \$book['Stock'];
\$returnDate = \$_GET['return_date'] ?? null;


// Get live vote count from the votes table
\$voteCountStmt = \$conn->prepare("SELECT COUNT(*) AS vote_count FROM votes WHERE Book_ID = ?");
\$voteCountStmt->bind_param("i", \$Book_ID);
\$voteCountStmt->execute();
\$voteResult = \$voteCountStmt->get_result()->fetch_assoc();
\$voteCount = \$voteResult['vote_count'] ?? 0;
\$voteCountStmt->close();

// Get read count (number of rentals)
\$readCountStmt = \$conn->prepare("SELECT COUNT(*) AS read_count FROM rent WHERE Book_ID = ?");
\$readCountStmt->bind_param("i", \$Book_ID);
\$readCountStmt->execute();
\$readResult = \$readCountStmt->get_result()->fetch_assoc();
\$readCount = \$readResult['read_count'] ?? 0;
\$readCountStmt->close();



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Preview: <?php echo htmlspecialchars(\$book['Title']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../../css/autogenerate/previewx.css">
  <script src="../../../javascript/generatescript.js"></script>
  <style>
      .vote-btn {
        background: none;
        border: none;
        opacity: 0.6;
      }
  </style>
</head>
<body>
<div class="book-preview">
 <div class="preview-header">
    <img src="<?php echo 'http://localhost/BryanCodeX/Book/' . \$Plan_type . '/Book_Cover/' . basename(htmlspecialchars(\$book['Book_Cover'])); ?>" alt="Book Cover" class="book-cover">
    <div class="book-info">
      <h2 class="book-title"><?php echo htmlspecialchars(\$book['Title']); ?></h2>
      <div class="book-stats">
       <span><i class="fas fa-eye"></i> <strong><?= \$readCount ?></strong> Reads</span>
  <button class="vote-btn" id="voteBtn" onclick="submitVote(<?= \$book['Book_ID'] ?>, <?= \$userId ?? 'null' ?>)">
    <i class="fas fa-star"></i>&nbsp;<strong id="voteCount"><?= \$voteCount ?></strong>
</button>

</span>

        <span><i class="fas fa-list"></i> <strong>1</strong> Parts</span>
        <span><i class="fa-solid fa-book"></i> <strong><?= \$stock ?></strong> Available</span>
      </div>
<div class="start-reading">
<?php
\$canRead = false;
\$hasChecked = false;

// Check if the user has rented the book
if (\$userId) {
    // Prepare the query to check if the book is rented and status is ongoing
    \$checkStmt = \$conn->prepare("SELECT * FROM rent WHERE Account_ID = ? AND Book_ID = ? AND Status = 'ongoing' AND Return_Date > NOW()");
    \$checkStmt->bind_param("ii", \$userId, \$Book_ID);
    \$checkStmt->execute();
    \$checkStmt->store_result();
    \$canRead = \$checkStmt->num_rows > 0;
    \$checkStmt->close();
    \$hasChecked = true;
}
?>

<button 
    class="start-btn" 
    onclick="location.href='../../../Book/<?= \$book['Plan_type'] ?>/Story/<?= \$isbn ?>_story.php'" 
    <?php echo (\$canRead || !\$hasChecked) ? '' : 'disabled style="opacity: 0.5; cursor: not-allowed;"'; ?>>
    ▶ Start Reading
</button>

<?php if (!\$canRead && \$hasChecked): ?>
    <?php if (\$book['Plan_type'] === 'Free' || \$book['Plan_type'] === 'Premium'): ?>
        <div class="button-container">
             <input type="hidden" name="isbn" value="<?= \$isbn ?>">
            <form method="post" action="reserve_rent_book.php">
                  <?php if (\$stock > 0): ?>
                  <button class="btn rent-btn" type="button" onclick="openModal('rentModal')">Rent</button>
                  <?php else: ?>
                    <p style="color: #888;">This book is out of stock. You can only reserve it.</p>
                  <?php endif; ?>
                </form>

               <?php if (\$userId): ?>
                  <?php
                      // Query to fetch the rental details for the logged-in user
                      \$stmt = \$conn->prepare("SELECT Rent_Date, Return_Date FROM rent WHERE Account_ID = ? AND Book_ID = ? AND Status = 'ongoing'");
                      \$stmt->bind_param("ii", \$userId, \$book['Book_ID']);
                      \$stmt->execute();
                      \$rental = \$stmt->get_result()->fetch_assoc();
                      \$stmt->close();
                      
                      // Check if the user has rented the book and has a return date
                      if (\$rental && \$rental['Return_Date']) {
                          \$returnDate = \$rental['Return_Date']; // Get the return date from the database
                      }
                  ?>

                  <!-- Display Countdown Timer if the user has rented the book -->
                      <?php if (\$returnDate): ?>
                          <p>Countdown Days: <span id="countdown-timer"></span></p>

                          <script>
                              // JavaScript function to update the countdown
                              function updateCountdown() {
                                  const returnDate = new Date("<?php echo \$returnDate; ?> 23:59:59"); // Set return date from PHP
                                  const currentDate = new Date();
                                  const timeDifference = returnDate - currentDate;

                                  if (timeDifference <= 0) {
                                      document.getElementById("countdown-timer").innerHTML = "Your rental has expired!";
                                      return;
                                  }

                                  // Calculate remaining time
                                  const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                                  const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                  const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                                  const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

                                  // Display the countdown timer
                                  document.getElementById("countdown-timer").innerHTML = `\${days}d \${hours}h \${minutes}m \${seconds}s`;
                              }

                              // Update the countdown every second
                              setInterval(updateCountdown, 1000);
                              updateCountdown(); // Call once to display immediately
                          </script>
                      <?php endif; ?>
              <?php endif; ?>



               <?php if (\$stock === 0): ?>
                  <!-- Reserve Button (Enabled when stock is 0) -->
                  <form method="post" action="reserve_rent_book.php">
                      <input type="hidden" name="isbn" value="<?= \$isbn ?>">
                      <button class="btn reserve-btn" type="submit">Reserve</button>
                  </form>
              <?php elseif (\$stock > 0): ?>
                  <!-- Reserve Button (Disabled when stock is more than 0) -->
                  <form method="post" action="reserve_rent_book.php">
                      <input type="hidden" name="isbn" value="<?= \$isbn ?>">
                      <button class="btn reserve-btn" type="submit" disabled style="opacity: 0.5; cursor: not-allowed;">Reserve</button>
                  </form>
              <?php endif; ?>
                <!-- Add to Cart Button (Always available) -->
                <form method="post" action="reserve_rent_book.php">
                    <input type="hidden" name="isbn" value="<?= \$isbn ?>">
                    <button class="btn add-btn" type="submit">Add to Cart</button>
                </form>
            </div>
      
        <p style="color: #888;">Please rent or reserve this book to start reading.</p>
    <?php elseif (\$book['Plan_type'] === 'Paid'): ?>
        <form method="post" action="../../../payment/process_payment.php">
            <input type="hidden" name="isbn" value="<?= \$isbn ?>">
            <input type="hidden" name="price" value="<?= \$book['Price'] ?>">
            <button class="buy-btn" type="submit">💳 Buy for $<?= \$book['Price'] ?></button>
        </form>
        <p style="color: #888;">Purchase required to read this book.</p>
    <?php endif; ?>
<?php elseif (!\$userId): ?>
    <p style="color: #888;">Please log in to continue.</p>
<?php endif; ?>
</div>

    </div>
  </div>

  <div class="book-content">
    <h3>Story Snippet</h3>
    <p><?php echo nl2br(htmlspecialchars(\$book['Story_Snippet'])); ?></p>

    <h3>Description</h3>
    <p><?php echo nl2br(htmlspecialchars(\$book['Description'])); ?></p>
  </div>

<div class="reviews-section">
    <h3>Reviews</h3>
    
    <?php
    // Query to fetch reviews, user data, and plan data
    \$reviewStmt = \$conn->prepare("SELECT r.Review_Text, r.Created_At, u.Fullname, p.Plan_Name 
                                  FROM reviews r
                                  JOIN profiles u ON r.Profile_ID = u.Profile_ID
                                  JOIN accountlist al ON u.Account_ID = al.Account_ID
                                  JOIN plans p ON al.Plan_ID = p.Plan_ID
                                  WHERE r.Book_ID = ?");
    \$reviewStmt->bind_param("i", \$book['Book_ID']);
    \$reviewStmt->execute();
    \$reviews = \$reviewStmt->get_result();

    // Check if there are reviews and display them
    if (\$reviews->num_rows > 0) {
        while (\$review = \$reviews->fetch_assoc()) {
            echo "<div class='review'>";
            echo "<strong>" . htmlspecialchars(\$review['Fullname']) . " (" . htmlspecialchars(\$review['Plan_Name']) . ")</strong>";
            echo "<p>" . nl2br(htmlspecialchars(\$review['Review_Text'])) . "</p>";
            echo "<small>" . htmlspecialchars(\$review['Created_At']) . "</small>";
            echo "</div>";
        }
    } else {
        // Message if no reviews exist
        echo "<div class='review'><strong>Anonymous</strong><p>Be the first to leave a review!</p></div>";
    }

    \$reviewStmt->close();
    ?>

    <!-- Review Submission Form -->
    <h4>Leave a Review</h4>
    <?php if (\$userId): ?>
        <form method="post" action="../../../process/index/submit_review.php">
            <input type="hidden" name="isbn" value="<?= \$isbn ?>">
            <div class="form-group">
                <label for="reviewText">Your Review</label>
                <textarea name="reviewText" id="reviewText" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    <?php else: ?>
        <!-- Prompt to log in if user is not logged in -->
        <p style="color: #888;">Please log in to leave a review.</p>
    <?php endif; ?>
</div>
</div>



<div id="rentModal" class="modal fade" tabindex="-1" aria-labelledby="rentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rentModalLabel">Select Rent Duration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="../../../process/index/addrent.php">
          <input type="hidden" name="isbn" value="<?= \$isbn ?>">  <!-- Hidden ISBN -->
          <label for="rentDuration">Choose a duration:</label>
          <select name="rentDuration" id="rentDuration" class="form-control">
            <option value="3">3 Days</option>
            <option value="7">7 Days</option>
            <option value="15">15 Days</option>
            <option value="30">30 Days</option>
          </select>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Rent Now</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>



</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close the connection after all queries
\$conn->close();
?>
PHP;


    file_put_contents($preview_filename, $preview_template);

    // ✅ Auto-generate Story page for Paid plan
    $story_folder = $baseDir . "$Plan_type/Story/"; // Absolute path for story folder
    if (!is_dir($story_folder)) {
        mkdir($story_folder, 0777, true);
    }
    $story_filename = $story_folder . $isbn . "_story.php";

    $story_template = <<<PHP
<?php
require_once('../../../backend/config/config.php');
include '../../../reusable/header.php';

\$isbn = '$isbn';
\$stmt = \$conn->prepare("SELECT * FROM books WHERE ISBN = ?");
\$stmt->bind_param("s", \$isbn);
\$stmt->execute();
\$book = \$stmt->get_result()->fetch_assoc();
\$stmt->close();
\$conn->close();

if (!\$book) {
    die("Book not found.");
}

\$coverPath = "../../../Book/" . \$book['Plan_type'] . "/Book_Cover/" . basename(\$book['Book_Cover']);
\$title = htmlspecialchars(\$book['Title']);
\$author = htmlspecialchars(\$book['Author']);
\$story = nl2br(htmlspecialchars(\$book['Story']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Story - <?= \$title ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../../css/autogenerate/story.css">
   <style>

.photos-container::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url('<?= \$coverPath ?>') no-repeat center center;
  background-size: cover;
  filter: blur(20px);
  transform: scale(1.1); /* Prevent blur edges from cutting off */
  z-index: 0;
}
</style>
</head>
<body>

<!-- STORY HEADER -->
<div class="story-header">
  <div class="story-info">
    <img src="<?= \$coverPath ?>" alt="<?= \$title ?> Cover">
    <div class="story-meta">
      <span class="title"><?= \$title ?></span>
      <span class="author">by <?= \$author ?></span>
    </div>
  </div>

  <div class="story-actions">
    <button>+</button>
    <span class="vote"><i class="fas fa-star"></i> Vote</span>
  </div>
</div>

<div class="photos-container">
  <img src="<?= \$coverPath ?>" alt="<?= \$title ?> Cover" class="book-cover-lg">
</div>

<div class="story-container">
  <div class="story-text">
    <center>Chapter 1</center>
    <p><?= \$story ?></p>
  </div>

  <div class="continue-btn">
    <button onclick="location.href='next-part.php'">Continue to Next Part</button>
  </div>
</div>

</body>
</html>
PHP;

    file_put_contents($story_filename, $story_template);

    $stmt->close();
}
$conn->close();

if (!isset($_POST['Title'], $_POST['Plan_type'], $_FILES['Book_Cover'], $_FILES['File_Path'])) {
    echo "Missing required fields.";
    exit;
}
?>
