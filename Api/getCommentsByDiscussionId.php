<?php
ob_start();
session_start();
require_once '../config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Check if discussion ID is provided
if (!isset($_GET['discussion_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Discussion ID is required'
    ]);
    exit();
}

$discussion_id = intval($_GET['discussion_id']);

// Prepare and execute query to get comments for the specific discussion
$sql = "SELECT c.id, c.comment, c.created_at, c.is_anonymous, c.discussion_id, c.student_id
        FROM Comment c
        WHERE c.discussion_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $discussion_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    // Convert 1 or 0 into true or false for is_anonymous
    $row['is_anonymous'] = (bool)$row['is_anonymous'];  // Cast integer to boolean (true/false)

    // Add comment to array
    $comments[] = $row;
}

// Format the response
$response = [
    'status' => 'success',
    'data' => $comments
];

echo json_encode($response);

$stmt->close();
$conn->close();
?>
