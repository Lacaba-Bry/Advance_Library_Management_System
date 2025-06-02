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
    $sql = "SELECT COUNT(*) FROM favorites WHERE account_id = ? AND book_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $account_id, $book_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    } else {
        // Handle potential errors with the SQL preparation
        die('Error preparing the statement: ' . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Search Layout</title>
  <link rel="stylesheet" href="css/index/search.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .book-card img {
        width: 150px;      /* Adjust as needed */
        height: 200px;     /* Adjust as needed */
        object-fit: cover;  /* Maintain aspect ratio and cover the area */
    }
  </style>
</head>
<body>

<div class="main-layout">
  <!-- Sidebar -->
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

  <!-- Main Content -->
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
          $imagePath = "Book/" . htmlspecialchars($book['Plan_type']) . "/Book_Cover/" . htmlspecialchars($book['Book_Cover']);

          // Check if the image exists and is a valid file, otherwise use a default image
          if (!file_exists($imagePath) || empty($book['Book_Cover'])) {
            $imagePath = 'image/profile/defaultprofile.jpg';
          }

          $encodedImagePath = htmlspecialchars($imagePath);

          $isFavorited = isBookFavorited($conn, $accountId, $book['Book_ID']);
          $heartIcon = $isFavorited ? 'fa-heart' : 'fa-heart-o';
      ?>
        <div class="book-card">
          <button class="favorite-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">
            <i class="fa fa-<?php echo $heartIcon; ?>" aria-hidden="true"></i>
          </button>
          <!-- Corrected Image Handling -->
          <img src="<?php echo $encodedImagePath; ?>" alt="Book Cover" onerror="this.onerror=null; this.src='image/profile/defaultprofile.jpg';" />
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
            console.error("AJAX Error: " + textStatus, errorThrown);
            alert('An error occurred while processing your request.');
          }
        });
      });

      $(".add-to-cart-btn").click(function() {
        var bookId = $(this).data('book-id');
        $.ajax({
          url: 'process/index/addcart.php',
          type: 'POST',
          data: { book_id: bookId },
          success: function(response) {
            alert(response);
          }
        });
      });

      $(".rent-btn").click(function() {
        var bookId = $(this).data('book-id');
        $.ajax({
          url: 'process/index/addrent.php',
          type: 'POST',
          data: { book_id: bookId },
          success: function(response) {
            alert(response);
          }
        });
      });

      $(".reserve-btn").click(function() {
        var bookId = $(this).data('book-id');
        alert("Reserve functionality not implemented yet for book ID: " + bookId);
      });
    });
  </script>
</body>
</html>
