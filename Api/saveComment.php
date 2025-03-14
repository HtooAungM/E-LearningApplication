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
    $requiredFields = ['discussion_id', 'student_id', 'comment'];
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

    // Insert new comment
    $insertSql = "INSERT INTO Comment (discussion_id, student_id, comment, is_anonymous) 
                  VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param(
        "iisi", 
        $data['discussion_id'],
        $data['student_id'],
        $data['comment'],
        $isAnonymous
    );

    if ($stmt->execute()) {
        $commentId = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Comment created successfully',
            'data' => [
                'id' => $commentId,
                'discussion_id' => $data['discussion_id'],
                'student_id' => $data['student_id'],
                'comment' => $data['comment'],
                'is_anonymous' => $isAnonymous
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error creating comment: ' . $stmt->error
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