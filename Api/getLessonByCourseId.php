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

    // Check if course ID is provided
    if (!isset($_GET['courseId'])) {
        throw new Exception('Course ID is required');
    }

    // Validate and sanitize course_id
    $course_id = filter_var($_GET['courseId'], FILTER_VALIDATE_INT);
    if ($course_id === false || $course_id <= 0) {
        throw new Exception('Invalid Course ID');
    }

    // Prepare and execute query to fetch lessons by course_id
    $sql = "SELECT id, course_id, title, video_url, lesson_script FROM Lesson WHERE course_id = ? ORDER BY id ASC";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare SQL query: ' . $conn->error);
    }

    $stmt->bind_param("i", $course_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $lessons = [];

    while ($lesson = $result->fetch_assoc()) {
        $lessons[] = [
            'id' => intval($lesson['id']),
            'course_id' => intval($lesson['course_id']),
            'title' => htmlspecialchars($lesson['title']),
            'video_url' => $lesson['video_url'],
            'lesson_script' => $lesson['lesson_script']
        ];
    }

    // Clear any previous output
    if (ob_get_length()) ob_clean();

    // Send the JSON response
    echo json_encode([
        'status' => 'success',
        'message' => $result->num_rows > 0 ? 'Lessons retrieved successfully' : 'No lessons found for this course',
        'data' => $lessons
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Clear any previous output
    if (ob_get_length()) ob_clean();
    
    http_response_code(400);
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
