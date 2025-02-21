<?php
header('Content-Type: application/json');
include("../config.php");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST method is allowed'
    ]);
    exit();
}

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $requiredFields = ['student_id', 'course_id', 'date_of_enrollment'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            echo json_encode([
                'status' => 'error',
                'message' => "Missing required field: $field"
            ]);
            exit();
        }
    }

    // Check if enrollment already exists
    $checkSql = "SELECT id FROM Enrollment WHERE student_id = ? AND course_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $data['student_id'], $data['course_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Student is already enrolled in this course'
        ]);
        exit();
    }

    // Set default values
    $progress_percentage = 0;
    $is_enrolled = true;

    // Insert new enrollment
    $insertSql = "INSERT INTO Enrollment (student_id, course_id, date_of_enrollment, progress_percentage, is_enrolled) 
                  VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param(
        "iisib", 
        $data['student_id'],
        $data['course_id'],
        $data['date_of_enrollment'],
        $progress_percentage,
        $is_enrolled
    );

    if ($stmt->execute()) {
        $enrollmentId = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Enrollment created successfully',
            'data' => [
                'id' => $enrollmentId,
                'student_id' => $data['student_id'],
                'course_id' => $data['course_id']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error creating enrollment: ' . $stmt->error
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
