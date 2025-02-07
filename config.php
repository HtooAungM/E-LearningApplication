<?php
// Database connection details
$servername = "localhost"; // Change to your database server, e.g., "127.0.0.1" or "database_host"
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "elearning_db";  // The name of the database to create

// Create connection
$conn = new mysqli($servername, $username, $password);

$conn->select_db($dbname);  // Select the database for future queries

?>
