<?php
require_once('backend/config/config.php');

?>


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
    


  </style>
<body>

<main>
  </head>
   <header class="header">
        <span class="logo-section">
          <span class="logo">Home</span>
        </span>

        <div class="search-bar">
          <input type="text" placeholder="Search ..." aria-label="Search">
        </div>

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






<div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg for wider modal -->
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
  <label for="statusSelect" class="form-label">Status</label>
  <select class="form-select" id="statusSelect" name="Status" required>
    <option value="" disabled selected>Select status</option>
    <option value="Free">Free</option>
    <option value="Premium">Premium</option>
  </select>
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


<div class="modal hidden" id="myModal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h2>Confirm Action</h2>
    <p>Are you sure you want to continue?</p>
    <div class="modal-actions">
      <button class="btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn-primary">Confirm</button>
    </div>
  </div>
</div>

<script>


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