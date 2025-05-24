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

    // Upload book cover
    $cover_folder = "../Book/$Plan_type/Book_Cover/"; // Plan-based folder
    $cover_name = basename($_FILES["Book_Cover"]["name"]);
    $cover_path = $cover_folder . $cover_name;

    // Make sure folder exists
    if (!is_dir($cover_folder)) {
        mkdir($cover_folder, 0777, true);
    }
    move_uploaded_file($_FILES["Book_Cover"]["tmp_name"], $cover_path);

    // Upload book file
    $file_folder = "../Book/$Plan_type/Files_Path/"; // Plan-based folder
    $file_name = basename($_FILES["File_Path"]["name"]);
    $file_path = $file_folder . $file_name;

    // Make sure folder exists
    if (!is_dir($file_folder)) {
        mkdir($file_folder, 0777, true);
    }
    move_uploaded_file($_FILES["File_Path"]["tmp_name"], $file_path);

    // Insert into database
    $sql = "INSERT INTO books 
        (Book_ID, Title, Author, Publisher, ISBN, Genre, Plan_type, Book_Cover, File_Path, Story_Snippet, Description, Story)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $book_id, $title, $author, $publisher, $isbn, $genre, $Plan_type, $cover_path, $file_path, $story_snippet, $description, $story);

    if ($stmt->execute()) {
        echo "Book added successfully with ID: $book_id";
    } else {
        echo "Error: " . $stmt->error;
    }

    // ✅ Auto-generate preview page
    $preview_folder = "../Book/$Plan_type/Preview/"; // Plan-based folder for previews
    if (!is_dir($preview_folder)) {
        mkdir($preview_folder, 0777, true);
    }
    $preview_filename = $preview_folder . $isbn . ".php";

    $preview_template = <<<PHP
    <?php
    require_once('../../../backend/config/config.php');
    
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
        <img src="<?= htmlspecialchars(\$book['Book_Cover']) ?>" alt="Book Cover" class="book-cover">
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

    $stmt->close();
}
$conn->close();

if (!isset($_POST['Title'], $_POST['Plan_type'], $_FILES['Book_Cover'], $_FILES['File_Path'])) {
    echo "Missing required fields.";
    exit;
}

?>
