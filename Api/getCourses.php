<?php
include("../config.php");

$sql = "SELECT * FROM Course";

$result = $conn->query($sql);

$json = array();

if ($result->num_rows > 0) {

  while ($row = $result->fetch_assoc()) {
    $json[] = $row;
  }
  echo json_encode($json);
} else {
  $response = array("result" => "No of rows is zero");
  array_push($json, $response);
  echo json_encode($json);
}

$conn->close(); ?>