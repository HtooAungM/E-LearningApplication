<?php
include 'config.php';
include 'header.php';

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $deleteSql = "DELETE FROM Quiz WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: getQuiz.php");
    exit();
}

// Get all quizzes
$sql = "SELECT * FROM Quiz";
$result = $conn->query($sql);
?>

<h1>Quiz List</h1>
<a href="#" class="action-button" onclick="showAddModal()">Add New Quiz</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td>
                        <a href="getQuestion.php?quiz_id=<?php echo $row['id']; ?>" class="quiz-title">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </td>
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
    <p>No quizzes found.</p>
<?php endif; ?>

<!-- Add Quiz Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <h2>Add New Quiz</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="Quiz Title" required>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" onclick="hideAddModal()">Cancel</button>
                <button type="submit" class="confirm-btn">Add Quiz</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" id="edit_id" name="update_id">
            <input type="text" id="edit_title" name="title" placeholder="Quiz Title" required>
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
        <form method="POST">
            <input type="hidden" id="delete_id" name="delete_id">
            <p>Are you sure you want to delete this quiz?</p>
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

    function showAddModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function hideAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }
</script>

<?php
$conn->close();
?>