<?php
// Include your database connection file
include("dbconnection.php");

// SQL query to fetch all courses
$sql = "SELECT * FROM course";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="brand">
                <img src="assets/logo.png" alt="Logo" class="logo">
                <span class="brand-name">eLearning</span>
            </div>
            <nav class="nav-links">
                <a href="AdminHomePage.php">Main Page</a>
                <a href="courses.php" class="active">Courses</a>
                <a href="lessons.php">Lessons</a>
                <a href="enrollment.php">Enrollment</a>
            </nav>
        </aside>
        <main class="content">
            <h1>Courses Dashboard</h1>
            <table class="course-table">
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Instructor ID</th>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["id"]. "</td>
                                    <td>" . $row["instructor_id"]. "</td>
                                    <td>" . $row["title"]. "</td>
                                    <td>" . $row["duration_in_hours"]. "</td>
                                    <td>" . $row["price"]. "</td>
                                    <td><a href='editCourse.php?id=" . $row["id"] . "'>Edit</a> | <a href='deleteCourse.php?id=" . $row["id"] . "'>Delete</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No courses found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>

<?php
$connection->close();
?>
