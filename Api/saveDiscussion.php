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
    $requiredFields = ['student_id', 'content'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            echo json_encode([
                'status' => 'error',
                'message' => "Missing required field: $field"
            ]);
            exit();
        }
    }

    // Set default values for optional fields
    $isAnonymous = isset($data['is_anonymous']) ? $data['is_anonymous'] : false;

    // Insert new discussion post
    $insertSql = "INSERT INTO Discussion (student_id, content, is_anonymous) 
                  VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param(
        "isi", 
        $data['student_id'],
        $data['content'],
        $isAnonymous
    );

    if ($stmt->execute()) {
        $discussionId = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Discussion created successfully',
            'data' => [
                'id' => $discussionId,
                'student_id' => $data['student_id'],
                'content' => $data['content'],
                'is_anonymous' => $isAnonymous
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error creating discussion: ' . $stmt->error
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