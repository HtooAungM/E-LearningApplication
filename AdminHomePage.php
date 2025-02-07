<?php
include 'config.php';

// Get total counts
$studentCount = $conn->query("SELECT COUNT(*) as count FROM Student")->fetch_assoc()['count'];
$courseCount = $conn->query("SELECT COUNT(*) as count FROM Course")->fetch_assoc()['count'];
$enrollmentCount = $conn->query("SELECT COUNT(*) as count FROM Enrollment")->fetch_assoc()['count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn2gether - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <ul>
                <li><a href="adminHomePage.php">Home</a></li>
                <li><a href="getUser.php">Users</a></li>
                <li><a href="getCourse.php">Courses</a></li>
                <li><a href="getLesson.php">Lessons</a></li>
            </ul>
        </nav>
        <div class="content">
            <h1 class="welcome-message">Welcome to Learn2gether Admin Dashboard</h1>
            
            <div class="stats-container">
                <div class="stat-card users">
                    <div class="stat-icon"></div>
                    <h2>Total Users</h2>
                    <div class="stat-number"><?php echo $studentCount; ?></div>
                    <p>Registered students</p>
                </div>

                <div class="stat-card courses">
                    <div class="stat-icon"></div>
                    <h2>Total Courses</h2>
                    <div class="stat-number"><?php echo $courseCount; ?></div>
                    <p>Available courses</p>
                </div>

                <div class="stat-card enrollments">
                    <div class="stat-icon"></div>
                    <h2>Total Enrollments</h2>
                    <div class="stat-number"><?php echo $enrollmentCount; ?></div>
                    <p>Course enrollments</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>