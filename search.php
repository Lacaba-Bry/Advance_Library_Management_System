<?php
require_once __DIR__ . '/backend/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$accountId = $_SESSION['user_id'];
$userName = $_SESSION['fullname'] ?? 'User';
$avatar = $_SESSION['avatar'] ?? 'image/profile/defaultprofile.jpg'; // Set default avatar
$logout_url = 'backend/logout.php';

function getBooks($conn) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, Stock FROM books";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}

$books = getBooks($conn);

function isBookFavorited($conn, $account_id, $book_id) {
    $count = 0;
    $sql = "SELECT COUNT(*) FROM favorites WHERE account_id = ? AND book_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $account_id, $book_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        die('Error preparing the statement: ' . $conn->error);
    }
    return $count > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book List</title>
  <link rel="stylesheet" href="css/index/search.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .book-card img {
        width: 150px;
        height: 200px;
        object-fit: cover;
    }
    .no-cover {
        width: 150px;
        height: 200px;
        background-color: #f0f0f0; /* Placeholder color */
        text-align: center;
        line-height: 200px;
        color: #888;
        font-size: 14px;
    }
  </style>
</head>
<body>

<div class="main-layout">
  <div class="sidebar">
    <div class="form-group">
      <label for="editor-picks">🎯 Editor Picks</label>
      <select id="editor-picks" name="editor-picks">
        <option value="">Select</option>
        <option value="best-sales">🔥 Best Sales (105)</option>
        <option value="most-commented">💬 Most Commented (21)</option>
        <option value="newest-books">🆕 Newest Books (32)</option>
        <option value="featured">⭐ Featured (129)</option>
        <option value="watch-history">👁️ Watch History (21)</option>
        <option value="best-books">🏆 Best Books (44)</option>
      </select>
    </div>

    <div class="filter-group">
      <h3>🏢 Choose Publisher</h3>
      <select>
        <option>All Publishers</option>
        <option>Publisher A</option>
        <option>Publisher B</option>
      </select>
    </div>

    <div class="filter-group">
      <h3>📅 Select Year</h3>
      <select>
        <option>All Years</option>
        <option>2025</option>
        <option>2024</option>
      </select>
    </div>

    <div class="filter-group">
      <h3>📚 Shop by Category</h3>
      <div class="checkbox-group">
        <label><input type="checkbox"> Action</label>
        <label><input type="checkbox"> Comedy</label>
        <label><input type="checkbox"> Horror</label>
        <label><input type="checkbox"> Fantasy</label>
      </div>
    </div>
  </div>

  <div class="main-content">
    <form class="search-container">
      <div class="search">
        <span class="search-icon material-symbols-outlined">search</span>
        <input class="search-input" type="search" placeholder="Search books by title, author or keyword...">
      </div>
      <ul class="suggestions">
        <li>The Hunger Games by Suzanne Collins</li>
        <li>Harry Potter by J.K. Rowling</li>
        <li>The Embassy of Cambodia by Zadie Smith</li>
        <li>After by Anna Todd</li>
      </ul>
    </form>
    <div class="book-wrapper">
      <?php
      $cardsPerPage = 8;
      $totalBooks = count($books);
      $totalPages = ceil($totalBooks / $cardsPerPage);

      $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
      $currentPage = max(1, min($currentPage, $totalPages));

      $startIndex = ($currentPage - 1) * $cardsPerPage;
      $booksOnCurrentPage = array_slice($books, $startIndex, $cardsPerPage);

      foreach ($booksOnCurrentPage as $book):
        //  Absolute path for file_exists()
        $basePath = $_SERVER['DOCUMENT_ROOT'];  //  Root of the web server's file system

        // Construct the full image path relative to the document root
        $imagePath = $basePath . "/BryanCodeX/Book/" . htmlspecialchars($book['Plan_type']) . "/Book_Cover/" . htmlspecialchars($book['Book_Cover']);
        
        // Construct the URL path for the img src attribute
        $imageUrl = "/BryanCodeX/Book/" . htmlspecialchars($book['Plan_type']) . "/Book_Cover/" . htmlspecialchars($book['Book_Cover']);
        
        // Debugging: Log the image path and URL
        error_log("Image Path (file_exists): $imagePath");
        error_log("Image URL (img src): $imageUrl");

        // Check if the image exists using file_exists() or if the cover field is empty
        if (empty($book['Book_Cover'])) {
            error_log("Book_Cover field is empty for Book ID: " . $book['Book_ID']);
            $imageUrl = './Book/Book_Cover/A-Fathers-Silence.png';  // Fallback image
        } elseif (!file_exists($imagePath)) {
            error_log("Image not found at path: " . $imagePath . " for Book ID: " . $book['Book_ID']);
            $imageUrl = './Book/Book_Cover/A-Fathers-Silence.png';  // Fallback image
        } else {
            error_log("Image found at path: $imagePath for Book ID: " . $book['Book_ID']);
        }

        // Encode the URL path for safe use in HTML
        $encodedImageUrl = htmlspecialchars($imageUrl);

        // Check if the book is favorited
        $isFavorited = isBookFavorited($conn, $accountId, $book['Book_ID']);
        $heartIcon = $isFavorited ? 'fa-heart' : 'fa-heart-o';
      ?>
        <div class="book-card">
          <button class="favorite-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">
            <i class="fa fa-<?php echo $heartIcon; ?>" aria-hidden="true"></i>
          </button>

          <!-- Show book cover if it exists or fallback to default -->
          <img src="<?php echo $encodedImageUrl; ?>" alt="Book Cover" class="book-cover" />

          <div class="book-details">
            <div class="book-title"><?php echo htmlspecialchars($book['Title']); ?></div>
            <div class="book-author"><?php echo htmlspecialchars($book['Author']); ?></div>
          </div>
          <div class="book-actions">
            <button class="btn cart-btn add-to-cart-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">🛒 Add</button>
            <button class="btn rent-btn rent-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>" <?php echo ($book['Stock'] <= 0) ? 'disabled' : ''; ?>>Rent</button>
            <button class="btn reserve-btn reserve-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">Reserve</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button onclick="changePage(-1)" id="prevBtn" <?php if ($currentPage <= 1) echo 'disabled'; ?>>Prev</button>
      <span id="pageNumbers">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <button onclick="goToPage(<?php echo $i; ?>)" class="<?php if ($i == $currentPage) echo 'active'; ?>"><?php echo $i; ?></button>
        <?php endfor; ?>
      </span>
      <button onclick="changePage(1)" id="nextBtn" <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>>Next</button>
    </div>

  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  function toggleFavorite(button) {
      button.classList.toggle('active');
    }

    function goToPage(page) {
      window.location.href = '?page=' + page;
    }

    function changePage(offset) {
      let currentPage = <?php echo $currentPage; ?>;
      let totalPages = <?php echo $totalPages; ?>;
      let newPage = Math.min(Math.max(1, currentPage + offset), totalPages);
      window.location.href = '?page=' + newPage;
    }

    $(document).ready(function() {
      $(".favorite-btn").click(function() {
        var bookId = $(this).data('book-id');
        var icon = $(this).find('i');
        var isFavorited = icon.hasClass('fa-heart');

        $.ajax({
          url: 'process/index/addfavorite.php',
          type: 'POST',
          data: { book_id: bookId, is_favorited: isFavorited ? 1 : 0 },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              if (isFavorited) {
                icon.removeClass('fa-heart').addClass('fa-heart-o');
              } else {
                icon.removeClass('fa-heart-o').addClass('fa-heart');
              }
            } else {
              alert('Error: ' + response.message);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert('Error: ' + textStatus);
          }
        });
      });

      $(".add-to-cart-btn").click(function() {
        var bookId = $(this).data('book-id');
        // Handle add to cart logic here
      });

      $(".reserve-btn").click(function() {
        var bookId = $(this).data('book-id');
        // Handle reserve logic here
      });
    });
  </script>
</body>
</html>
