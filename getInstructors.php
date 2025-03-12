<?php
require_once 'auth_check.php';
requireAdmin();

// Include the database connection
include 'config.php';

// Handle add instructor request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_id']) && !isset($_POST['delete_id'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $expertise = trim($_POST['field_of_expertise']);
    $gender = trim($_POST['gender']);
    $dob = trim($_POST['date_of_birth']);

    // Check if email already exists
    $checkSql = "SELECT id FROM User WHERE email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('A user with this email already exists!');</script>";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Add new instructor
        $insertSql = "INSERT INTO User (name, email, password, gender, date_of_birth, user_type) 
                      VALUES (?, ?, ?, ?, ?, 'instructor')";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $gender, $dob);
        
        if ($stmt->execute()) {
            header("Location: getInstructors.php");
            exit();
        } else {
            echo "<script>alert('Error adding instructor: " . $stmt->error . "');</script>";
        }
    }
}

// Handle delete request
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $deleteSql = "DELETE FROM User WHERE id = ? AND user_type = 'instructor'";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: getInstructors.php");
    exit();
}

// Handle update request
if (isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];

    $updateSql = "UPDATE User SET name = ?, email = ?, gender = ?, date_of_birth = ? WHERE id = ? AND user_type = 'instructor'";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssssi", $name, $email, $gender, $dob, $id);
    $stmt->execute();
    header("Location: getInstructors.php");
    exit();
}

// SQL query to get all instructors
$sql = "SELECT * FROM User WHERE user_type = 'instructor'";
$result = $conn->query($sql);

include 'header.php';
?>
            <h1>Instructor List</h1>
            <a href="#" class="action-button" onclick="showAddModal()">Add New Instructor</a>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
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
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
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
                <p>No instructors found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Instructor Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h2>Add New Instructor</h2>
            <form method="POST" onsubmit="return validateForm()">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="date" name="date_of_birth" required>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="hideAddModal()">Cancel</button>
                    <button type="submit" class="confirm-btn">Add Instructor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Instructor</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="update_id" id="edit_id">
                <input type="text" name="name" id="edit_name" placeholder="Full Name" required>
                <input type="email" name="email" id="edit_email" placeholder="Email" required>
                <select name="gender" id="edit_gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="date" name="date_of_birth" id="edit_dob" required>
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
            <p>Are you sure you want to delete this instructor?</p>
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
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_gender').value = data.gender;
            document.getElementById('edit_dob').value = data.date_of_birth;
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

        function validateForm() {
            const password = document.querySelector('input[name="password"]');
            if (password && password.value.length < 6) {
                alert('Password must be at least 6 characters long');
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
$conn->close();
?>
