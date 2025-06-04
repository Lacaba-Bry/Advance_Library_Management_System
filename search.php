<?php
require_once __DIR__ . '/backend/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$accountId = $_SESSION['user_id'];
$userName = $_SESSION['fullname'] ?? 'User';
$avatar = $_SESSION['avatar'] ?? 'image/profile/defaultprofile.jpg';
$logout_url = 'backend/logout.php';

// Capture the search term
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the getBooks function to filter based on search
function getBooks($conn, $searchTerm = '') {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, Stock FROM books";
    if ($searchTerm) {
        $sql .= " WHERE Title LIKE ? OR Author LIKE ?";
    }
    $stmt = $conn->prepare($sql);
    if ($searchTerm) {
        $searchTerm = "%$searchTerm%"; // Add wildcards for partial matching
        $stmt->bind_param("ss", $searchTerm, $searchTerm);  // Bind parameters for Title and Author search
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    $stmt->close();
    return $books;
}

$books = getBooks($conn, $searchTerm);

// Check if the book is favorited
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
  <link rel="stylesheet" href="css/index/searchx.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .book-card img {
        width: 200px;
        height: 200px;
        object-fit: cover;
    }
    .no-cover {
        width: 150px;
        height: 200px;
        background-color: #f0f0f0;
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
    <form class="search-container" method="GET">
      <div class="search">
        <span class="search-icon material-symbols-outlined">search</span>
        <input class="search-input" type="search" name="search" placeholder="Search books by title, author or keyword..." value="<?= htmlspecialchars($searchTerm) ?>">
      </div>
      <ul class="suggestions">
        <!-- Dynamically populated suggestions can be added here if needed -->
      </ul>
    </form>

    <div class="book-wrapper">
<?php
$cardsPerPage = 14; // 14 books per page
$totalBooks = count($books);
$totalPages = ceil($totalBooks / $cardsPerPage);
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));
$startIndex = ($currentPage - 1) * $cardsPerPage;
$booksOnCurrentPage = array_slice($books, $startIndex, $cardsPerPage);

foreach ($booksOnCurrentPage as $book):
    $planType = strtolower($book['Plan_type']);
    $filename = basename($book['Book_Cover']);
    $filename = preg_replace("/[^a-zA-Z0-9._-]/", "", $filename);
    $encodedFilename = urlencode($filename);

    // Construct URL and file path for the book cover
    $imageUrl = "/BryanCodeX/Book/" . ucfirst($planType) . "/Book_Cover/" . $encodedFilename;
    $serverPath = $_SERVER['DOCUMENT_ROOT'] . "/BryanCodeX/Book/" . ucfirst($planType) . "/Book_Cover/" . $filename;

    // Check if the book is favorited
    $isFavorited = isBookFavorited($conn, $accountId, $book['Book_ID']);
    $heartIcon = $isFavorited ? 'fa-heart' : 'fa-heart-o';
?>

<div class="book-card">
    <div class="book-cover">
        <?php if (!empty($filename) && file_exists($serverPath)): ?>
            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Book Cover" width="150">
        <?php else: ?>
            <img src="/BryanCodeX/assets/images/placeholder.jpg" alt="Placeholder Cover" width="150">
        <?php endif; ?>
    </div>
    <div class="book-info">
        <h5><?= htmlspecialchars($book['Title']) ?></h5>
        <p><?= htmlspecialchars($book['Author']) ?></p>
        
        <button class="favorite-btn" data-book-id="<?= htmlspecialchars($book['Book_ID']); ?>">
            <i class="fa <?= $heartIcon ?>" aria-hidden="true"></i>
        </button>
        <?php if ($isFavorited): ?>
            <button class="btn btn-sm btn-danger">♥ Favorited</button>
        <?php endif; ?>
    </div>
</div>

<?php endforeach; ?>

<!-- Pagination -->
<div class="pagination">
    <?php if ($totalPages > 1): ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <button class="<?= $i == $currentPage ? 'active' : '' ?>" 
                    <?= $i == $currentPage ? 'disabled' : '' ?> 
                    onclick="window.location.href='?page=<?= $i ?>'">
                <?= $i ?>
            </button>
        <?php endfor; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
  });
</script>
</body>
</html>
