<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="css/adminhomex.css">
   <link rel="stylesheet" href="css/adminheader.css">
   <script type="text/javascript" src="javascript/adminhome.js" defer></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
  <title>Document</title>
  <style>
    /* Additional styles for the Price input field */
    #priceField {
      display: none;
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

    <div class="dashboard-header">
        <p>Jan 12, 2023 | Thursday, 11:00</p>
    </div>

    <div class="stats-box">
        <div class="stat-box">
            <h2>1223</h2>
            <p>Total Visitors</p>
        </div>
        <div class="stat-box">
            <h2>740</h2>
            <p>Borrowed Books</p>
        </div>
        <div class="stat-box">
            <h2>22</h2>
            <p>Overdue Books</p>
        </div>
        <div class="stat-box">
            <h2>60</h2>
            <p>New Members</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="actions-grid">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
          Add New Book
          </button>

            <button>Register New User</button>
            <button>Issue Book</button>
            <button>Return Book</button>
        </div>
    </div>

    <!-- Notices / Announcements -->
    <div class="announcements">
        <h3>Notices / Announcements</h3>
        <ul>
            <li>System will be down for maintenance at 8 PM.</li>
            <li>New books added in the Science Fiction category.</li>
        </ul>
    </div>
</main>

<!-- Add New Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
     <form id="addBookForm" enctype="multipart/form-data" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <input type="text" class="form-control" name="Title" placeholder="Title" required>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Author" placeholder="Author" required>
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Publisher" placeholder="Publisher">
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="ISBN" placeholder="ISBN">
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Genre" placeholder="Genre">
          </div>
         <div class="mb-3">
          <label for="PlanSelect" class="form-label">Plan_type</label>
          <select class="form-select" id="PlanSelect" name="Plan_type" required>
            <option value="" disabled selected>Plan Type</option>
            <option value="Free">Free</option>
            <option value="Premium">Premium</option>
            <option value="Paid">Paid</option> <!-- Added Paid option -->
          </select>
        </div>
        
        <!-- Price Input (Initially hidden) -->
        <div class="mb-3" id="priceField">
          <input type="number" class="form-control" name="Price" placeholder="Price" step="0.01" min="0">
        </div>

        <!-- Stock Input -->
        <div class="mb-3">
          <input type="number" class="form-control" name="Stock" placeholder="Stock" min="0" required>
        </div>

        <div class="mb-3">
            <label for="bookCover" class="form-label">Book Cover</label>
            <input type="file" class="form-control" id="bookCover" name="Book_Cover" accept="image/*">
          </div>
          <div class="mb-3">
            <input type="text" class="form-control" name="Story_Snippet" placeholder="Story Snippet">
          </div>
          <div class="mb-3">
            <textarea class="form-control" name="Description" placeholder="Description" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <textarea class="form-control" name="Story" placeholder="Story" rows="5"></textarea>
          </div>
          <div class="mb-3">
            <label for="filePath" class="form-label">Upload File</label>
            <input type="file" class="form-control" id="filePath" name="File_Path">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Book</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const planSelect = document.getElementById('PlanSelect');
  const priceField = document.getElementById('priceField');
  const priceInput = priceField.querySelector('input');

  // Function to handle Plan Type change
  function handlePlanChange() {
    if (planSelect.value === 'Paid') {
      priceField.style.display = 'block'; // Show the price input
      priceInput.required = true; // Make price field required
    } else {
      priceField.style.display = 'none'; // Hide the price input
      priceInput.required = false; // Make price field not required
      priceInput.value = 0; // Set price to 0 for Free and Premium
    }
  }

  // Set initial state based on the default value
  handlePlanChange();

  // Listen for changes in Plan type
  planSelect.addEventListener('change', handlePlanChange);
});


document.addEventListener('DOMContentLoaded', () => {
  const addBookForm = document.getElementById('addBookForm');

  addBookForm.addEventListener('submit', async function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(addBookForm);

    try {
      const response = await fetch('process/addnewbook.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.text(); // Expecting plain text response
      alert(result); // Show success or error message

      addBookForm.reset(); // Optional: clear form after submission
      const modal = bootstrap.Modal.getInstance(document.getElementById('addBookModal'));
      modal.hide(); // Hide the modal
    } catch (error) {
      alert("Error submitting form: " + error.message);
    }
  });
});
</script>

</body>
</html>
