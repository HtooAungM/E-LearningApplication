<?php
include("../config.php");

// Check if course_id is provided
if (!isset($_GET['course_id'])) {
    echo json_encode(array("error" => "Course ID is required"));
    exit();
}

$course_id = $_GET['course_id'];

// Join with Course table to get course title and filter by course_id
$sql = "SELECT l.*, c.title as course_title 
        FROM Lesson l
        JOIN Course c ON l.course_id = c.id
        WHERE l.course_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$json = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $json[] = $row;
    }
    echo json_encode($json);
} else {
    $response = array("result" => "No lessons found for this course");
    array_push($json, $response);
    echo json_encode($json);
}

$conn->close();
?>
