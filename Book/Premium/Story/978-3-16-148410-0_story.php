<?php
require_once('../../../backend/config/config.php');
include '../../../reusable/header.php';

$isbn = '978-3-16-148410-0';
$stmt = $conn->prepare("SELECT * FROM books WHERE ISBN = ?");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$book) {
    die("Book not found.");
}

$coverPath = "../../../Book/" . $book['Plan_type'] . "/Book_Cover/" . basename($book['Book_Cover']);
$title = htmlspecialchars($book['Title']);
$author = htmlspecialchars($book['Author']);
$story = nl2br(htmlspecialchars($book['Story']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Story - <?= $title ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../../../css/autogenerate/story.css">
  <style>

.photos-container::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
 background: url('<?= $coverPath ?>') no-repeat center center;
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
    <img src="<?= $coverPath ?>" alt="<?= $title ?> Cover">
    <div class="story-meta">
      <span class="title"><?= $title ?></span>
      <span class="author">by <?= $author ?></span>
    </div>
  </div>

  <div class="story-actions">
    <button>+</button>
    <span class="vote"><i class="fas fa-star"></i> Vote</span>
  </div>
</div>

<div class="photos-container">
     <img src="<?= $coverPath ?>" alt="<?= $title ?> Cover" class="book-cover-lg">
</div>

<div class="story-container">
  <div class="story-text">
    <center>Chapter 1</center>
    <p><?= $story ?></p>
  </div>

  <div class="continue-btn">
    <button onclick="location.href='next-part.php'">Continue to Next Part</button>
  </div>
</div>

</body>
</html>