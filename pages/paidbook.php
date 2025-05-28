<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Paid Book List | Admin Panel</title>
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
        font-family: 'Inter', sans-serif;
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

    .add-button {
        padding: 8px 16px;
        font-size: 14px;
        border: none;
        background-color: var(--primary-clr);
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .add-button:hover {
        background-color: #4e55e4;
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

    .badge.available {
        background-color: #e6f4ea;
        color: var(--success);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge.unavailable {
        background-color: #fdecea;
        color: var(--danger);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
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
        <img src="./sample1.jpg" alt="Admin profile" class="profile-img">
        <span class="user-name">Admin Panel</span>
      </div>
    </div>
  </header>

  <div class="header-row">
    <h1>Paid Books</h1>
    <div class="right-controls">
      <div class="search-bar">
        <input type="text" placeholder="Search by Title or Author...">
      </div>
      <button class="add-button">Add New Book</button>
    </div>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Author</th>
          <th>Genre</th>
          <th>Price</th>
          <th>Availability</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>The Lost Forest</td>
          <td>Jane Ellis</td>
          <td>Mystery</td>
          <td>$4.99</td>
          <td><span class="badge available">Available</span></td>
        </tr>
        <tr>
          <td>2</td>
          <td>AI & The Future</td>
          <td>David Lim</td>
          <td>Non-Fiction</td>
          <td>$7.50</td>
          <td><span class="badge unavailable">Unavailable</span></td>
        </tr>
        <tr>
          <td>3</td>
          <td>Moonlight Romance</td>
          <td>Eva Torres</td>
          <td>Romance</td>
          <td>$3.99</td>
          <td><span class="badge available">Available</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
