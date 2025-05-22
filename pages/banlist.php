<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Member List | Admin Panel</title>
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

    .search-bar input {
        padding: 10px 15px;
        border: 1px solid var(--border-clr);
        border-radius: 6px;
        font-size: 14px;
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

        .search-bar input {
            font-size: 13px;
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
        <div class="search-bar">
            <input type="text" placeholder="Search by Name or Email...">
        </div>
        <!-- Ban Button added here -->
        <button class="ban-button">Ban Account</button>
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
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Santos</td>
                <td>john@example.com</td>
                <td>02/15/2024</td>
                <td>Repeated violations of community guidelines</td>
                <td><span class="badge inactive">Banned</span></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Mary Cruz</td>
                <td>marycruz@email.com</td>
                <td>02/18/2024</td>
                <td>Spamming</td>
                <td><span class="badge inactive">Banned</span></td>
            </tr>
            <!-- Add more rows as needed -->
        </tbody>
    </table>
  </div>

</main>

</body>
</html>
