<?php
session_start();
require_once('../../backend/config/config.php');  
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookId = $_POST['book_id'] ?? null;
    $userId = $_POST['user_id'] ?? null;
    $voteValue = $_POST['vote_value'] ?? 1; // Default to 1

    if ($bookId && $userId) {
        // Check if the user has already voted
        $check = $conn->prepare("SELECT * FROM votes WHERE Book_ID = ? AND Account_ID = ?");
        $check->bind_param("ii", $bookId, $userId);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            // Vote exists – remove it (toggle off)
            $delete = $conn->prepare("DELETE FROM votes WHERE Book_ID = ? AND Account_ID = ?");
            $delete->bind_param("ii", $bookId, $userId);
            $delete->execute();
            $delete->close();

            // Get updated vote count
            $count = $conn->prepare("SELECT COUNT(*) as total_votes FROM votes WHERE Book_ID = ?");
            $count->bind_param("i", $bookId);
            $count->execute();
            $totalVotes = $count->get_result()->fetch_assoc();
            $count->close();

            echo json_encode(['success' => true, 'new_vote_count' => $totalVotes['total_votes'], 'voted' => false]);
        } else {
            // No vote yet – insert new vote
            $insert = $conn->prepare("INSERT INTO votes (Book_ID, Account_ID, Vote_Value) VALUES (?, ?, ?)");
            $insert->bind_param("iii", $bookId, $userId, $voteValue);
            if ($insert->execute()) {
                // Get updated vote count
                $count = $conn->prepare("SELECT COUNT(*) as total_votes FROM votes WHERE Book_ID = ?");
                $count->bind_param("i", $bookId);
                $count->execute();
                $totalVotes = $count->get_result()->fetch_assoc();
                $count->close();

                echo json_encode(['success' => true, 'new_vote_count' => $totalVotes['total_votes'], 'voted' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to register vote.']);
            }
            $insert->close();
        }

        $check->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
