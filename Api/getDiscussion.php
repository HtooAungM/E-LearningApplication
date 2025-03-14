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
    // Fetch all discussions with student details
    $sql = "SELECT d.id, d.student_id, d.content, d.created_at, d.updated_at, d.is_anonymous, 
                   s.first_name, s.last_name
            FROM Discussion d
            JOIN Student s ON d.student_id = s.id
            ORDER BY d.created_at DESC";

    $result = $conn->query($sql);

    $discussions = [];
    while ($row = $result->fetch_assoc()) {
        // Convert is_anonymous from 1/0 to true/false
        if ($row['is_anonymous'] == 1) {
            $row['is_anonymous'] = true;
        } else {
            $row['is_anonymous'] = false;
        }

        // Hide student details if the discussion is anonymous
        if ($row['is_anonymous']) {
            $row['student_id'] = null;
            $row['first_name'] = 'Anonymous';
            $row['last_name'] = '';
        }
        $discussions[] = $row;
    }

    echo json_encode($discussions);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>

