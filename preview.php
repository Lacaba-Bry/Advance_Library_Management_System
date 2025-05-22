<?php include 'reusable/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Document</title>
    <style>
.book-preview {
  max-width: 900px;
  margin: 30px auto;
  font-family: 'Segoe UI', sans-serif;
  color: #222;
}

.preview-header {
  display: flex;
  gap: 30px;
  align-items: center;
}

.book-cover {
  width: 220px;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.book-info {
  flex: 1;
}

.book-title {
  font-size: 2rem;
  margin-bottom: 10px;
}

.book-stats {
  display: flex;
  gap: 20px;
  font-size: 0.95rem;
  color: #666;
  margin-bottom: 15px;
}

.start-btn {
  background: black;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 20px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s ease;
}
.start-btn:hover {
  background: #333;
}

.book-content {
  margin-top: 30px;
}
.book-content h3 {
  margin-bottom: 8px;
  margin-top: 24px;
  font-size: 1.2rem;
  border-bottom: 2px solid #eee;
  padding-bottom: 4px;
}
.book-content p {
  color: #444;
  line-height: 1.6;
}

.reviews-section {
  margin-top: 40px;
}
.review {
  margin-bottom: 20px;
  background: #f9f9f9;
  padding: 15px;
  border-left: 4px solid #ccc;
}
.review strong {
  display: block;
  margin-bottom: 5px;
}
</style>

</head>
<body>
    <div class="book-preview">
  <div class="preview-header">
    <img src="sample1.jpg" alt="Book Cover" class="book-cover">

    <div class="book-info">
      <h2 class="book-title">ACE</h2>
      <div class="book-stats">
        <span><i class="fas fa-eye"></i> <strong>9.3M</strong> Reads</span>
        <span><i class="fas fa-star"></i> <strong>214K</strong> Votes</span>
        <span><i class="fas fa-list"></i> <strong>107</strong> Parts</span>
        <span><i class="fas fa-clock"></i> <strong>14h 51m</strong> Time</span>
      </div>

      <div class="start-reading">
        <button class="start-btn">▶ Start reading</button>
      </div>
    </div>
  </div>

  <div class="book-content">
    <h3>Story Snippet</h3>
    <p>The King has returned... and the game will never be the same again.</p>

    <h3>Description</h3>
    <p>ACE is a gripping tale of power, betrayal, and redemption, as the shadows of the underworld rise again. This is more than just a story—it’s a legacy reborn.</p>
  </div>

  <div class="reviews-section">
    <h3>Reviews</h3>
    <div class="review">
      <strong>John Doe</strong>
      <p>Absolutely brilliant storytelling. Couldn’t put it down!</p>
    </div>
    <div class="review">
      <strong>Jane Smith</strong>
      <p>Ace truly lives up to the name. Waiting for part 108!</p>
    </div>
  </div>
</div>
</body>
</html>