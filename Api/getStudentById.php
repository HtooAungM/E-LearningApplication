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
        'message' => 'Student ID is required'
    ]);
    exit();
}

$student_id = intval($_GET['id']);

// Prepare and execute query
$sql = "SELECT * FROM Student WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {    
    $student = $result->fetch_assoc();
    
    // Format the response
    $response = [
        'status' => 'success',
        'data' => [
            'id' => $student['id'],
            'first_name' => $student['first_name'],
            'last_name' => $student['last_name'],
            'email' => $student['email'],
            'gender' => $student['gender'],
            'date_of_birth' => $student['date_of_birth']
        ]
    ];

    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Student not found'
    ]);
}

$stmt->close();
$conn->close();
?> 