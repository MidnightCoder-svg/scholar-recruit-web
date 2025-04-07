
<?php
// Database connection parameters
$host = "localhost";
$username = "root";  // replace with your database username
$password = "";      // replace with your database password
$database = "scholar_recruit";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
