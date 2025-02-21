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
    $requiredFields = ['course_id', 'title', 'video_url', 'lesson_script'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            echo json_encode([
                'status' => 'error',
                'message' => "Missing required field: $field"
            ]);
            exit();
        }
    }

    // Check if lesson title already exists for this course
    $checkSql = "SELECT id FROM Lesson WHERE course_id = ? AND title = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("is", $data['course_id'], $data['title']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'A lesson with this title already exists in this course'
        ]);
        exit();
    }

    // Insert new lesson
    $insertSql = "INSERT INTO Lesson (course_id, title, video_url, lesson_script) 
                  VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param(
        "isss", 
        $data['course_id'],
        $data['title'],
        $data['video_url'],
        $data['lesson_script']
    );

    if ($stmt->execute()) {
        $lessonId = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Lesson created successfully',
            'data' => [
                'id' => $lessonId,
                'title' => $data['title'],
                'course_id' => $data['course_id']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error creating lesson: ' . $stmt->error
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
