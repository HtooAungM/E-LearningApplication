<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No data provided'
    ]);
    exit();
}

// Validate required fields
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password are required'
    ]);
    exit();
}

// Check student credentials
$sql = "SELECT * FROM Student WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $data['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    if (password_verify($data['password'], $student['password'])) {
        // Remove password from response
        unset($student['password']);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => $student
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid password'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Student not found'
    ]);
}

$stmt->close();
$conn->close();
?> 