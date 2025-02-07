<?php
// Include the database connection
include 'config.php';

// SQL query to get all students
$sql = "SELECT * FROM Student";
$result = $conn->query($sql);

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $deleteSql = "DELETE FROM Student WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: getUser.php");
    exit();
}

// Handle update request
if (isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];

    $updateSql = "UPDATE Student SET first_name = ?, last_name = ?, email = ?, gender = ?, date_of_birth = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssssi", $firstName, $lastName, $email, $gender, $dob, $id);
    $stmt->execute();
    header("Location: getUser.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Users</title>
    <style>
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
            <h1>Student List</h1>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
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
                <p>No students found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Student</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="update_id" id="edit_id">
                <input type="text" name="first_name" id="edit_first_name" placeholder="First Name">
                <input type="text" name="last_name" id="edit_last_name" placeholder="Last Name">
                <input type="email" name="email" id="edit_email" placeholder="Email">
                <input type="text" name="gender" id="edit_gender" placeholder="Gender">
                <input type="text" name="date_of_birth" id="edit_date_of_birth" placeholder="Date of Birth">
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
            <p>Are you sure you want to delete this student?</p>
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
            document.getElementById('edit_first_name').value = data.first_name;
            document.getElementById('edit_last_name').value = data.last_name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_gender').value = data.gender;
            document.getElementById('edit_date_of_birth').value = data.date_of_birth;
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
