<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Store</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    body {
      background-color: #f9f9fb;
    }

    .navbar {
      background-color: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar .container-fluid {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .search-bar {
      width: 80%;
    }

    .search-icon {
      background: transparent;
      border: none;
      cursor: pointer;
    }

    .navbar .d-flex a {
      color: #333;
      text-decoration: none;
    }

    .navbar .d-flex a:hover {
      color: #007bff;
    }

    .btn-outline-dark {
      border: 1px solid #007bff;
      color: #007bff;
      padding: 5px 10px;
    }

    .btn-outline-dark:hover {
      background-color: #007bff;
      color: white;
    }

    .book-card {
      position: relative;
      border-radius: 10px;
      overflow: hidden;
      background: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: transform 0.2s ease;
    }

    .book-card:hover {
      transform: scale(1.02);
    }

    .favorite-icon {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #fff;
      border-radius: 50%;
      padding: 6px;
      z-index: 10;
      color: #666;
      cursor: pointer;
    }

    .book-img {
      height: 250px;
      width: 100%;
      object-fit: cover;
    }

    .price-old {
      text-decoration: line-through;
      color: #888;
      font-size: 0.9em;
    }

    .sidebar {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .sidebar h6 {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .sidebar .form-check {
      margin-bottom: 8px;
    }
  </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Haven Library</a>
    
    <!-- Search Bar -->
    <form class="d-flex mx-auto w-50">
      <input class="form-control me-2" type="search" placeholder="Search for books, titles, authors..." aria-label="Search">
      <button class="search-icon btn btn-outline-dark ms-2">🔍</button>
    </form>
    
    <!-- Auth Buttons: Heart, Cart, Profile, Logout -->
    <div class="d-flex">
      <!-- Favorite Icon -->
      <a href="#" class="me-3 text-dark">
        <i class="fa-regular fa-heart fa-lg"></i>
      </a>
      
      <!-- Cart Icon -->
      <a href="#" class="me-3 text-dark">
        <i class="fa-solid fa-cart-shopping fa-lg"></i>
      </a>
      
      <!-- Profile Icon -->
      <a href="#" class="me-3 text-dark">
        <img src="https://via.placeholder.com/32" class="rounded-circle" alt="Profile">
      </a>
      
      <!-- Logout Button -->
      <button class="btn btn-outline-dark">Logout</button>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container-fluid py-4">
  <div class="row">
    
    <!-- Left Sidebar -->
    <div class="col-md-3 mb-4">
      <div class="sidebar">
        <h6>Editor Picks</h6>
        <ul class="list-unstyled small">
          <li><a href="#">Best Sales (105)</a></li>
          <li><a href="#">Most Commented (21)</a></li>
          <li><a href="#">Newest Books (32)</a></li>
          <li><a href="#">Featured (129)</a></li>
          <li><a href="#">Watch History (21)</a></li>
          <li><a href="#">Best Books (44)</a></li>
        </ul>

        <hr>

        <h6>Choose Publisher</h6>
        <select class="form-select mb-3">
          <option selected>All Publishers</option>
          <option>Publisher A</option>
          <option>Publisher B</option>
        </select>

        <h6>Select Year</h6>
        <select class="form-select mb-3">
          <option selected>All Years</option>
          <option>2025</option>
          <option>2024</option>
          <option>2023</option>
        </select>

        <h6>Shop by Category</h6>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat1">
          <label class="form-check-label" for="cat1">Action</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat2">
          <label class="form-check-label" for="cat2">Comedy</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat3">
          <label class="form-check-label" for="cat3">Horror</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cat4">
          <label class="form-check-label" for="cat4">Fantasy</label>
        </div>
        <!-- Add more categories as needed -->
      </div>
    </div>

    <!-- Books Grid -->
    <div class="col-md-9">
      <div class="row g-4">
        <!-- Example Book -->
        <div class="col-md-4">
          <div class="book-card p-2">
            <span class="favorite-icon"><i class="fa-regular fa-heart"></i></span>
            <img src="https://via.placeholder.com/300x400?text=Thunder+Stunt" class="book-img" alt="Book Cover">
            <div class="p-2">
              <h6 class="mb-1">Thunder Stunt</h6>
              <p class="text-muted small mb-1">Adventure, Science, Comedy</p>
              <div><strong>$54.78</strong> <span class="price-old">$70.00</span></div>
              <div class="mt-2 text-warning">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
              </div>
              <button class="btn btn-primary btn-sm w-100 mt-2"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
            </div>
          </div>
        </div>

        <!-- Duplicate this block for more books -->
      </div>
    </div>

  </div>
</div>

</body>
</html>
