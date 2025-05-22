<?php

require_once('../backend/config/config.php');

echo "<pre>";
print_r($_FILES['Book_Cover']);
echo "</pre>";

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
    $status = $_POST['Status'];
    $story_snippet = $_POST['Story_Snippet'];
    $description = $_POST['Description'];
    $story = $_POST['Story'];

    // Determine prefix
    $prefix = $status === "Free" ? "Free" : "Premium";

    // Generate new Book_ID
    $query = "SELECT COUNT(*) as count FROM books WHERE Status = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $number = $result['count'] + 1;
    $book_id = $prefix . str_pad($number, 2, '0', STR_PAD_LEFT);
    $stmt->close();

    // Upload book cover
    $cover_folder = "../Book/Book_Cover/$prefix/";
    $cover_name = basename($_FILES["Book_Cover"]["name"]);
    $cover_path = $cover_folder . $cover_name;

    // Make sure folder exists
    if (!is_dir($cover_folder)) {
        mkdir($cover_folder, 0777, true);
    }
    move_uploaded_file($_FILES["Book_Cover"]["tmp_name"], $cover_path);

    // Upload book file
    $file_folder = "../Book/Files_Path/$prefix/";
    $file_name = basename($_FILES["File_Path"]["name"]);
    $file_path = $file_folder . $file_name;

    // Make sure folder exists
    if (!is_dir($file_folder)) {
        mkdir($file_folder, 0777, true);
    }
    move_uploaded_file($_FILES["File_Path"]["tmp_name"], $file_path);

    // Insert into database
    $sql = "INSERT INTO books 
        (Book_ID, Title, Author, Publisher, ISBN, Genre, Status, Book_Cover, File_Path, Story_Snippet, Description, Story)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $book_id, $title, $author, $publisher, $isbn, $genre, $status, $cover_path, $file_path, $story_snippet, $description, $story);

    if ($stmt->execute()) {
        echo "Book added successfully with ID: $book_id";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();

if (!isset($_POST['Title'], $_POST['Status'], $_FILES['Book_Cover'], $_FILES['File_Path'])) {
    echo "Missing required fields.";
    exit;
}


?>

