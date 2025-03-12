<?php
include 'config.php';
include 'header.php';

// Get course ID from URL
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($course_id === 0) {
    echo "<div class='error'>Invalid course ID</div>";
    exit();
}

// Fetch course details
$sql = "SELECT c.*, u.name as instructor_name FROM Course c JOIN User u ON c.instructor_id = u.id WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$courseResult = $stmt->get_result();

if ($courseResult->num_rows === 0) {
    echo "<div class='error'>Course not found.</div>";
    exit();
}

$course = $courseResult->fetch_assoc();

// Get related lessons
$lessonSql = "SELECT * FROM Lesson WHERE course_id = ?";
$stmt = $conn->prepare($lessonSql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$lessonResult = $stmt->get_result();

// Handle file upload and lesson creation
if (isset($_POST['upload_lesson'])) {
    $title = $_POST['title'];
    $lesson_script = $_POST['lesson_script'];

    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
        $video_name = basename($_FILES['video_file']['name']);
        $target_dir = "uploads/videos/";
        $target_file = $target_dir . $video_name;

        $allowed_types = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];
        $file_type = mime_content_type($_FILES['video_file']['tmp_name']);

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
                $sql = "INSERT INTO Lesson (course_id, title, video_url, lesson_script) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isss", $course_id, $title, $video_name, $lesson_script);
                
                if ($stmt->execute()) {
                    echo "<script>alert('Lesson added successfully!'); window.location.href='CourseDetail.php?course_id=$course_id';</script>";
                } else {
                    echo "<div class='error'>Error saving lesson.</div>";
                }
            } else {
                echo "<div class='error'>Failed to upload video.</div>";
            }
        } else {
            echo "<div class='error'>Invalid file type. Only MP4, AVI, MOV, and MKV are allowed.</div>";
        }
    } else {
        echo "<div class='error'>Please upload a valid video file.</div>";
    }
}
?>

<a href="getCourse.php" class="back-arrow">Back to Courses</a>

<div class="course-detail">
    <div class="course-header">
        <div class="course-image">
            <img src="uploads/images/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image">
        </div>
        <div class="course-info">
            <h1><?php echo htmlspecialchars($course['title']); ?></h1>
            <div class="course-meta">
                <span class="category-badge"><?php echo htmlspecialchars($course['category']); ?></span>
                <span class="level-badge"><?php echo htmlspecialchars($course['level']); ?></span>
            </div>
            <div class="instructor-info">
                <span class="instructor-label">Instructor:</span>
                <span class="instructor-name"><?php echo htmlspecialchars($course['instructor_name']); ?></span>
            </div>
        </div>
    </div>

    <div class="course-lessons">
        <h2>Course Lessons</h2>
        <button class="action-button" onclick="showAddLessonModal()">Add New Lesson</button>
        
        <?php if ($lessonResult->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Lesson Title</th>
                        <th>Video</th>
                        <th>Lesson Script</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($lesson = $lessonResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                            <td>
                                <video width="200" controls>
                                    <source src="uploads/videos/<?php echo htmlspecialchars($lesson['video_url']); ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                            <td><?php echo htmlspecialchars(substr($lesson['lesson_script'], 0, 100)) . '...'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No lessons found for this course.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Add Lesson Modal -->
<div id="addLessonModal" class="modal">
    <div class="modal-content">
        <h2>Add New Lesson</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <input type="text" name="title" placeholder="Lesson Title" required>
            <input type="file" name="video_file" accept="video/*" required>
            <textarea name="lesson_script" placeholder="Lesson Script" required></textarea>
            <button type="submit" name="upload_lesson" class="confirm-btn">Add Lesson</button>
        </form>
    </div>
</div>

<script>
    function showAddLessonModal() {
        document.getElementById('addLessonModal').style.display = 'block';
    }
    function hideAddLessonModal() {
        document.getElementById('addLessonModal').style.display = 'none';
    }
</script>