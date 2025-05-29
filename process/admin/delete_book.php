<?php
// Enable error reporting for debugging (REMOVE IN PRODUCTION!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../../backend/config/config.php');  // Corrected path

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $bookId = $_POST['book_id'];

    // Sanitize the book ID (important for security!)
    $bookId = filter_var($bookId, FILTER_VALIDATE_INT);  // Ensures it's an integer
    if ($bookId === false || $bookId <= 0) {
        echo "Invalid book ID.";
        exit;
    }

    // Fetch book details BEFORE deleting (for file paths)
    $sql = "SELECT Book_Cover, Plan_type, File_Path, ISBN FROM books WHERE Book_ID = ?";  // Removed Story from select
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        echo "Database error: Could not prepare statement.";
        exit;
    }

    $stmt->bind_param("i", $bookId);

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        echo "Database error: Could not execute statement.";
        exit;
    }

    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close(); // Close the statement

    if ($result) {
        $planType = strtolower($result['Plan_type']);
        $isbn = $result['ISBN']; // Get ISBN for preview and story files

        // Base directory for all book-related files
        $baseDir = $_SERVER['DOCUMENT_ROOT'] . "/BryanCodeX/Book/" . ucfirst($planType) . "/";

        // Construct file paths - IMPORTANT: Sanitize filenames!
        $bookCover = $baseDir . "Book_Cover/" . basename($result['Book_Cover']);
        $filePath = $baseDir . "Files_Path/" . basename($result['File_Path']);
        $previewFile = $baseDir . "Preview/" . $isbn . ".php";  // Construct Preview path using ISBN
        $storyFile = $baseDir . "Story/" . $isbn . "_story.php"; // construct story file path.

        // Function to safely delete a file with error logging
        function safeUnlink($filePath) {
            if (!empty($filePath) && file_exists($filePath)) {
                if (!unlink($filePath)) {
                    error_log("Failed to delete file: " . $filePath . "  Permissions issue?");
                    return false; // Indicate failure
                }
            }
            return true; // Indicate success (or file didn't exist)
        }

        // Delete files using the safeUnlink function
        $coverDeleted = safeUnlink($bookCover);
        $fileDeleted = safeUnlink($filePath);
        $storyDeleted = safeUnlink($storyFile);
        $previewDeleted = safeUnlink($previewFile);

        $allFilesDeleted = $coverDeleted && $fileDeleted && $storyDeleted && $previewDeleted;

        // Delete from database
        $sql = "DELETE FROM books WHERE Book_ID = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            echo "Database error: Could not prepare delete statement.";
            exit;
        }

        $stmt->bind_param("i", $bookId);

        if ($stmt->execute()) {
            if ($allFilesDeleted) {
                echo "Book and associated files deleted successfully!";
            } else {
                echo "Book deleted from database, but some file deletions failed. Check error log.";
            }

        } else {
            error_log("Database deletion error: " . $stmt->error);
            echo "Error deleting book from database.";
        }
        $stmt->close(); // Close the statement
    } else {
        echo "Book not found.";
    }
    $conn->close();
} else {
    echo "Invalid request.";
}
?>