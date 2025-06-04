<?php
require_once(__DIR__ . '/../backend/config/config.php');

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'all';

// SQL with optional genre filter. Prioritize books.Price
$query = "SELECT books.*, COALESCE(books.Price, plans.Price) AS display_price
          FROM books
          LEFT JOIN plans ON books.Plan_type = plans.Plan_Name
          WHERE books.Title LIKE ? OR books.Author LIKE ?";

if ($genreFilter !== 'all') {
    $query .= " AND books.Genre = ?";
}

$stmt = $conn->prepare($query);
$searchParam = "%" . $searchTerm . "%";
if ($genreFilter !== 'all') {
    $stmt->bind_param("sss", $searchParam, $searchParam, $genreFilter);
} else {
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

try {
    $stmt->execute();
    $books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<p style='color: red;'>An error occurred while retrieving books. Please contact the administrator.</p>";
    $books = [];
}


// Query to get the total number of transactions
$total_transactions_query = "SELECT COUNT(*) AS total_transactions FROM transaction_plan";
$total_transactions_result = $conn->query($total_transactions_query);
$total_transactions = 0; // Default value if the query fails

if ($total_transactions_result) {
    $row = $total_transactions_result->fetch_assoc();
    $total_transactions = $row['total_transactions'];
}

// Query to get the total earnings (sum of the amounts)
$total_earnings_query = "SELECT SUM(amount) AS total_earnings FROM transaction_plan WHERE payment_status = 'completed'";
$total_earnings_result = $conn->query($total_earnings_query);
$total_earnings = 0.00; // Default value if the query fails

if ($total_earnings_result) {
    $row = $total_earnings_result->fetch_assoc();
    $total_earnings = number_format($row['total_earnings'], 2);  // Format to 2 decimal places
}

// Query to get the total book count and stock for Free, Premium, and Paid books
$book_count_query = "
    SELECT 
        Plan_type,
        COUNT(*) AS total_books,
        SUM(Stock) AS total_stock
    FROM books
    GROUP BY Plan_type
";

$book_count_result = $conn->query($book_count_query);
$book_counts = [];

// Initialize the book counts for Free, Premium, and Paid plans
$book_counts = [
    'free' => ['total_books' => 0, 'total_stock' => 0],
    'premium' => ['total_books' => 0, 'total_stock' => 0],
    'paid' => ['total_books' => 0, 'total_stock' => 0]
];

if ($book_count_result) {
    while ($row = $book_count_result->fetch_assoc()) {
        $plan_type = strtolower($row['Plan_type']);
        $book_counts[$plan_type] = [
            'total_books' => $row['total_books'],
            'total_stock' => $row['total_stock']
        ];
    }
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book List | Admin Panel</title>
  <link rel="stylesheet" href="css/adminheader.css">
  <link rel="stylesheet" href="css/adminpanel/booklist.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
  <main>
    <header class="header">
      <span class="logo-section"><span class="logo">Home</span></span>
      <div class="user-info">
        <div class="user-profile">
          <img src="./sample1.jpg" alt="Profile picture" class="profile-img">
          <span class="user-name">Bryan Lacaba</span>
        </div>
      </div>
    </header>
                  <!-- Summary Cards (Total Transactions and Earnings) -->
                        <div class="summary-cards">
                            <div class="card">
                                <h3>Total Transactions</h3>
                                <p><?php echo $total_transactions; ?></p> <!-- Display dynamic total transactions -->
                            </div>
                            <div class="card">
                                <h3>Free Books</h3>
                                <p><?php echo $book_counts['free']['total_books']; ?> Books</p> <!-- Display Free Books Count -->
                            </div>
                            <div class="card">
                                <h3>Premium Books</h3>
                                <p><?php echo $book_counts['premium']['total_books']; ?> Books</p> <!-- Display Premium Books Count -->
                            </div>
                            <div class="card">
                                <h3>Paid Books</h3>
                                <p><?php echo $book_counts['paid']['total_books']; ?> Books</p> <!-- Display Paid Books Count -->
                            </div>
                        </div>

    <div class="header-row">
      <h1>Book List</h1>
      <div class="right-controls">
        <form method="get" action="booklist.php" class="search-bar">
          <input type="text" class="form-control" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        </form>

        <form method="get" action="booklist.php" class="sort-dropdown">
          <select name="genre" class="form-control" onchange="this.form.submit()">
            <option value="all" <?php if ($genreFilter == 'all') echo 'selected'; ?>>Sort by Genre</option>
            <option value="fiction" <?php if ($genreFilter == 'fiction') echo 'selected'; ?>>Fiction</option>
            <option value="dystopian" <?php if ($genreFilter == 'dystopian') echo 'selected'; ?>>Dystopian</option>
            <option value="fantasy" <?php if ($genreFilter == 'fantasy') echo 'selected'; ?>>Fantasy</option>
            <option value="non-fiction" <?php if ($genreFilter == 'non-fiction') echo 'selected'; ?>>Non-Fiction</option>
          </select>
        </form>

        <button class="add-book-btn btn btn-primary" onclick="location.href='add-book.html'">Add Book</button>
      </div>
    </div>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Book Cover</th>
            <th>Book ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Publisher</th>
            <th>ISBN</th>
            <th>Genre</th>
            <th>Plan</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
         <?php
foreach ($books as $book) {
    // Ensure lowercase for consistency
    $planType = strtolower($book['Plan_type']);
    $filename = basename($book['Book_Cover']); // Extract filename

    // Sanitize filename (removing special characters)
    $filename = preg_replace("/[^a-zA-Z0-9._-]/", "", $filename);

    // URL-encode the filename to handle special characters (like apostrophes or spaces)
    $encodedFilename = urlencode($filename);

    // Define base directory and image path
    $baseDir = $_SERVER['DOCUMENT_ROOT'] . "/BryanCodeX/Book/";
    $imagePath = $baseDir . ucfirst($planType) . "/Book_Cover/" . $filename;
    $imageUrl = "/BryanCodeX/Book/" . ucfirst($planType) . "/Book_Cover/" . $encodedFilename;

    // Construct the preview path for the View button
    $previewPath = "Book/" . ucfirst(strtolower($book['Plan_type'])) . "/Preview/";
    if (isset($book['ISBN'])) {
        $previewPath .= $book['ISBN'] . ".php"; // Link to the preview page using ISBN
    } else {
        $previewPath .= "default.php"; // Use a default preview page if ISBN is missing
    }

    echo '<tr>';
    if (!empty($book['Book_Cover']) && file_exists($imagePath)) {
        echo '<td><img src="' . htmlspecialchars($imageUrl) . '" alt="' . htmlspecialchars($book['Title']) . ' Cover" width="50"></td>';
    } else {
        // Show a placeholder if the image doesn't exist
        echo '<td><img src="/path_to_placeholder_image.jpg" alt="Placeholder Image" width="50"></td>';
    }

    echo '<td>' . $book['Book_ID'] . '</td>';
    echo '<td>' . $book['Title'] . '</td>';
    echo '<td>' . $book['Author'] . '</td>';
    echo '<td>' . $book['Publisher'] . '</td>';
    echo '<td>' . $book['ISBN'] . '</td>';
    echo '<td>' . $book['Genre'] . '</td>';
    echo '<td><span class="badge badge-' . getBadgeClass($book['Plan_type']) . '">' . $book['Plan_type'] . '</span></td>';
    echo '<td>' . $book['display_price'] . '</td>';  // Use display_price
    echo '<td>' . $book['Stock'] . '</td>';
    echo '<td>
              <div class="action-buttons">
                  <a href="' . htmlspecialchars($previewPath) . '" class="btn btn-info btn-sm">View</a>  <!-- View button links to preview page -->
                  <button class="btn btn-warning btn-sm" onclick="updateBook(\'' . $book['Book_ID'] . '\')">Update</button>
                  <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteConfirmationModal" data-book-id="' . $book['Book_ID'] . '">Delete</button>
              </div>
          </td>';
    echo '</tr>';
}

function getBadgeClass($planType) {
    switch (strtolower($planType)) {
        case 'free': return 'success';
        case 'premium': return 'primary';
        case 'paid': return 'warning';
        default: return 'secondary';
    }
}
?>

        </tbody>
      </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Book</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this book? This action cannot be undone.
          </div>
          <div class="modal-footer">
           
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
             <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          </div>
        </div>
      </div>
    </div>
  </main>

   <script>
      $(document).ready(function() {
      let bookIdToDelete;

      $('#deleteConfirmationModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget); // Button that triggered the modal
        bookIdToDelete = button.data('book-id'); // Extract info from data-* attributes
      });

      $('#confirmDeleteBtn').click(function() {
        if (bookIdToDelete) {
          $.ajax({
            url: '/BryanCodeX/process/admin/delete_book.php', // Corrected URL - ROOT RELATIVE
            type: 'POST',
            data: { book_id: bookIdToDelete },
            success: function(response) {
              $('#deleteConfirmationModal').modal('hide');
              alert(response); // Show response from the server
              location.reload(); // Reload the page to reflect changes
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.error("Error deleting book:", textStatus, errorThrown);
              alert("An error occurred while deleting the book.");
            }
          });
        }
      });
    });
  </script>

</body>
</html>
