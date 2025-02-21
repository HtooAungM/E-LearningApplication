<?php
header('Content-Type: application/json');
include("../config.php");

// Check if user is logged in via session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Authentication required'
    ]);
    exit();
}

// Check if user is instructor
if ($_SESSION['user_type'] === 'instructor') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied'
    ]);
    exit();
}

try {
    // Handle different HTTP methods
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Get all instructors or specific instructor
            if (isset($_GET['id'])) {
                $sql = "SELECT id, name, email, field_of_expertise, gender, date_of_birth FROM Instructor WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_GET['id']);
            } else {
                $sql = "SELECT id, name, email, field_of_expertise, gender, date_of_birth FROM Instructor";
                $stmt = $conn->prepare($sql);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $instructors = [];

            while ($row = $result->fetch_assoc()) {
                $instructors[] = $row;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $instructors
            ]);
            break;

        case 'POST':
            // Add new instructor
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            $requiredFields = ['name', 'email', 'password', 'field_of_expertise', 'gender', 'date_of_birth'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => "Missing required field: $field"
                    ]);
                    exit();
                }
            }

            // Check if email already exists
            $checkSql = "SELECT id FROM Instructor WHERE email = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Email already exists'
                ]);
                exit();
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert new instructor
            $sql = "INSERT INTO Instructor (name, email, password, field_of_expertise, gender, date_of_birth) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", 
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['field_of_expertise'],
                $data['gender'],
                $data['date_of_birth']
            );

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Instructor added successfully',
                    'data' => ['id' => $conn->insert_id]
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error adding instructor'
                ]);
            }
            break;

        case 'PUT':
            // Update instructor
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Instructor ID is required'
                ]);
                exit();
            }

            // Build update query dynamically based on provided fields
            $updateFields = [];
            $types = "";
            $values = [];

            if (isset($data['name'])) {
                $updateFields[] = "name = ?";
                $types .= "s";
                $values[] = $data['name'];
            }
            if (isset($data['email'])) {
                $updateFields[] = "email = ?";
                $types .= "s";
                $values[] = $data['email'];
            }
            if (isset($data['field_of_expertise'])) {
                $updateFields[] = "field_of_expertise = ?";
                $types .= "s";
                $values[] = $data['field_of_expertise'];
            }
            if (isset($data['gender'])) {
                $updateFields[] = "gender = ?";
                $types .= "s";
                $values[] = $data['gender'];
            }
            if (isset($data['date_of_birth'])) {
                $updateFields[] = "date_of_birth = ?";
                $types .= "s";
                $values[] = $data['date_of_birth'];
            }

            if (empty($updateFields)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No fields to update'
                ]);
                exit();
            }

            $sql = "UPDATE Instructor SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $types .= "i";
            $values[] = $data['id'];

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Instructor updated successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error updating instructor'
                ]);
            }
            break;

        case 'DELETE':
            // Delete instructor
            if (!isset($_GET['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Instructor ID is required'
                ]);
                exit();
            }

            $sql = "DELETE FROM Instructor WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_GET['id']);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Instructor deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error deleting instructor'
                ]);
            }
            break;

        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Method not allowed'
            ]);
            break;
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 