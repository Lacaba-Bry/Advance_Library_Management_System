const recommendedBooks = [
  'Books/cpp.jpg', 'Books/Java.jpg', 'Books/Python.jpeg', 'Books/Csharp.jpg', 'Books/Ruby.jpg'
];


  // Array of popular books with their image paths
  const popularBooks = [
    'Books/Popular2025/Php.jpg', 'Books/Popular2025/Data.jpg', 'Books/Popular2025/Sql.jpg', 'Books/Popular2025/Ubuntu.jpg', 'Books/Popular2025/Git.jpg'
  ];

  // Initial indexes to track which set of books to render
  let recommendedIndex = 0;
  let popularIndex = 0;

  // Function to render books to the specified container
  function renderBooks(containerId, books, index) {
    const container = document.getElementById(containerId);  // Get the container element by ID
    container.innerHTML = ''; // Clear current content
    const current = books.slice(index, index + 4); // Get a slice of 4 books starting from index
    current.forEach((imgSrc, i) => {
      const div = document.createElement('div'); // Create a new div for each book
      div.className = 'book-card ' + ['red', 'blue', 'yellow', 'pink'][i % 4];  // Assign a color class to the book card

      // Create an image element for the book cover
      const img = document.createElement('img');
      img.src = imgSrc; // Set the image source to the current book cover
      img.alt = 'Book Cover'; // Alt text for accessibility
      img.className = 'book-cover'; // Optional: Add a class for styling

      div.appendChild(img); // Append the image to the div
      container.appendChild(div); // Append the div to the container
    });
  }
 // Function to change the book set by incrementing or decrementing the index
  function changeSet(type, direction) {
    const books = type === 'recommended' ? recommendedBooks : popularBooks; // Choose the books array based on the type
    const maxIndex = Math.max(0, books.length - 4);  // Calculate the maximum index to prevent out-of-bounds
    if (type === 'recommended') {
      recommendedIndex = Math.min(Math.max(0, recommendedIndex + direction * 4), maxIndex);  // Update the recommended index
      renderBooks('recommended-set', books, recommendedIndex);  // Render the recommended books
    } else {
      popularIndex = Math.min(Math.max(0, popularIndex + direction * 4), maxIndex);  // Update the popular index
      renderBooks('popular-set', books, popularIndex);  // Render the popular books
    }
  }