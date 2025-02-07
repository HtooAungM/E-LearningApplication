<?php
$servername = "localhost";
$username = "root";
$password = "";

$connection = new mysqli($servername, $username, $password);


if ($connection->connect_error) {
  die("Connection failed: " . $connection->connect_error);
}


$sql = "CREATE DATABASE elearningdb";
if ($connection->query($sql) === TRUE) {
  echo "<p>Database is created successfully.</p>";
} else {
  echo "Error in creating database: " . $connection->error;
}

$connection->close();
