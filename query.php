<?php
include 'config.php';

// Insert sample Students
$studentSQL = "INSERT INTO Student (first_name, last_name, email, password, gender, date_of_birth) VALUES
    ('John', 'Doe', 'john@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Male', '1995-05-15'),
    ('Jane', 'Smith', 'jane@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Female', '1998-08-22'),
    ('Mike', 'Johnson', 'mike@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Male', '1997-03-10')";

if ($conn->query($studentSQL) === TRUE) {
    echo "Sample students added successfully<br>";
} else {
    echo "Error adding students: " . $conn->error . "<br>";
}

// Insert sample Users (including instructors)
$userSQL = "INSERT INTO User (name, email, password, gender, date_of_birth, user_type) VALUES
    ('Prof. Sarah Wilson', 'sarah@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Female', '1985-04-12', 'admin'),
    ('Dr. Robert Brown', 'robert@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Male', '1980-09-28', 'instructor'),
    ('Prof. Emily Davis', 'emily@example.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Female', '1982-11-15', 'instructor')";

if ($conn->query($userSQL) === TRUE) {
    echo "Sample users added successfully<br>";
} else {
    echo "Error adding users: " . $conn->error . "<br>";
}

// Insert sample Courses
$courseSQL = "INSERT INTO Course (user_id, title, image, duration_in_hours, level, price, category) VALUES
    (1, 'Introduction to Web Development', 'web_dev.jpg', '20', 'Beginner', 49, 'Programming'),
    (2, 'Advanced Python Programming', 'python.jpg', '30', 'Advanced', 79, 'Programming'),
    (3, 'Digital Marketing Basics', 'marketing.jpg', '15', 'Beginner', 39, 'Marketing')";

if ($conn->query($courseSQL) === TRUE) {
    echo "Sample courses added successfully<br>";
} else {
    echo "Error adding courses: " . $conn->error . "<br>";
}

// Insert sample Lessons
$lessonSQL = "INSERT INTO Lesson (course_id, title, video_url, lesson_script) VALUES
    (1, 'HTML Basics', 'https://example.com/video1.mp4', 'In this lesson, we will learn HTML basics...'),
    (1, 'CSS Fundamentals', 'https://example.com/video2.mp4', 'Learn the fundamentals of CSS...'),
    (2, 'Python Functions', 'https://example.com/video3.mp4', 'Understanding Python functions...')";

if ($conn->query($lessonSQL) === TRUE) {
    echo "Sample lessons added successfully<br>";
} else {
    echo "Error adding lessons: " . $conn->error . "<br>";
}

// Insert sample Payments
$paymentSQL = "INSERT INTO Payment (payment_method, payment_date) VALUES
    ('Credit Card', '2024-03-15 10:30:00'),
    ('PayPal', '2024-03-16 14:20:00'),
    ('Debit Card', '2024-03-17 09:45:00')";

if ($conn->query($paymentSQL) === TRUE) {
    echo "Sample payments added successfully<br>";
} else {
    echo "Error adding payments: " . $conn->error . "<br>";
}

// Insert sample Enrollments
$enrollmentSQL = "INSERT INTO Enrollment (student_id, course_id, payment_id, date_of_enrollment, progress_percentage, is_enrolled) VALUES
    (1, 1, 1, '2024-03-15', 30, 1),
    (2, 2, 2, '2024-03-16', 45, 1),
    (3, 3, 3, '2024-03-17', 15, 1)";

if ($conn->query($enrollmentSQL) === TRUE) {
    echo "Sample enrollments added successfully<br>";
} else {
    echo "Error adding enrollments: " . $conn->error . "<br>";
}

// Insert sample Quizzes
$quizSQL = "INSERT INTO Quiz (title) VALUES
    ('Web Development Basics Quiz'),
    ('Python Programming Assessment'),
    ('Digital Marketing Test')";

if ($conn->query($quizSQL) === TRUE) {
    echo "Sample quizzes added successfully<br>";
} else {
    echo "Error adding quizzes: " . $conn->error . "<br>";
}

// Insert sample Questions
$questionSQL = "INSERT INTO Question (quiz_id, question_text, option1, option2, option3, option4, correct_answer) VALUES
    (1, 'What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Transfer Markup Language', 'None of the above', 'Hyper Text Markup Language'),
    (1, 'Which tag is used for creating a hyperlink?', '<link>', '<a>', '<href>', '<url>', '<a>'),
    (2, 'What is the output of print(2**3)?', '6', '8', '5', '9', '8')";

if ($conn->query($questionSQL) === TRUE) {
    echo "Sample questions added successfully<br>";
} else {
    echo "Error adding questions: " . $conn->error . "<br>";
}

// Insert sample Answers
$answerSQL = "INSERT INTO Answer (student_id, question_id, answer_text, is_correct) VALUES
    (1, 1, 'Hyper Text Markup Language', 1),
    (2, 2, '<a>', 1),
    (3, 3, '8', 1)";

if ($conn->query($answerSQL) === TRUE) {
    echo "Sample answers added successfully<br>";
} else {
    echo "Error adding answers: " . $conn->error . "<br>";
}

$conn->close();
?>
