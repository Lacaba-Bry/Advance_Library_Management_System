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


// Query to get the total number of books
$total_books_query = "SELECT COUNT(*) AS total_books FROM books";
$total_books_result = $conn->query($total_books_query);
$total_books = 0; // Default value if the query fails

if ($total_books_result) {
    $row = $total_books_result->fetch_assoc();
    $total_books = $row['total_books'];
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
  <link rel="stylesheet" href="css/adminhomex.css">
   <script type="text/javascript" src="javascript/adminhome.js" defer></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
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
                                <h3>Total Books</h3>
                                <p><?php echo $total_books; ?></p> <!-- Display dynamic total transactions -->
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
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
          Add New Book
        </button>

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
                 <button class="btn btn-warning btn-sm restock-button" data-book-id="' . $book['Book_ID'] . '">Restock</button>
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

<!-- Restock Book Modal (Appears First) -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="restockModalLabel">Restock Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="restockForm" method="post">
          <input type="hidden" name="book_id" id="restockBookId" />
          <div class="mb-3">
            <label for="restockQuantity" class="form-label">Enter Quantity to Restock:</label>
            <input type="number" class="form-control" id="restockQuantity" name="restockQuantity" min="1" required />
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Restock</button>
          </div>
        </form>
      </div>
    </div>
  </div>
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









  <!-- Add New Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
     <form id="addBookForm" enctype="multipart/form-data" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <input type="text" class="form-control" name="Title" placeholder="Title" required>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Author" placeholder="Author" required>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Publisher" placeholder="Publisher">
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="ISBN" placeholder="ISBN">
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Genre" placeholder="Genre">
          </div>
         <div class="mb-3">
          <label for="PlanSelect" class="form-label">Plan_type</label>
          <select class="form-select" id="PlanSelect" name="Plan_type" required>
            <option value="" disabled selected>Plan Type</option>
            <option value="Free">Free</option>
            <option value="Premium">Premium</option>
            <option value="Paid">Paid</option> <!-- Added Paid option -->
          </select>
        </div>
        
        <!-- Price Input (Initially hidden) -->
        <div class="mb-3" id="priceField">
          <input type="number" class="form-control" name="Price" placeholder="Price" step="0.01" min="0">
        </div>

        <!-- Stock Input -->
        <div class="mb-3">
          <input type="number" class="form-control" name="Stock" placeholder="Stock" min="0" required>
        </div>

        <div class="mb-3">
            <label for="bookCover" class="form-label">Book Cover</label>
            <input type="file" class="form-control" id="bookCover" name="Book_Cover" accept="image/*">
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Story_Snippet" placeholder="Story Snippet">
          </div>
          <div class="mb-3">
            <textarea class="form-control" name="Description" placeholder="Description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <textarea class="form-control" name="Story" placeholder="Story" rows="5"></textarea>
          </div>
          <div class="mb-3">
            <label for="filePath" class="form-label">Upload File</label>
            <input type="file" class="form-control" id="filePath" name="File_Path">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Book</button>
        </div>
      </form>
    </div>
  </div>
</div>



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

    
document.addEventListener('DOMContentLoaded', () => {
  const planSelect = document.getElementById('PlanSelect');
  const priceField = document.getElementById('priceField');
  const priceInput = priceField.querySelector('input');

  // Function to handle Plan Type change
  function handlePlanChange() {
    if (planSelect.value === 'Paid') {
      priceField.style.display = 'block'; // Show the price input
      priceInput.required = true; // Make price field required
    } else {
      priceField.style.display = 'none'; // Hide the price input
      priceInput.required = false; // Make price field not required
      priceInput.value = 0; // Set price to 0 for Free and Premium
    }
  }

  // Set initial state based on the default value
  handlePlanChange();

  // Listen for changes in Plan type
  planSelect.addEventListener('change', handlePlanChange);
});

document.addEventListener('DOMContentLoaded', () => {
  const addBookForm = document.getElementById('addBookForm');

  addBookForm.addEventListener('submit', async function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(addBookForm);

    try {
      const response = await fetch('process/addnewbook.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.text(); // Expecting plain text response
      alert(result); // Show success or error message

      addBookForm.reset(); // Optional: clear form after submission
      const modal = bootstrap.Modal.getInstance(document.getElementById('addBookModal'));
      modal.hide(); // Hide the modal
    } catch (error) {
      alert("Error submitting form: " + error.message);
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
    const restockButtons = document.querySelectorAll('.restock-button');
    restockButtons.forEach(button => {
        button.addEventListener('click', function () {
            const bookId = this.getAttribute('data-book-id');
            document.getElementById('restockBookId').value = bookId; // Set the book ID for the restock form
            $('#restockModal').modal('show'); // Show the restock modal
        });
    });

    // Handle restocking when form is submitted
    document.getElementById('restockForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        const formData = new FormData(this);

        fetch('/BryanCodeX/backend/reserve_book_notification.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Show success or error message
            $('#restockModal').modal('hide'); // Close the modal
            location.reload(); // Reload the page to reflect the changes
        })
        .catch(error => {
            console.error("Error restocking book:", error);
            alert("An error occurred while restocking the book.");
        });
    });
});

  </script>

</body>
</html>
