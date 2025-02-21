<?php
header('Content-Type: application/json');
include("../config.php");

try {
    // Get query parameters
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

    // Base query with joins to get student and course information
    $sql = "SELECT e.*, 
            s.first_name, s.last_name, s.email,
            c.title as course_title, c.level, c.category
            FROM Enrollment e
            JOIN Student s ON e.student_id = s.id
            JOIN Course c ON e.course_id = c.id";
    
    $params = [];
    $types = "";
    $where = [];

    // Add filters if provided
    if ($student_id) {
        $where[] = "e.student_id = ?";
        $params[] = $student_id;
        $types .= "i";
    }

    if ($course_id) {
        $where[] = "e.course_id = ?";
        $params[] = $course_id;
        $types .= "i";
    }

    // Add WHERE clause if filters exist
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    // Add ordering
    $sql .= " ORDER BY e.date_of_enrollment DESC";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $enrollments = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format the enrollment data
            $enrollment = [
                'id' => $row['id'],
                'student' => [
                    'id' => $row['student_id'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email']
                ],
                'course' => [
                    'id' => $row['course_id'],
                    'title' => $row['course_title'],
                    'level' => $row['level'],
                    'category' => $row['category']
                ],
                'date_of_enrollment' => $row['date_of_enrollment'],
                'progress_percentage' => $row['progress_percentage'],
                'is_enrolled' => (bool)$row['is_enrolled']
            ];
            $enrollments[] = $enrollment;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $enrollments
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'No enrollments found',
            'data' => []
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
