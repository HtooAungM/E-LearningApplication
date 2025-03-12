<?php
ob_start();
session_start();
require_once '../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Check if instructor ID is provided
if (!isset($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Instructor ID is required'
    ]);
    exit();
}

$instructor_id = intval($_GET['id']);

// Prepare and execute query
$sql = "SELECT * FROM User WHERE id = ? AND user_type = 'instructor'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {    
    $instructor = $result->fetch_assoc();
    
    // Format the response
    $response = [
        'status' => 'success',
        'data' => [
            'id' => $instructor['id'],
            'name' => $instructor['name'],
            'email' => $instructor['email'],
            'gender' => $instructor['gender'],
            'date_of_birth' => $instructor['date_of_birth'],
            'user_type' => $instructor['user_type'],
            'degree' => $instructor['degree']
        ]
    ];

    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Instructor not found'
    ]);
}

$stmt->close();
$conn->close();
?>
