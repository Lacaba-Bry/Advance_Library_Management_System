<?php
require_once __DIR__ . '/backend/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$accountId = $_SESSION['user_id'];
$userName = $_SESSION['fullname'] ?? 'User';
$avatar = $_SESSION['avatar'] ?? 'https://via.placeholder.com/32';
$logout_url = 'backend/logout.php';

function getBooks($conn) {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Price, Genre, Plan_type, Stock FROM books";  // IMPORTANT: Select Stock here!
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
    }
    return $books;
}

$books = getBooks($conn);

$booksPerPage = 6;
$totalBooks = count($books);
$totalPages = ceil($totalBooks / $booksPerPage);

$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));

$startIndex = ($currentPage - 1) * $booksPerPage;
$booksOnCurrentPage = array_slice($books, $startIndex, $booksPerPage);

function isBookFavorited($conn, $account_id, $book_id) {
    $sql = "SELECT COUNT(*) FROM favorites WHERE account_id = ? AND book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $account_id, $book_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body { background-color: #f9f9fb; }
    .navbar { background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    .navbar .container-fluid { display: flex; justify-content: space-between; align-items: center; }
    .search-bar { width: 80%; }
    .search-icon { background: transparent; border: none; cursor: pointer; }
    .navbar .d-flex a { color: #333; text-decoration: none; }
    .navbar .d-flex a:hover { color: #007bff; }
    .btn-outline-dark { border: 1px solid #007bff; color: #007bff; padding: 5px 10px; }
    .btn-outline-dark:hover { background-color: #007bff; color: white; }
    .book-card { position: relative; border-radius: 10px; overflow: hidden; background: linear-gradient(to bottom, #ffffff, #f6f9fc); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; margin-bottom: 20px; }
    .book-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
    .favorite-icon { position: absolute; top: 10px; right: 10px; background: #fff; border-radius: 50%; padding: 6px; z-index: 10; color: #666; cursor: pointer; }
    .favorite-icon:hover { color: #e74c3c; }
    .book-img { height: 250px; width: 100%; object-fit: cover; } /* Increased height */
    .price-old { text-decoration: line-through; color: #888; font-size: 0.9em; }
    .sidebar { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    .sidebar h6 { font-weight: 600; margin-bottom: 10px; font-family: 'Segoe UI', sans-serif; }
    .sidebar .form-check { margin-bottom: 8px; }

    .book-cover-container {
        position: relative;
        overflow: hidden;
        width: 100%; /* Take up the full width */
        margin: 0 auto;  /* Center the image */
    }

    .book-cover-container:hover .quick-view {
        opacity: 1;
    }

    .quick-view {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        text-decoration: none;
        z-index: 1;
    }

    .action-buttons {
        display: flex;
        flex-direction: column; /* Stack buttons vertically */
        gap: 5px; /* Space between buttons */
        margin-top: 10px;
    }

    .top-buttons {
        display: flex;
        gap: 5px;
    }


    .action-buttons button {
        flex: 1; /* Distribute space evenly */
        width: 100%;  /* Make buttons full width of their container */
    }

      .action-buttons .reserve-btn { /* Specific style for the reserve button */
        width: 100%; /* Overriding the general width */
    }

    /* New CSS for text alignment */
    .book-card .p-2 h6 {
        text-align: center;  /* Center the title */
    }

    .book-card .p-2 div {
        text-align: center;  /* Center the price */
    }

  </style>
</head>
<body>


<div class="container-fluid py-4">
  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="sidebar">
        <h6>Editor Picks</h6>
        <ul class="list-unstyled small">
          <li><a href="#">Best Sales (105)</a></li>
          <li><a href="#">Most Commented (21)</a></li>
          <li><a href="#">Newest Books (32)</a></li>
          <li><a href="#">Featured (129)</a></li>
          <li><a href="#">Watch History (21)</a></li>
          <li><a href="#">Best Books (44)</a></li>
        </ul>

        <hr>

        <h6>Choose Publisher</h6>
        <select class="form-select mb-3">
          <option selected>All Publishers</option>
          <option>Publisher A</option>
          <option>Publisher B</option>
        </select>

        <h6>Select Year</h6>
        <select class="form-select mb-3">
          <option selected>All Years</option>
          <option>2025</option>
          <option>2024</option>
          <option>2023</option>
        </select>

        <h6>Shop by Category</h6>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat1">
          <label class="form-check-label" for="cat1">Action</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat2">
          <label class="form-check-label" for="cat2">Comedy</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat3">
          <label class="form-check-label" for="cat3">Horror</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat4">
          <label class="form-check-label" for="cat4">Fantasy</label>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="row g-4">
        <?php foreach ($booksOnCurrentPage as $book):
           // $imagePath = htmlspecialchars($book['Book_Cover']); // COMMENT OUT THIS LINE
$imagePath = "Book/" . htmlspecialchars($book['Plan_type']) . "/Book_Cover/" . htmlspecialchars($book['Book_Cover']); // UNCOMMENT THIS LINE
            $isFavorited = isBookFavorited($conn, $accountId, $book['Book_ID']);
            $heartIcon = $isFavorited ? 'fa-heart' : 'fa-heart-o';

            ?>
          <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="book-card p-2">
              <div class="book-cover-container">
                <a href="#" class="quick-view">Quick View</a>
                <img src="<?php echo htmlspecialchars($imagePath); ?>" class="book-img" alt="Book Cover">
              </div>

              <div class="p-2">
                <h6 class="mb-1"><?php echo htmlspecialchars($book['Title']); ?></h6>
                <p class="text-muted small mb-1"><?php echo htmlspecialchars($book['Genre']); ?></p>
                <div><strong>$<?php echo htmlspecialchars($book['Price']); ?></strong></div>
                <div class="action-buttons">

                    <div class="top-buttons">
                        <button class="btn btn-primary btn-sm add-to-cart-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
                        <button class="btn btn-success btn-sm rent-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>"
                            <?php echo ($book['Stock'] <= 0) ? 'disabled' : ''; ?>> Rent
                        </button>
                    </div>

                    <button class="btn btn-warning btn-sm reserve-btn" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">Reserve</button>
                </div>
              </div>
              <span class="favorite-icon" data-book-id="<?php echo htmlspecialchars($book['Book_ID']); ?>">
                <i class="fa fa-<?php echo $heartIcon; ?>" aria-hidden="true"></i>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <li class="page-item <?php if ($currentPage <= 1) echo 'disabled'; ?>">
            <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
          </li>
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if ($i == $currentPage) echo 'active'; ?>">
              <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>">
            <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.form-check-input').forEach(input => {
    input.addEventListener('change', () => {
      alert('Category filtering not yet implemented');
    });
  });

  $(document).ready(function() {
      $(".favorite-icon").click(function() {
          var bookId = $(this).data('book-id');
          var icon = $(this).find('i');
          var isFavorited = icon.hasClass('fa-heart');

          $.ajax({
              url: 'process/index/addfavorite.php', // Corrected path
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
              url: 'process/index/addcart.php', // Corrected path
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
              url: 'process/index/addrent.php', // Corrected path
              type: 'POST',
              data: { book_id: bookId },
              success: function(response) {
                  alert(response);
              }
          });
      });
  });
</script>

</body>
</html>