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

try {
    // Get the discussion_id from the query parameters
    if (!isset($_GET['discussion_id']) || empty($_GET['discussion_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'discussion_id is required'
        ]);
        exit();
    }

    $discussionId = $_GET['discussion_id'];

    // Fetch comments related to the discussion
    $sql = "SELECT c.id, c.student_id, c.comment, c.created_at, c.is_anonymous, 
                   s.first_name, s.last_name
            FROM Comment c
            JOIN Student s ON c.student_id = s.id
            WHERE c.discussion_id = ?
            ORDER BY c.created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $discussionId);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Hide student details if the comment is anonymous
        if ($row['is_anonymous']) {
            $row['student_id'] = null;
            $row['first_name'] = 'Anonymous';
            $row['last_name'] = '';
        }
        $comments[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $comments
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>