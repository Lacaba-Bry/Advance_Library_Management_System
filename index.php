<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/Index.css">
  <title>Book Store - Student Offer</title>
</head>
<body>
  <header>
    <div class="logo">Haven Library</div>

    <div class="search-container">
      <div class="dropdown">
        <button class="dropbtn">☰ Menu</button>
        <div class="dropdown-content">
          <a href="#">Home</a>
          <a href="#">Categories</a>
          <a href="#">About</a>
        </div>
      </div>

      <input type="text" placeholder="Search for books, titles, authors..." class="search-bar">
      <button class="search-icon">🔍</button>
    </div>

    <div class="auth-buttons">
      <a href="pricing.php"><button class="btn">Pricing</button></a>
    <button class="btn" onclick="openModal('loginModal')">Login</button>
    <button class="btn" onclick="openModal('registerModal')">Sign Up</button>
    </div>
  </header>

<section class="hero">
  <div class="hero-content">
    <div class="hero-text">
      <h4>BACK TO SCHOOL</h4>
      <h1>Welcome to Haven Library</h1>
      <p>Lyour digital sanctuary for knowledge and learning. <br>
      Whether you're an avid reader, a student, or just curious,<br>
      we offer a vast collection of books, articles, and resources <br>
      across various genres and subjects. 
      <br>Our platform provides easy access to thousands of titles from the comfort of your home.</p>
      <div class="hero-buttons">
        <button class="btn">Get the deal</button>
        <button class="btn btn-outline">See other promos</button>
      </div>
    </div>
  </div>

  <div class="best-seller">
    <div class="seller-text">
      
      <h1>Best Seller</h1>
      <p>Base Sale This Week</p>
      <img src="Rain.jpg" alt="Bestsell">
      <h4>The Rain in Espana</h4>
      <div class="seller-buttons">
        <button class="btn">USD 20.25</button>
      </div>
    </div>
  </div>
</section>


<section class="features">
<div class="feature-item">
    <div class="feature-title">
      <img src="IndexImage/Booksx.png" alt="Icon">
      <div class="text-block">
        <h4>Premium Selection</h4>
        <p>high-quality books.</p>
      </div>
    </div>
  </div>
  <div class="feature-item">
  <div class="feature-title">
  <img src="IndexImage/Books.png" alt="Icon">
  <div class="text-block">
    <h4>Flexible Reading</h4>
    <p>Read anytime, anywhere</p>
    </div>
    </div>
  </div>
  <div class="feature-item">
  <div class="feature-title">
  <img src="IndexImage/Secure.png" alt="Icon"> 
  <div class="text-block">
    <h4>Secure Payment</h4>
    <p>Your info is safe with us.</p>
    </div>
    </div>
  </div>
  <div class="feature-item">
  <div class="feature-title">
  <img src="IndexImage/Quality.png" alt="Icon"> 
  <div class="text-block">
    <h4>Best Quality</h4>
    <p>Only verified materials.</p>
    </div>
    </div>
  </div>
  <div class="feature-item">
  <div class="feature-title">
  <img src="IndexImage/Return.png" alt="Icon"> 
  <div class="text-block">
    <h4>Return Guarantee</h4>
    <p>Hassle-free returns.</p>
    </div>
    </div>
  </div>
</section>


<section class="recommend-popular">
  <div class="carousel-section recommended">
    <h2>Recommended For You</h2>
    <p>Explore curated picks for your next read.</p>
    <div class="carousel-wrapper">
      <button class="carousel-btn left" onclick="changeSet('recommended', -1)">‹</button>
      <div class="book-row" id="recommended-set"></div>
      <button class="carousel-btn right" onclick="changeSet('recommended', 1)">›</button>
    </div>
  </div>

  <div class="carousel-section popular">
    <h2>Popular in 2025</h2>
    <p>Top trending titles this year.</p>
    <div class="carousel-wrapper">
      <button class="carousel-btn left" onclick="changeSet('popular', -1)">‹</button>
      <div class="book-row" id="popular-set"></div>
      <button class="carousel-btn right" onclick="changeSet('popular', 1)">›</button>
    </div>
  </div>
</section>

<section class="viewLibrary">
  <button>Browse Book</button>
</section>

  <!-- Add this section below the <section class="recommend-popular"> -->
