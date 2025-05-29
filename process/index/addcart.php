<?php
require_once __DIR__ . '/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$accountId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $bookId = (int)$_POST['book_id'];
    $isFavorited = isset($_POST['is_favorited']) && $_POST['is_favorited'] == 1;

    if ($isFavorited) {
        // Remove from favorites
        $sql = "DELETE FROM favorites WHERE account_id = ? AND book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $accountId, $bookId);
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Removed from favorites']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        // Add to favorites
        $sql = "INSERT INTO favorites (account_id, book_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $accountId, $bookId);
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Added to favorites']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        $stmt->close();
    }
} else {
    header("HTTP/1.1 400 Bad Request");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>