<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['quiz_id'])) {
    header("Location: getQuiz.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];

// Get quiz title
$quizSql = "SELECT title FROM Quiz WHERE id = ?";
$stmt = $conn->prepare($quizSql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quizResult = $stmt->get_result();
$quiz = $quizResult->fetch_assoc();

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $deleteSql = "DELETE FROM Question WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: getQuestion.php?quiz_id=" . $quiz_id);
    exit();
}

// Handle update request
if (isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $question_text = $_POST['question_text'];
    $option1 = $_POST['option1'];
    $option2 = $_POST['option2'];
    $option3 = $_POST['option3'];
    $option4 = $_POST['option4'];
    $correct_answer = $_POST['correct_answer'];

    $updateSql = "UPDATE Question SET question_text = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, correct_answer = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssssssi", $question_text, $option1, $option2, $option3, $option4, $correct_answer, $id);
    $stmt->execute();
    header("Location: getQuestion.php?quiz_id=" . $quiz_id);
    exit();
}

// Get all questions for this quiz
$sql = "SELECT * FROM Question WHERE quiz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<a href="getQuiz.php" class="back-arrow">Back to Quizzes</a>

<h1>Questions for Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h1>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Options</th>
                <th>Correct Answer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                    <td>
                        1. <?php echo htmlspecialchars($row['option1']); ?><br>
                        2. <?php echo htmlspecialchars($row['option2']); ?><br>
                        3. <?php echo htmlspecialchars($row['option3']); ?><br>
                        4. <?php echo htmlspecialchars($row['option4']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['correct_answer']); ?></td>
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
    <p>No questions found for this quiz.</p>
<?php endif; ?>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" id="edit_id" name="update_id">
            <textarea name="question_text" id="edit_question_text" placeholder="Question Text" required></textarea>
            <input type="text" id="edit_option1" name="option1" placeholder="Option 1" required>
            <input type="text" id="edit_option2" name="option2" placeholder="Option 2" required>
            <input type="text" id="edit_option3" name="option3" placeholder="Option 3" required>
            <input type="text" id="edit_option4" name="option4" placeholder="Option 4" required>
            <input type="text" id="edit_correct_answer" name="correct_answer" placeholder="Correct Answer" required>
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
            <p>Are you sure you want to delete this question?</p>
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
        document.getElementById('edit_question_text').value = data.question_text;
        document.getElementById('edit_option1').value = data.option1;
        document.getElementById('edit_option2').value = data.option2;
        document.getElementById('edit_option3').value = data.option3;
        document.getElementById('edit_option4').value = data.option4;
        document.getElementById('edit_correct_answer').value = data.correct_answer;
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

<?php
$conn->close();
?>
