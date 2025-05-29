<?php
require_once('../../../backend/config/config.php');
include '../../../reusable/header.php';

$isbn = '978-1-23-456813-2';
$stmt = $conn->prepare("SELECT * FROM books WHERE ISBN = ?");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$book) {
    die("Book not found.");
}

// Extract Plan_type from the database record
$Plan_type = $book['Plan_type'];
// Generate cover path for preview page
$coverPath = "../../../Book/" . $Plan_type . "/Book_Cover/" . basename($book['Book_Cover']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Preview: <?= htmlspecialchars($book['Title']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../../css/autogenerate/preview.css">
</head>
<body>
<div class="book-preview">
  <div class="preview-header">
    <img src="http://localhost/BryanCodeX/Book/<?= $Plan_type ?>/Book_Cover/<?= basename(htmlspecialchars($book['Book_Cover'])) ?>" alt="Book Cover" class="book-cover">
    <div class="book-info">
      <h2 class="book-title"><?= htmlspecialchars($book['Title']) ?></h2>
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
    <p><?= nl2br(htmlspecialchars($book['Story_Snippet'])) ?></p>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($book['Description'])) ?></p>
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