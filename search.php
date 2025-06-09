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

// Function to get books based on the search term
function getBooks($conn, $searchTerm = '') {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, Stock, ISBN FROM books";
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

function getBooksByPlanType($conn, $planType) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, ISBN FROM books WHERE Plan_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $planType);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    return $books;
}

function fetchFilteredBooks($conn, $filters) {
    $params = [];
    $types = "";
    $conditions = [];

    $baseQuery = "SELECT b.* FROM books b";

    // JOINs for editor picks
    switch ($filters['editor']) {
        case 'best-sales':
            $baseQuery .= " JOIN transaction_plan t ON b.Book_ID = t.plan_id";
            $conditions[] = "t.payment_status = 'completed' AND t.amount > 0";
            $groupBy = " GROUP BY b.Book_ID ORDER BY COUNT(t.transaction_id) DESC";
            break;
        case 'most-commented':
            $baseQuery .= " JOIN reviews r ON b.Book_ID = r.Book_ID";
            $groupBy = " GROUP BY b.Book_ID ORDER BY COUNT(r.Review_ID) DESC";
            break;
        case 'watch-history':
            $baseQuery .= " JOIN rent r ON b.Book_ID = r.Book_ID";
            $groupBy = " GROUP BY b.Book_ID ORDER BY COUNT(r.Rent_ID) DESC";
            break;
        case 'best-books':
            $baseQuery .= " JOIN votes v ON b.Book_ID = v.Book_ID";
            $groupBy = " GROUP BY b.Book_ID ORDER BY SUM(v.Vote_Value) DESC";
            break;
        case 'newest-books':
            $groupBy = " ORDER BY b.Book_ID DESC";
            break;
        default:
            $groupBy = " ORDER BY b.Book_ID DESC";
    }

    if (!empty($filters['search'])) {
        $conditions[] = "(b.Title LIKE ? OR b.Author LIKE ?)";
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
        $types .= "ss";
    }

    if (!empty($filters['price'])) {
        // Ensure you're only fetching Paid books for the price filter
        $conditions[] = "b.Plan_type = 'Paid'";

        if ($filters['price'] == 'below-300') {
            $conditions[] = "b.Price < 300";
        } elseif ($filters['price'] == 'above-300') {
            $conditions[] = "b.Price >= 300";
        }
    }

    if (!empty($filters['plan'])) {
        $conditions[] = "b.Plan_type = ?";
        $params[] = $filters['plan'];
        $types .= "s";
    }

    // Handle Genre Filter
    if (!empty($filters['genre'])) {
        $genrePlaceholders = implode(',', array_fill(0, count($filters['genre']), '?'));
        $conditions[] = "b.Genre IN (" . $genrePlaceholders . ")";
        foreach ($filters['genre'] as $genreId) {
            // Convert genreId back to genre name (assuming you have a mapping)
            // For simplicity, let's assume the genreId is the genre name itself
            $params[] = str_replace('-', ' ', $genreId); // Replace hyphens with spaces
            $types .= "s";
        }
    }

    if (!empty($conditions)) {
        $baseQuery .= " WHERE " . implode(" AND ", $conditions);
    }

    $baseQuery .= $groupBy . " LIMIT 100"; // Optional limit

    $stmt = $conn->prepare($baseQuery);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $books;
}

// Fetch filters
$filters = [
    'search' => $_GET['search'] ?? '',
    'editor' => $_GET['editor'] ?? '',
    'price'  => $_GET['price-filter'] ?? '',
    'plan'   => $_GET['plan-filter'] ?? '',
    'genre'  => $_GET['genre'] ?? [], // Get the selected genres
];

// Final book result
$books = fetchFilteredBooks($conn, $filters);

// --------------------------------------------------------------------------
// DISPLAY BOOKS BASED ON USER PLAN
// --------------------------------------------------------------------------

$userPlanType = $_SESSION['user_type'] ?? 'free';  // Default to 'Free' if not set

