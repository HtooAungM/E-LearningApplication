<?php
header('Content-Type: application/json');
include("../config.php");

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only GET method is allowed'
    ]);
    exit();
}

// Check if discussion_id is provided
if (!isset($_GET['discussion_id']) || empty($_GET['discussion_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'discussion_id is required'
    ]);
    exit();
}

$discussionId = $_GET['discussion_id'];

try {
    // Prepare the SQL query
    $sql = "SELECT d.id, d.student_id, d.content, d.created_at, d.updated_at, d.is_anonymous,
                   s.first_name, s.last_name
            FROM Discussion d
            JOIN Student s ON d.student_id = s.id
            WHERE d.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $discussionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $discussion = $result->fetch_assoc();

        // Convert `is_anonymous` from integer to boolean
        $discussion['is_anonymous'] = ($discussion['is_anonymous'] == 1);  // ✅ Ensures true/false

        // Hide student details if the discussion is anonymous
        if ($discussion['is_anonymous']) {
            $discussion['student_id'] = null;
            $discussion['first_name'] = 'Anonymous';
            $discussion['last_name'] = '';
        }

        echo json_encode([
            'status' => 'success',
            'data' => $discussion
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Discussion not found'
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
