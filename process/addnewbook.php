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

// Extract Plan_type from the database record
\$Plan_type = \$book['Plan_type'];
// Generate cover path for preview page
\$coverPath = "../../../Book/" . \$Plan_type . "/Book_Cover/" . basename(\$book['Book_Cover']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Preview: <?= htmlspecialchars(\$book['Title']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../../css/autogenerate/preview.css">
</head>
<body>
<div class="book-preview">
  <div class="preview-header">
    <img src="http://localhost/BryanCodeX/Book/<?= \$Plan_type ?>/Book_Cover/<?= basename(htmlspecialchars(\$book['Book_Cover'])) ?>" alt="Book Cover" class="book-cover">
    <div class="book-info">
      <h2 class="book-title"><?= htmlspecialchars(\$book['Title']) ?></h2>
      <div class="book-stats">
        <span><i class="fas fa-eye"></i> <strong>0</strong> Reads</span>
        <span><i class="fas fa-star"></i> <strong>0</strong> Votes</span>
        <span><i class="fas fa-list"></i> <strong>1</strong> Parts</span>
        <span><i class="fas fa-clock"></i> <strong>N/A</strong> Time</span>
      </div>
      <div class="start-reading">
        <button class="start-btn">▶ Start reading</button>
      </div>
    </div>
  </div>

  <div class="book-content">
    <h3>Story Snippet</h3>
    <p><?= nl2br(htmlspecialchars(\$book['Story_Snippet'])) ?></p>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars(\$book['Description'])) ?></p>
  </div>

  <div class="reviews-section">
    <h3>Reviews</h3>
    <div class="review">
      <strong>Anonymous</strong>
      <p>Be the first to leave a review!</p>
    </div>
  </div>
</div>
</body>
</html>
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
