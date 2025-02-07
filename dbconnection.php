<?php

$servername = "localhost";

$username = "root";

$password = "";

$database = "elearningdb";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

