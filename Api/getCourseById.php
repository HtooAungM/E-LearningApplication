<?php
ob_start();
session_start();
require_once '../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Check if student ID is provided
if (!isset($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Course ID is required'
    ]);
    exit();
}

$course_id = intval($_GET['id']);

// Prepare and execute query
$sql = "SELECT * FROM Course WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {    
    $course = $result->fetch_assoc();
    
    // Format the response
    $response = [
        'status' => 'success',
        'data' => [
            'id' => $course['id'],
            'title' => $course['title'],
            'image' => $course['image'],
            'duration_in_hours' => $course['duration_in_hours'],
            'level' => $course['level'],
            'price' => $course['price'],
            'category' => $course['category'],
            'instructorId' => $course['instructor_id'],

        ]
    ];

    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Course not found'
    ]);
}

$stmt->close();
$conn->close();
?> 