<section class="sale-feature-section">
  <div class="sale">
    <h3>Top Free</h3>
    <p>Get amazing discounts on a wide selection of books!</p>
</div>
</section>





<section class="genres">
  <h2>Explore Our Book Genres</h2>
  <p>Find your next great read by exploring our wide selection of genres.</p>
  <div class="genre-cards">
    <div class="genre-card">
      <img src="Books/Genre/Fiction.png" alt="Fiction">
      <h4>Fiction</h4>
    </div>
    <div class="genre-card">
      <img src="Books/Genre/NonFiction.png" alt="Non-Fiction">
      <h4>Non-Fiction</h4>
    </div>
    <div class="genre-card">
      <img src="Books/Genre/SciFi.png" alt="Science Fiction">
      <h4>Science Fiction</h4>
    </div>
    <div class="genre-card">
      <img src="Books/Genre/Fantasy.jpg" alt="Fantasy">
      <h4>Fantasy</h4>
    </div>
    <div class="genre-card">
      <img src="Books/Genre/Mystery.png" alt="Mystery">
      <h4>Mystery</h4>
    </div>
    <div class="genre-card">
      <img src="Books/Genre/Romance.png" alt="Romance">
      <h4>Romance</h4>
    </div>
    <div class="genre-card">
      <img src="Books/Genre/Horror.png" alt="Horror">
      <h4>Horror</h4>
    </div>
  </div>
</section>






<section class="gamified-features">
  <!-- Block 1 -->
  <div class="feature-block">
    <div class="feature-image">
      <img src="IndexImage/DigitalLibrary.png" alt="Code Interface">
    </div>
    <div class="feature-text">
      <h2>Exploration of Different Studies</h2>
      <p>Discover a wide range of academic resources to explore diverse fields of study—all in one integrated library system.</p>
    </div>
  </div>

  <!-- Block 2 -->
  <div class="feature-block reverse">
  <div class="feature-image">
      <img src="IndexImage/Knowledge.png" alt="Python Card">
    </div>
    <div class="feature-text">
      <h2>Enhance Your Knowledge</h2>
      <p>Empower your learning journey with instant access to books, journals, and digital archives that expand your knowledge.</p>
    </div>
  </div>

  <!-- Block 3 -->
  <div class="feature-block">
    <div class="feature-image">
      <img src="IndexImage/stress.png" alt="Minesweeper">
    </div>
    <div class="feature-text">
      <h2>Stress Reduction / Relaxation</h2>
      <p>Find your quiet corner—our library system offers access to calming reads, audiobooks, and cozy environments that support relaxation.</p>
    </div>
  </div>
</section>




<!-- Add the subscription form below the Sale and Featured Section -->
<section class="subscribe-form">
  <h2>Subscribe to Get Updates</h2>
  <div class="input-wrapper">
    <input type="email" placeholder="Enter your email" />
    <button>Subscribe</button>
  </div>
</section>



<!-- Footer Section -->
<footer class="footer">
  <div class="footer-links">
    <a href="#">About Us</a>
    <a href="#">Privacy Policy</a>
    <a href="#">Terms of Service</a>
    <a href="#">Contact</a>
  </div>
  <div class="footer-social">
    <a href="#" class="social-icon">Facebook</a>
    <a href="#" class="social-icon">Instagram</a>
    <a href="#" class="social-icon">Twitter</a>
  </div>
  <p class="footer-text">&copy; 2025 Haven Library. All rights reserved.</p>
</footer>









<!-- MODAL SECTION-->
<!-- Login Modal -->
<div id="loginModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('loginModal')">&times;</span>
    <h2>Login</h2>
    <form action="backend/login.php" method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      
      <!-- Remember Me Checkbox -->
      <div class="remember-me">
        <input type="checkbox" id="rememberMe" name="remember" />
        <label for="rememberMe">Remember Me</label>
        <a href="#" onclick="openForgotPasswordModal()">Forgot Password?</a>
      </div>
      
      <button type="submit">Login</button>
    </form>

    <!-- Forgot Password and Sign Up Link -->
    <div class="login-footer">
      <p>Don't have an account? <a href="#" onclick="switchToRegisterModal()">Sign Up</a></p>
    </div>
  </div>
</div>

