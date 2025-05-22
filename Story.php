<?php include 'reusable/header.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Story - ACE</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff;
      color: #222;
    }
.photos-container {
  position: relative;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}

/* Blurred background of the same image */
.photos-container::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url('sample1.jpg') no-repeat center center;
  background-size: cover;
  filter: blur(20px);
  transform: scale(1.1); /* Prevent blur edges from cutting off */
  z-index: 0;
}

/* Centered image */
.book-cover-lg {
  width: 300px; /* Adjust size as needed */
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.4);
  position: relative;
  z-index: 1;
}


    /* HEADER SECTION */
    .story-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #fff;
      padding: 10px 20px;
      border-bottom: 1px solid #ddd;
    }

    .story-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .story-info img {
      height: 45px;
      border-radius: 5px;
    }

    .story-meta {
      display: flex;
      flex-direction: column;
    }

    .story-meta .title {
      font-weight: bold;
      font-size: 1rem;
    }

    .story-meta .author {
      font-size: 0.85rem;
      color: #666;
    }

    .story-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .story-actions button,
    .story-actions .vote {
      background: #ff5c00;
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .story-actions .vote {
      background: none;
      color: #ff5c00;
      font-size: 0.95rem;
    }

    /* MAIN STORY CONTENT */
    .story-container {
      max-width: 700px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .story-text {
      font-size: 1.1rem;
      line-height: 1.8;
      text-align: justify;
    }

    .continue-btn {
      margin-top: 40px;
      text-align: center;
    }

    .continue-btn button {
      background-color: #222;
      color: white;
      padding: 12px 25px;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .continue-btn button:hover {
      background-color: #444;
    }
  </style>
</head>
<body>

<!-- STORY HEADER -->
<div class="story-header">
  <div class="story-info">
    <img src="sample1.jpg" alt="ACE Cover">
    <div class="story-meta">
      <span class="title">ACE</span>
      <span class="author">by Aura_Swan</span>
    </div>
  </div>

  <div class="story-actions">
    <button>+</button>
    <span class="vote"><i class="fas fa-star"></i> Vote</span>
  </div>
</div>

<div class="photos-container">
  <img src="sample1.jpg" alt="ACE Cover" class="book-cover-lg">
</div>
<!-- STORY CONTENT -->
<div class="story-container">
  <div class="story-text">
    <center>Chapter 1</center>
    <p>
      The room was quiet except for the ticking of an old wall clock. Outside, the world moved fast—noisy, ruthless, and cold. But here, in the center of a dim-lit lounge, sat a man with a glass of bourbon and a mind full of vengeance.
    </p>
    <p>
      ACE had returned.
    </p>
    <p>
      With every move calculated and every ally handpicked, he was ready to reclaim what was his. The shadows were watching—but so was he.
    </p>
  </div>

  <div class="continue-btn">
    <button onclick="location.href='next-part.php'">Continue to Next Part</button>
  </div>
</div>

</body>
</html>