// Function to check if the user has access to the book
function canAccessBook($userPlanType, $bookPlanType) {
    // VIP and Premium users can access all books
    if ($userPlanType === 'VIP' || $userPlanType === 'Premium') {
        return true;
    }

    // Free users can access only Free and Paid books
    if ($userPlanType === 'Free' && in_array($bookPlanType, ['Free', 'Paid'])) {
        return true;
    }

    return false; // Deny access if none of the conditions match
}

// Get books based on the user's plan type
function getBooksByUserPlan($conn, $userPlanType) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type FROM books WHERE Plan_type IN ";

    if ($userPlanType === 'VIP') {
        $sql .= "('Free', 'Paid', 'Premium', 'VIP')";
    } elseif ($userPlanType === 'Premium') {
        $sql .= "('Free', 'Paid', 'Premium')";
    } else {
        $sql .= "('Free', 'Paid')";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}

// Fetch books based on the user's plan type
$accessibleBooks = getBooksByUserPlan($conn, $userPlanType);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book List</title>
  <link rel="stylesheet" href="css/index/searchxx.css"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .reset-btn {
  margin-top: 150px;
  background-color: #f44336; /* Red color */
  color: white;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  border-radius: 5px;
}

.reset-btn:hover {
  background-color: #e53935; /* Darker red */
}

/* Book cover container (image and lock overlay) */
.book-cover-container {
    position: relative;
    display: inline-block;
}

.book-cover {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 10px; /* Optional: Rounded corners */
}

/* Lock overlay (appears when the book is locked) */
.lock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
    border-radius: 10px;
    opacity: 0; /* Initially hidden */
    visibility: hidden; /* Initially hidden */
    transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out; /* Smooth transition */
}

/* Show the lock overlay when the book is locked */
.book-card .disabled-link .lock-overlay { /* Modified selector */
    visibility: visible;
    opacity: 1;
}

.lock-overlay i {
    font-size: 50px; /* Lock icon size */
}

/* Optional: Disable clicking on locked books */
.book-card .disabled-link { /* Modified selector */
    pointer-events: none; /* Disables clicking on the book cover */
    cursor: not-allowed; /* Change cursor to indicate it's not clickable */
}

  </style>
</head>
<body>
<?php include('reusable/header.php'); ?>

<div class="main-layout">
  <div class="sidebar">
  <div class="form-group">
  <label for="editor-picks">🎯 Editor Picks</label>
  <form method="GET" id="filter-form">
    <select id="editor-picks" name="editor" onchange="document.getElementById('filter-form').submit();">
      <option value="">Select</option>
      <option value="best-sales">🔥 Best Sales</option>
      <option value="most-commented">💬 Most Commented</option>
      <option value="newest-books">🆕 Newest Books</option>
      <option value="watch-history">👁️ Watch History</option>
      <option value="best-books">🏆 Best Books</option>
    </select>
  </form>
</div>

<form method="GET" id="filter-form">
<div class="filter-group">
 <h3>🏢 Filter Paid Books by Price</h3>
  <select name="price-filter" id="price-filter" onchange="this.form.submit()">
    <option value="">All Price</option>
   <option value="below-300" <?= isset($_GET['price-filter']) && $_GET['price-filter'] == 'below-300' ? 'selected' : '' ?>>P300 Below</option>
<option value="above-300" <?= isset($_GET['price-filter']) && $_GET['price-filter'] == 'above-300' ? 'selected' : '' ?>>P300 Above</option>

  </select>
</div>

<div class="filter-group">
  <h3>📅 Select Plan</h3>
  <select name="plan-filter" id="plan-filter" onchange="this.form.submit()">
    <option value="">All Plan</option>
    <option value="Free" <?= $_GET['plan-filter'] == 'Free' ? 'selected' : '' ?>>Free</option>
    <option value="Premium" <?= $_GET['plan-filter'] == 'Premium' ? 'selected' : '' ?>>Premium</option>
  </select>
</div>

