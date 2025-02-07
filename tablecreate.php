<?php
// Include the database connection
include 'config.php';

// SQL query to create the "Student" table
$studentTable = "CREATE TABLE IF NOT EXISTS Student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    gender VARCHAR(100) NOT NULL,
    date_of_birth VARCHAR(100) NOT NULL
)";

// Execute the query
if ($conn->query($studentTable) === TRUE) {
    echo "Table 'Student' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}


$instructorTable = "CREATE TABLE IF NOT EXISTS Instructor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    field_of_expertise VARCHAR(100) NOT NULL,
    gender VARCHAR(100) NOT NULL,
    date_of_birth VARCHAR(100) NOT NULL
)";

// Execute the query
if ($conn->query($instructorTable) === TRUE) {
    echo "Table 'Instructor' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$courseTable = "CREATE TABLE IF NOT EXISTS Course (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    image TEXT NOT NULL,
    duration_in_hours VARCHAR(100) NOT NULL,
    level VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    FOREIGN KEY (instructor_id) REFERENCES Instructor(id)
)";

// Execute the query
if ($conn->query($courseTable) === TRUE) {
    echo "Table 'Course' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$lessonTable = "CREATE TABLE IF NOT EXISTS Lesson (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    video_url TEXT NOT NULL,
    lesson_script TEXT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES Course(id)

)";

// Execute the query
if ($conn->query($lessonTable) === TRUE) {
    echo "Table 'Lesson' created successfully.";   
} else {
    echo "Error creating table: " . $conn->error;
}

$enrollmentTable = "CREATE TABLE IF NOT EXISTS Enrollment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    date_of_enrollment VARCHAR(100) NOT NULL,
    progress_percentage INT NOT NULL,
    is_enrolled BOOLEAN NOT NULL,
    FOREIGN KEY (student_id) REFERENCES Student(id),
    FOREIGN KEY (course_id) REFERENCES Course(id)


)";

// Execute the query
if ($conn->query($enrollmentTable) === TRUE) {
    echo "Table 'Enrollment' created successfully.";   
} else {
    echo "Error creating table: " . $conn->error;
}
// Close the connection
$conn->close();
?>


