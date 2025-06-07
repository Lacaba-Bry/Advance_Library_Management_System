<?php
$host = 'localhost';
$user = 'root'; // Default username for MySQL on XAMPP
$password = ''; // Default password for MySQL on XAMPP (blank by default)
$dbname = 'bryancodex';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL)

?>
