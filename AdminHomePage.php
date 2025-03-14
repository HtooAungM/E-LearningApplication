<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'config.php';

// Get total counts
$studentCount = $conn->query("SELECT COUNT(*) as count FROM Student")->fetch_assoc()['count'];
$courseCount = $conn->query("SELECT COUNT(*) as count FROM Course")->fetch_assoc()['count'];
$enrollmentCount = $conn->query("SELECT COUNT(*) as count FROM Enrollment")->fetch_assoc()['count'];

// Check if user is instructor to hide certain elements
$isInstructor = ($_SESSION['user_type'] === 'instructor');

include 'header.php';
?>

<!-- Add this right after the opening of content div -->
<div class="content-main">
    <?php if ($isInstructor): ?>
    <style>
        .instructor-hidden {
            display: none !important;
        }
    </style>
    <?php endif; ?>
    
    <h1 class="welcome-message">Welcome to Learn2gether Admin Dashboard</h1>
    
    <div class="stats-container">
        <div class="stat-card users">
            <div class="stat-icon"></div>
            <h2>Total Students</h2>
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
</body>
</html>
<?php $conn->close(); ?>