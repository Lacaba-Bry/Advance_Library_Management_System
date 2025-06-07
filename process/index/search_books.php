<?php
require_once __DIR__ . '/backend/config/config.php';

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

function getBooks($conn, $searchTerm = '') {
    $books = [];
    $sql = "SELECT Book_ID, Title, Author, Book_Cover, Genre, Price, Plan_type, Stock FROM books";
    if ($searchTerm) {
        $sql .= " WHERE Title LIKE ? OR Author LIKE ?";
    }
    $stmt = $conn->prepare($sql);

    if ($searchTerm) {
        $searchTerm = "%$searchTerm%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
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

// Fetch books based on search term
$books = getBooks($conn, $searchTerm);

// Display books dynamically as a response to AJAX
foreach ($books as $book):
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
    </div>
</div>

<?php endforeach; ?>
