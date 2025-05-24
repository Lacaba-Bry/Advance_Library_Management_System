<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book List | Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="css/adminheader.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
      --warning: #ffc107;
    }

    .summary-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      padding: 20px;
      margin-top: 10px;
    }

    .summary-cards .card {
      flex: 1 1 150px;
      background: var(--card-bg);
      border: 1px solid var(--border-clr);
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
      text-align: center;
    }

    .summary-cards .card h3 {
      font-size: 14px;
      color: var(--muted-clr);
      margin-bottom: 8px;
    }

    .summary-cards .card p {
      font-size: 20px;
      font-weight: 600;
      color: var(--text-clr);
      margin: 0;
    }

    @media (max-width: 600px) {
      .summary-cards {
        flex-direction: column;
      }
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

    .search-bar input,
    .sort-dropdown select {
      padding: 10px 15px;
      border: 1px solid var(--border-clr);
      border-radius: 6px;
      font-size: 14px;
    }

    .add-book-btn {
      background-color: var(--primary-clr);
      color: white;
      padding: 10px 15px;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      text-align: center;
      display: inline-block;
    }

    .table-container {
      overflow-x: auto;
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

    /* Button Styles */
    .action-buttons {
      display: flex;
      gap: 8px;
    }

    .action-buttons button {
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 6px;
      cursor: pointer;
      border: none;
    }

    .action-buttons button.view {
      background-color: #17a2b8;
      color: white;
    }

    .action-buttons button.view:hover {
      background-color: #138496;
    }

    .action-buttons button.update {
      background-color: #ffc107;
      color: white;
    }

    .action-buttons button.update:hover {
      background-color: #e0a800;
    }

    .action-buttons button.delete {
      background-color: #dc3545;
      color: white;
    }

    .action-buttons button.delete:hover {
      background-color: #c82333;
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

    <!-- Summary Cards Section -->
    <div class="summary-cards">
      <div class="card">
        <h3>Total Books</h3>
        <p>10</p>
      </div>
      <div class="card">
        <h3>Free Books</h3>
        <p>3</p>
      </div>
      <div class="card">
        <h3>Premium Books</h3>
        <p>5</p>
      </div>
      <div class="card">
        <h3>Paid Books</h3>
        <p>2</p>
      </div>
    </div>

  <div class="header-row">
      <h1>Book List</h1>
      <div class="right-controls">
        <div class="search-bar">
          <input type="text" id="searchInput" class="form-control" placeholder="Search by title or author...">
        </div>
        <div class="sort-dropdown">
          <select id="genreSort" class="form-control">
            <option value="all">Sort by Genre</option>
            <option value="fiction">Fiction</option>
            <option value="dystopian">Dystopian</option>
            <option value="fantasy">Fantasy</option>
            <option value="non-fiction">Non-Fiction</option>
          </select>
        </div>
        <button class="add-book-btn btn btn-primary" onclick="location.href='add-book.html'">Add Book</button>
      </div>
    </div>

    <!-- Table for Book List -->
    <div class="table-container">
      <table id="bookTable" class="table table-striped">
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
            <th>Stock</th>
            <th>Actions</th> <!-- Added Actions Column -->
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><img src="https://example.com/gatsby.jpg" alt="The Great Gatsby Cover" width="50"></td>
            <td>B001</td>
            <td>The Great Gatsby</td>
            <td>F. Scott Fitzgerald</td>
            <td>Scribner</td>
            <td>9780743273565</td>
            <td>Fiction</td>
            <td><span class="badge badge-success">Free</span></td>
            <td>5</td>
            <td>
              <div class="action-buttons">
                <button class="btn btn-info btn-sm" onclick="viewBook('B001')">View</button>
                <button class="btn btn-warning btn-sm" onclick="updateBook('B001')">Update</button>
                <button class="btn btn-danger btn-sm" onclick="deleteBook('B001')">Delete</button>
              </div>
            </td>
          </tr>
          <tr>
            <td><img src="https://example.com/1984.jpg" alt="1984 Cover" width="50"></td>
            <td>B002</td>
            <td>1984</td>
            <td>George Orwell</td>
            <td>Secker & Warburg</td>
            <td>9780451524935</td>
            <td>Dystopian</td>
            <td><span class="badge badge-success">Premium</span></td>
            <td>3</td>
            <td>
              <div class="action-buttons">
                <button class="btn btn-info btn-sm" onclick="viewBook('B002')">View</button>
                <button class="btn btn-warning btn-sm" onclick="updateBook('B002')">Update</button>
                <button class="btn btn-danger btn-sm" onclick="deleteBook('B002')">Delete</button>
              </div>
            </td>
          </tr>
          <tr>
            <td><img src="https://example.com/catcher.jpg" alt="The Catcher in the Rye Cover" width="50"></td>
            <td>B003</td>
            <td>The Catcher in the Rye</td>
            <td>J.D. Salinger</td>
            <td>Little, Brown and Company</td>
            <td>9780316769488</td>
            <td>Fiction</td>
            <td><span class="badge badge-warning">Paid</span></td>
            <td>2</td>
            <td>
              <div class="action-buttons">
                <button class="btn btn-info btn-sm" onclick="viewBook('B003')">View</button>
                <button class="btn btn-warning btn-sm" onclick="updateBook('B003')">Update</button>
                <button class="btn btn-danger btn-sm" onclick="deleteBook('B003')">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <script>
     function viewBook(bookId) {
      alert("Viewing book: " + bookId);
    }

    function updateBook(bookId) {
      alert("Updating book: " + bookId);
    }

    function deleteBook(bookId) {
      if (confirm("Are you sure you want to delete this book?")) {
        alert("Book " + bookId + " deleted.");
      }
    }
  </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