<!-- Forgot Password Modal (optional) -->
<div id="forgotPasswordModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('forgotPasswordModal')">&times;</span>
    <h2>Forgot Password</h2>
    <form action="backend/forgot-password.php" method="POST">
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Submit</button>
    </form>
  </div>
</div>


<!-- Register Modal -->
<div id="registerModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('registerModal')">&times;</span>
    <h2>Register</h2>
    
    <!-- ADD ID HERE -->
    <form id="registerForm" action="backend/register.php" method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="text" name="name" placeholder="Full Name" required />
      <input type="text" name="studentnum" placeholder="Student Number" required />
      
      <button type="button" class="btn btn-primary" onclick="redirectToCheckout()">Register</button>
      <input type="hidden" name="plan" id="selectedPlanInput">
    </form>
  </div>
</div>








<script>
  const recommendedBooks = [
    'Books/cpp.jpg', 'Books/Java.jpg', 'Books/Python.jpeg', 'Books/Csharp.jpg', 'Books/Ruby.jpg'
  ];

  const popularBooks = [
    'Books/Popular2025/Php.jpg', 'Books/Popular2025/Data.jpg', 'Books/Popular2025/Sql.jpg', 'Books/Popular2025/Ubuntu.jpg', 'Books/Popular2025/Git.jpg'
  ];

  let recommendedIndex = 0;
  let popularIndex = 0;

  function renderBooks(containerId, books, index) {
    const container = document.getElementById(containerId);
    container.innerHTML = ''; // Clear current content
    const current = books.slice(index, index + 4); // Get a slice of 4 books
    current.forEach((imgSrc, i) => {
      const div = document.createElement('div');
      div.className = 'book-card ' + ['red', 'blue', 'yellow', 'pink'][i % 4];

      // Create an image element for the book cover
      const img = document.createElement('img');
      img.src = imgSrc; // Set the image source to the current book cover
      img.alt = 'Book Cover'; // Alt text for accessibility
      img.className = 'book-cover'; // Optional: Add a class for styling

      div.appendChild(img); // Append the image to the div
      container.appendChild(div); // Append the div to the container
    });
  }

  function changeSet(type, direction) {
    const books = type === 'recommended' ? recommendedBooks : popularBooks;
    const maxIndex = Math.max(0, books.length - 4);
    if (type === 'recommended') {
      recommendedIndex = Math.min(Math.max(0, recommendedIndex + direction * 4), maxIndex);
      renderBooks('recommended-set', books, recommendedIndex);
    } else {
      popularIndex = Math.min(Math.max(0, popularIndex + direction * 4), maxIndex);
      renderBooks('popular-set', books, popularIndex);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    renderBooks('recommended-set', recommendedBooks, recommendedIndex);
    renderBooks('popular-set', popularBooks, popularIndex);
  });
  function openModal(id) {
    document.getElementById(id).style.display = 'block';
  }

  function closeModal(id) {
    document.getElementById(id).style.display = 'none';
  }

  // Close modal on outside click
  window.onclick = function(event) {
    ['loginModal', 'registerModal'].forEach(id => {
      const modal = document.getElementById(id);
      if (event.target === modal) modal.style.display = "none";
    });
  };

  function openModal(id) {
  document.getElementById(id).style.display = 'block';
}

function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}

// Function to switch to Register Modal
function switchToRegisterModal() {
  closeModal('loginModal');
  openModal('registerModal');
}

// Function to open the Forgot Password modal
function openForgotPasswordModal() {
  closeModal('loginModal');
  openModal('forgotPasswordModal');
}

// Close modals on outside click
window.onclick = function(event) {
  ['loginModal', 'registerModal', 'forgotPasswordModal'].forEach(id => {
    const modal = document.getElementById(id);
    if (event.target === modal) modal.style.display = "none";
  });
};
   function redirectToCheckout() {
    const form = document.getElementById('registerForm');

    // Use form.elements to safely access fields
    const email = form.elements['email'].value;
    const name = form.elements['name'].value;
    const studentnum = form.elements['studentnum'].value;

    // Optional: Store data for later use
    sessionStorage.setItem('registerData', JSON.stringify({
      email,
      name,
      studentnum
    }));

    // Redirect to checkout page
    window.location.href = 'transaction/checkout.php';
  }
</script>


</body>
</html>
