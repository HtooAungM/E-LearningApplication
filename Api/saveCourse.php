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
    $requiredFields = ['instructor_id', 'title', 'duration_in_hours', 'level', 'price', 'category'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            echo json_encode([
                'status' => 'error',
                'message' => "Missing required field: $field"
            ]);
            exit();
        }
    }

    // Check if course title already exists
    $checkSql = "SELECT id FROM Course WHERE title = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $data['title']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'A course with this title already exists'
        ]);
        exit();
    }

    // Handle image upload if provided
    $image = "default.jpg"; // Default image
    if (isset($data['image']) && !empty($data['image'])) {
        // Handle base64 image
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['image']));
        $imageName = uniqid() . '.jpg';
        $imagePath = "../uploads/" . $imageName;
        
        if (file_put_contents($imagePath, $imageData)) {
            $image = $imageName;
        }
    }

    // Insert new course
    $insertSql = "INSERT INTO Course (instructor_id, title, image, duration_in_hours, level, price, category) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param(
        "issssis", 
        $data['instructor_id'],
        $data['title'],
        $image,
        $data['duration_in_hours'],
        $data['level'],
        $data['price'],
        $data['category']
    );

    if ($stmt->execute()) {
        $courseId = $conn->insert_id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Course created successfully',
            'data' => [
                'id' => $courseId,
                'title' => $data['title'],
                'image' => $image
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error creating course: ' . $stmt->error
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
