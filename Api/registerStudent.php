<?php
ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

// Get data from either POST or JSON
$data = $_SERVER['CONTENT_TYPE'] === 'application/json' 
    ? json_decode(file_get_contents('php://input'), true)
    : $_POST;

if (!$data) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No data provided'
    ]);
    exit();
}

// Validate required fields
$required_fields = ['first_name', 'last_name', 'email', 'password', 'gender', 'date_of_birth'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            'status' => 'error',
            'message' => "Missing required field: $field"
        ]);
        exit();
    }
}

// Check if email already exists
$checkEmail = "SELECT id FROM Student WHERE email = ?";
$stmt = $conn->prepare($checkEmail);
$stmt->bind_param("s", $data['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email already exists'
    ]);
    exit();
}

// Hash password
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// Insert new student
$sql = "INSERT INTO Student (first_name, last_name, email, password, gender, date_of_birth) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", 
    $data['first_name'],
    $data['last_name'],
    $data['email'],
    $hashedPassword,
    $data['gender'],
    $data['date_of_birth']
);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Student registered successfully',
        'student_id' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Registration failed: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?> 