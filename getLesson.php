<?php
// Include the database connection
include 'config.php';

// SQL query to get all lessons with course titles
$sql = "SELECT l.*, c.title as course_title 
        FROM Lesson l
        JOIN Course c ON l.course_id = c.id";
$result = $conn->query($sql);

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $deleteSql = "DELETE FROM Lesson WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: getLesson.php");
    exit();
}

// Handle update request
if (isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $title = $_POST['title'];
    $videoUrl = $_POST['video_url'];
    $lessonScript = $_POST['lesson_script'];

    $updateSql = "UPDATE Lesson SET title = ?, video_url = ?, lesson_script = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssi", $title, $videoUrl, $lessonScript, $id);
    $stmt->execute();
    header("Location: getLesson.php");
    exit();
}

include 'header.php';
?>
            <h1>Lesson List</h1>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Video URL</th>
                            <th>Lesson Script</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['video_url']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['lesson_script'], 0, 50)) . '...'; ?></td>
                                <td class="button-group">
                                    <button class="edit-btn" onclick="showEditModal(<?php 
                                        echo htmlspecialchars(json_encode($row)); 
                                    ?>)">Edit</button>
                                    <button class="delete-btn" onclick="showDeleteModal(<?php 
                                        echo htmlspecialchars($row['id']); 
                                    ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No lessons found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Lesson</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="update_id" id="edit_id">
                <input type="text" name="title" id="edit_title" placeholder="Title">
                <input type="text" name="video_url" id="edit_video_url" placeholder="Video URL">
                <textarea name="lesson_script" id="edit_lesson_script" placeholder="Lesson Script"></textarea>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="hideEditModal()">Cancel</button>
                    <button type="submit" class="confirm-btn">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this lesson?</p>
            <form method="POST">
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="hideDeleteModal()">Cancel</button>
                    <button type="submit" class="confirm-btn">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showEditModal(data) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_video_url').value = data.video_url;
            document.getElementById('edit_lesson_script').value = data.lesson_script;
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
$conn->close();
?>