<div class="filter-group">
    <h3>📚 Shop by Genre</h3>
    <div class="checkbox-group">
        <?php
        // Fetch distinct genres from the database
        $sql = "SELECT DISTINCT Genre FROM books";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $genre = htmlspecialchars($row['Genre']); // Sanitize the genre
                $genreId = strtolower(str_replace(' ', '-', $genre)); // Create a unique ID for the checkbox

                // Check if the genre is selected (from GET parameters)
                $isChecked = isset($_GET['genre']) && in_array($genreId, $_GET['genre']);

                echo '<label>';
                echo '<input type="checkbox" name="genre[]" value="' . $genreId . '" onchange="this.form.submit()"'; // ADDED onchange event
                if ($isChecked) {
                    echo ' checked';
                }
                echo '> ' . $genre . '</label>';
            }
        } else {
            echo '<p>No genres found.</p>';
        }
        ?>
    </div>
</div>
</form>

     <div class="filter-group">
        <button type="reset" onclick="window.location.href = window.location.pathname" class="reset-btn">Reset Filters</button>
      </div>
  </div>
  

  <div class="main-content">
    <form class="search-container" method="GET" id="search-form">
  <div class="search">
    <span class="search-icon material-symbols-outlined">search</span>
    <input class="search-input" type="search" id="search-input" name="search" placeholder="Search books by title, author or keyword..." value="<?= htmlspecialchars($searchTerm) ?>">
  </div>
</form>


    <div class="book-wrapper" id="book-wrapper">
      <?php
          $cardsPerPage = 21;
        $totalBooks = count($books);
        $totalPages = ceil($totalBooks / $cardsPerPage);
        $currentPage = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
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

          // Check if the user has access to the book
          $hasAccess = canAccessBook($userPlanType, $book['Plan_type']);

      $isbn = $book['ISBN']; // use $book['ISBN'] for proper preview link
      $planFolder = ucfirst(strtolower($book['Plan_type']));
      $previewPath = "/BryanCodeX/Book/$planFolder/Preview/$isbn.php";
      ?>

      <div class="book-card">
        <?php if ($hasAccess): ?>
            <a href="<?= htmlspecialchars($previewPath) ?>" class="book-link">
        <?php else: ?>
            <div class="book-link disabled-link">
        <?php endif; ?>
          <div class="book-cover">
              <?php if (!empty($filename) && file_exists($serverPath)): ?>
                  <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Book Cover" width="150">
              <?php else: ?>
                  <img src="/BryanCodeX/assets/images/placeholder.jpg" alt="Placeholder Cover" width="150">
              <?php endif; ?>
              <?php if (!$hasAccess): ?>
                  <div class="lock-overlay">
                      <i class="fas fa-lock"></i>
                  </div>
              <?php endif; ?>
          </div>
          <?php if ($hasAccess): ?>
            </a>
        <?php else: ?>
            </div>
        <?php endif; ?>
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
                    onclick="window.location.href='?page=<?= $i ?>
                        &search=<?= urlencode($searchTerm) ?>
                        &editor=<?= urlencode($editorPick) ?>
                        &price-filter=<?= urlencode($priceFilter) ?>
                        &plan-filter=<?= urlencode($planFilter) ?>'">
                <?= $i ?>
            </button>
        <?php endfor; ?>
    <?php endif; ?>
</div>


    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
  // Prevent form submission with Enter key
  $('#search-input').on('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault(); // Prevents form submission on Enter
    }
  });

  // Live search on input
  $('#search-input').on('input', function() {
    var searchTerm = $(this).val();

    // Update the URL with the search term without reloading the page
    history.pushState(null, null, '?search=' + encodeURIComponent(searchTerm)); // Update the URL with search term

    // Perform the search via AJAX to update book list dynamically without reloading the page
    $.ajax({
      url: window.location.href, // Use the current URL to keep the search term
      type: 'GET',
      data: { search: searchTerm },  // Send the search term via GET request
      success: function(response) {
        // Update the book list with the updated content
        $('#book-wrapper').html($(response).find('#book-wrapper').html());  // Only replace the book list content
      }
    });
  });

  // Handle the favorite button click (this part is already correct)
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