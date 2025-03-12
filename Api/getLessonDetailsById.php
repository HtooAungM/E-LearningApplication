<?php
// Clean any existing output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Start fresh output buffer
ob_start();

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Check database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    // Check if lesson ID is provided
    if (!isset($_GET['lessonId'])) {
        throw new Exception('Lesson ID is required');
    }

    // Validate and sanitize lesson_id
    $lesson_id = filter_var($_GET['lessonId'], FILTER_VALIDATE_INT);
    if ($lesson_id === false || $lesson_id <= 0) {
        throw new Exception('Invalid Lesson ID');
    }

    // Prepare and execute query to fetch lesson details
    $sql = "SELECT l.*, c.title as course_title 
            FROM Lesson l 
            JOIN Course c ON l.course_id = c.id 
            WHERE l.id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare SQL query: ' . $conn->error);
    }

    $stmt->bind_param("i", $lesson_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Lesson not found');
    }

    $lesson = $result->fetch_assoc();
    
    // Format the lesson data
    $lessonData = [
        'id' => intval($lesson['id']),
        'course_id' => intval($lesson['course_id']),
        'title' => htmlspecialchars($lesson['title']),
        'video_url' => $lesson['video_url'],
        'lesson_script' => $lesson['lesson_script'],
        'course' => [
            'title' => htmlspecialchars($lesson['course_title'])
        ]
    ];

    // Clear any previous output
    if (ob_get_length()) ob_clean();

    // Send the JSON response
    echo json_encode([
        'status' => 'success',
        'message' => 'Lesson details retrieved successfully',
        'data' => $lessonData
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Clear any previous output
    if (ob_get_length()) ob_clean();
    
    http_response_code($e->getMessage() === 'Lesson not found' ? 404 : 400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}

// Flush and end output buffer
ob_end_flush();
?>
