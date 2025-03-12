<?php
// Include the database connection
include 'config.php';

// Fetch instructors from the database (from the 'User' table)
$instructorsSql = "SELECT id, name FROM User WHERE user_type = 'Instructor'";  // Filtering for instructors only
$instructorsResult = $conn->query($instructorsSql);

// Handle add course request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_id']) && !isset($_POST['delete_id'])) {
    $title = trim($_POST['title']);
    $duration = trim($_POST['duration_in_hours']);
    $level = trim($_POST['level']);
    $price = trim($_POST['price']);
    $category = $_POST['category'];  // Get the selected category
    $user_id = $_POST['user_id'];  // Get the selected instructor's user_id

    // Handle image upload
    $image = "default.jpg"; // Default image if no image is uploaded

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/images/";
        $imageFileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $imageFileName;

        // Check if file is an image (optional, can be adjusted based on needs)
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Move the uploaded file to the uploads/images folder
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $image = $imageFileName;  // Store the file name in the database
            } else {
                echo "<script>alert('Sorry, there was an error uploading the image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image file format. Only JPG, JPEG, PNG, and GIF are allowed.');</script>";
        }
    }

    // Check if course already exists
    $checkSql = "SELECT id FROM Course WHERE title = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('A course with this title already exists!');</script>";
    } else {
        // Add new course with selected instructor and category
        $insertSql = "INSERT INTO Course (instructor_id, title, image, duration_in_hours, level, price, category) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);

        // Adjusting the bind_param to match the correct types
        $stmt->bind_param("issdsss", $user_id, $title, $image, $duration, $level, $price, $category);

        // Now execute
        if ($stmt->execute()) {
            header("Location: getCourse.php");
            exit();
        } else {
            echo "<script>alert('Error adding course: " . $stmt->error . "');</script>";
        }
    }
}

// SQL query to get all courses
$sql = "SELECT * FROM Course";
$result = $conn->query($sql);

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $deleteSql = "DELETE FROM Course WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: getCourse.php");
    exit();
}

// Handle update request
if (isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $title = $_POST['title'];
    $duration = $_POST['duration_in_hours'];
    $level = $_POST['level'];
    $price = $_POST['price'];
    $category = $_POST['category'];  // New category selection

    $updateSql = "UPDATE Course SET title = ?, duration_in_hours = ?, level = ?, price = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssisi", $title, $duration, $level, $price, $category, $id);
    $stmt->execute();
    header("Location: getCourse.php");
    exit();
}

include 'header.php';
?>

<h1>Course List</h1>
<a href="#" class="action-button" onclick="showAddModal()">Add New Course</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Duration (Hours)</th>
                <th>Level</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td>
                        <a href="CourseDetail.php?course_id=<?php echo $row['id']; ?>" class="course-link">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row['duration_in_hours']); ?></td>
                    <td><?php echo htmlspecialchars($row['level']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td class="button-group">
                        <button class="edit-btn" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <button class="delete-btn" onclick="showDeleteModal(<?php echo htmlspecialchars($row['id']); ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No courses found.</p>
<?php endif; ?>

<!-- Add Course Modal -->
<div id="addCourseModal" class="modal">
    <div class="modal-content">
        <h2>Add New Course</h2>
        <form id="addCourseForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="text" id="title" name="title" placeholder="Course Title" required>
            <input type="text" id="duration" name="duration_in_hours" placeholder="Duration (Hours)" required>
            <select name="level" id="level" required>
                <option value="">Select Level</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
            <input type="number" id="price" name="price" placeholder="Price" required>

            <!-- Category Dropdown (Static List) -->
            <select name="category" id="category" required>
                <option value="">Select Category</option>
                <option value="Software Development">Software Development</option>
                <option value="Data Science & Analytics">Data Science & Analytics</option>
                <option value="Cloud Computing">Cloud Computing</option>
                <option value="Cybersecurity">Cybersecurity</option>
                <option value="Networking">Networking</option>
                <option value="Database Management">Database Management</option>
                <option value="DevOps & Automation">DevOps & Automation</option>
                <option value="UI/UX Design">UI/UX Design</option>
            </select>

            <!-- Instructor Dropdown -->
            <select name="user_id" id="user_id" required>
                <option value="">Select Instructor</option>
                <?php while ($row = $instructorsResult->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <input type="file" name="image" id="image" accept="image/*" required>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" onclick="hideAddModal()">Cancel</button>
                <button type="submit" class="confirm-btn">Add Course</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showEditModal(data) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_title').value = data.title;
        document.getElementById('edit_duration').value = data.duration_in_hours;
        document.getElementById('edit_level').value = data.level;
        document.getElementById('edit_price').value = data.price;
        document.getElementById('edit_category').value = data.category;
    }

    function hideEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function showDeleteModal(id) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('delete_id').value = id;
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Show/Hide Modal
    function showAddModal() {
        document.getElementById('addCourseModal').style.display = 'block';
    }

    function hideAddModal() {
        document.getElementById('addCourseModal').style.display = 'none';
        document.getElementById('addCourseForm').reset();
    }

    // Validate Form
    function validateForm() {
        const title = document.getElementById('title').value.trim();
        const duration = document.getElementById('duration').value.trim();
        const level = document.getElementById('level').value;
        const price = document.getElementById('price').value.trim();
        const category = document.getElementById('category').value;
        const user_id = document.getElementById('user_id').value;

        if (!title || !duration || !level || !price || !category || !user_id) {
            alert('Please fill in all fields');
            return false;
        }

        return true;
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
</script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>