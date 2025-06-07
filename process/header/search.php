<?php
require_once __DIR__ . '/config/config.php';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = "%" . $_GET['search'] . "%"; // Use wildcards for LIKE query

    // Perform a database query to fetch matching results
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? LIMIT 5");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any results are found
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='search-result'>";
            echo "<h4>" . htmlspecialchars($row['title']) . "</h4>";
            echo "<p>By: " . htmlspecialchars($row['author']) . "</p>";
            echo "<a href='book_detail.php?id=" . $row['id'] . "' class='btn'>View Details</a>";  // Link to the book detail page
            echo "</div>";
        }
    } else {
        echo "<p>No results found for '<strong>" . htmlspecialchars($_GET['search']) . "</strong>'.</p>";
    }
} else {
    echo "<p>Please enter a search term.</p>";
}
?>
