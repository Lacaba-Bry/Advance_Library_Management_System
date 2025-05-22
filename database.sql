-- Create database
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'librarian', 'member') NOT NULL,
    department VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'
);

-- Books table
CREATE TABLE books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100),
    publication_year INT,
    category VARCHAR(50),
    total_copies INT NOT NULL,
    available_copies INT NOT NULL,
    location VARCHAR(50),
    status ENUM('available', 'unavailable') DEFAULT 'available',
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Borrowings table
CREATE TABLE borrowings (
    borrowing_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT,
    user_id INT,
    borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date TIMESTAMP NOT NULL,
    return_date TIMESTAMP NULL,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed',
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Reservations table
CREATE TABLE reservations (
    reservation_id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT,
    user_id INT,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'fulfilled', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Categories table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Book Categories (Many-to-Many relationship)
CREATE TABLE book_categories (
    book_id INT,
    category_id INT,
    PRIMARY KEY (book_id, category_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Fines table
CREATE TABLE fines (
    fine_id INT PRIMARY KEY AUTO_INCREMENT,
    borrowing_id INT,
    amount DECIMAL(10,2) NOT NULL,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_date TIMESTAMP NULL,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    FOREIGN KEY (borrowing_id) REFERENCES borrowings(borrowing_id)
);

-- Notifications table
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    message TEXT NOT NULL,
    type ENUM('due_reminder', 'overdue', 'reservation', 'system') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
); 