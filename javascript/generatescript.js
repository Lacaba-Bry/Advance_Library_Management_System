function openModal(modalId) {
    var modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
  }


function submitVote(bookId, userId) {
    if (!userId) {
        alert("Please log in to vote.");
        return;
    }

    const voteButton = document.getElementById("voteBtn");

    fetch('../../../process/index/submit_vote.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `book_id=${bookId}&user_id=${userId}&vote_value=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const voteCountElement = document.getElementById("voteCount");
            voteCountElement.textContent = data.new_vote_count;
            voteButton.classList.add("voted");
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error voting:', error);
        alert("Something went wrong. Try again.");
    });
}