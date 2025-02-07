<?php
// Include the database connection
include 'config.php';  // Ensure this contains correct database connection details

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
    $category = $_POST['category'];

    $updateSql = "UPDATE Course SET title = ?, duration_in_hours = ?, level = ?, price = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssisi", $title, $duration, $level, $price, $category, $id);
    $stmt->execute();
    header("Location: getCourse.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Courses</title>
    <style>
        /* Same modal styles as in getUser.php */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 70%;
            max-width: 500px;
            border-radius: 5px;
        }
        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .modal-content input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
        .modal-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .confirm-btn {
            background-color: #4CAF50;
            color: white;
        }
        .cancel-btn {
            background-color: #f44336;
            color: white;
        }
    </style>
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
            <h1>Course List</h1>
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
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['duration_in_hours']); ?></td>
                                <td><?php echo htmlspecialchars($row['level']); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
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
                <p>No courses found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Course</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="update_id" id="edit_id">
                <input type="text" name="title" id="edit_title" placeholder="Title">
                <input type="text" name="duration_in_hours" id="edit_duration" placeholder="Duration in Hours">
                <input type="text" name="level" id="edit_level" placeholder="Level">
                <input type="number" name="price" id="edit_price" placeholder="Price">
                <input type="text" name="category" id="edit_category" placeholder="Category">
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
            <p>Are you sure you want to delete this course?</p>
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
