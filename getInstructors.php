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
    $degree = trim($_POST['degree']);

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
        $insertSql = "INSERT INTO User (name, email, password, gender, date_of_birth, user_type, degree) 
                      VALUES (?, ?, ?, ?, ?, 'instructor', ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ssssss", $name, $email, $hashedPassword, $gender, $dob, $degree);

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
    $degree = $_POST['degree'];

    $updateSql = "UPDATE User SET name = ?, email = ?, gender = ?, date_of_birth = ?, degree = ? WHERE id = ? AND user_type = 'instructor'";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssssi", $name, $email, $gender, $dob, $degree, $id);
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
                <th>Degree</th>
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
                    <td><?php echo htmlspecialchars($row['degree']); ?></td>
                    <td class="button-group">
                        <button class="edit-btn" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <button class="delete-btn" onclick="confirmDelete(<?php echo htmlspecialchars($row['id']); ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No instructors found.</p>
<?php endif; ?>

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
            <input type="text" name="degree" placeholder="Degree" required>
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
            <input type="text" name="degree" id="edit_degree" placeholder="Degree" required>
            <input type="date" name="date_of_birth" id="edit_dob" required>
            <div class="modal-buttons">
                <button type="button" class="cancel-btn" onclick="hideEditModal()">Cancel</button>
                <button type="submit" class="confirm-btn">Update</button>
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
        document.getElementById('edit_degree').value = data.degree;
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this instructor?")) {
            document.body.innerHTML += `<form id="deleteForm" method="POST">
                <input type="hidden" name="delete_id" value="${id}">
            </form>`;
            document.getElementById('deleteForm').submit();
        }
    }

    function showAddModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function hideAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    function hideEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

<?php $conn->close(); ?>
