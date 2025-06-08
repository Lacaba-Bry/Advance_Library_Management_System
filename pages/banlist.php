<?php
require_once(__DIR__ . '/../backend/config/config.php');

// Fetch banned users with details from the register table
$query = "SELECT b.id, r.Fullname, b.email, b.date_banned, b.status 
          FROM banned_users b 
          JOIN register r ON r.email = b.email";
$stmt = $conn->prepare($query);
$stmt->execute();
$bannedUsersResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ban List | Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/adminheader.css">
  <style>
    :root {
        --primary-clr: #5e63ff;
        --bg-clr: #f9f9f9;
        --card-bg: #ffffff;
        --text-clr: #333;
        --muted-clr: #777;
        --border-clr: #e0e0e0;
        --success: #28a745;
        --danger: #dc3545;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: var(--bg-clr);
        color: var(--text-clr);
        margin: 0;
        padding: 0;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 20px 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .header-row h1 {
        margin: 0;
        font-size: 24px;
    }

    .right-controls {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .ban-button {
        padding: 8px 16px;
        font-size: 14px;
        border: none;
        background-color: var(--danger);
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .ban-button:hover {
        background-color: #c82333;
    }

    .table-container {
        overflow-x: auto;
        margin: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card-bg);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    th, td {
        padding: 14px 18px;
        text-align: left;
        border-bottom: 1px solid var(--border-clr);
    }

    th {
        background: #f1f1f1;
        font-weight: 600;
    }

    tr:hover {
        background-color: #f9f9f9;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge.active {
        background-color: #e6f4ea;
        color: var(--success);
    }

    .badge.inactive {
        background-color: #fdecea;
        color: var(--danger);
    }

    @media (max-width: 600px) {
        th, td {
            font-size: 13px;
            padding: 10px;
        }

        .ban-button {
            font-size: 12px;
        }
    }
  </style>
</head>
<body>
<main>
    <header class="header">
    <span class="logo-section">
      <span class="logo">Home</span>
    </span>

    <div class="user-info">
       <div class="user-profile">
            <img src="./sample1.jpg" alt="Profile picture of Arafat Hossain" class="profile-img">
            <span class="user-name">Bryan Lacaba</span>
        </div>
    </div>
  </header>

  <div class="header-row">
    <h1>Ban List</h1>
    <div class="right-controls">
        <button class="ban-button" data-bs-toggle="modal" data-bs-target="#banModal">Ban Account</button>
    </div>
  </div>

  <div class="table-container">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date Banned</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($bannedUser = $bannedUsersResult->fetch_assoc()): ?>
            <tr>
                <td><?php echo $bannedUser['id']; ?></td>
                <td><?php echo htmlspecialchars($bannedUser['Fullname']); ?></td>
                <td><?php echo htmlspecialchars($bannedUser['email']); ?></td>
                <td><?php echo date('m/d/Y', strtotime($bannedUser['date_banned'])); ?></td>
                <td><span class="badge inactive">Banned</span></td>
                <td><button class="ban-button" data-user-id="<?php echo $bannedUser['id']; ?>" onclick="unbanUser(this)">Unban</button></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
  </div>

</main>

<!-- Ban Account Modal -->
<div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="banModalLabel">Ban Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="banForm" method="POST">
          <div class="mb-3">
            <label for="userEmail" class="form-label">Enter Email to Ban:</label>
            <input type="email" class="form-control" id="userEmail" name="email" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Ban Account</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// JavaScript to handle banning of users
function unbanUser(button) {
    const userId = button.getAttribute('data-user-id'); // Get the user ID from data attribute

    if (confirm('Are you sure you want to unban this user?')) {
        const formData = new FormData();
        formData.append('user_id', userId);

        fetch('process/admin/unban_user.php', { // Corrected path
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('User unbanned successfully!')) { // Check for specific success message
                alert('User has been unbanned!');
                window.location.reload();  // Reload the page to show the updated status
            } else {
                alert('Error: ' + data);
                console.error('Unban error:', data); // Log the error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error unbanning the user.');
        });
    }
}

// JavaScript to handle Ban Account form submission
document.getElementById('banForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    const userEmail = document.getElementById('userEmail').value;

    const formData = new FormData();
    formData.append('email', userEmail);

    fetch('process/admin/ban_user.php', { // Corrected path (already correct)
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
        .then(data => {
            if (data.includes('User has been banned successfully!')) { // Check for specific success message
                alert('User has been banned!');
                window.location.reload(); // Reload the page to reflect the changes
            } else {
                alert('Error: ' + data);
                console.error('Ban error:', data); // Log the error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was an error banning the user.');
        });
});
</script>


</body>
</html>
