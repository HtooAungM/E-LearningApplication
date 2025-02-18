<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn2gether - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .dropdown-content {
            display: none;
            background-color: #444;
            padding-left: 20px;
        }

        .dropdown.active .dropdown-content {
            display: block;
        }

        .dropdown-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
        .sidebar ul li:nth-child(3) {
            padding: 10px 0 10px 0;
        }

        .dropdown-btn::after {
            content: '▼';
            font-size: 0.8em;
            margin-left: 5px;
            transition: transform 0.3s ease;
        }

        .dropdown.active .dropdown-btn::after {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <ul>
                <li><a href="adminHomePage.php">Home</a></li>
                <li><a href="getUser.php">Student List</a></li>
                <li class="dropdown">
                    <a class="dropdown-btn" onclick="toggleDropdown(this)">Courses</a>
                    <div class="dropdown-content">
                        <ul>
                            <li><a href="getCourse.php">View Courses</a></li>
                            <li><a href="getLesson.php">View Lessons</a></li>
                        </ul>
                    </div>
                </li>
                <li><a href="getQuiz.php">Quiz</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <div class="content">

        <script>
            function toggleDropdown(element) {
                const dropdown = element.parentElement;
                dropdown.classList.toggle('active');
            }

            // Keep dropdown open if on a related page
            document.addEventListener('DOMContentLoaded', function() {
                const currentPath = window.location.pathname;
                if (currentPath.includes('Course') || currentPath.includes('Lesson')) {
                    document.querySelector('.dropdown').classList.add('active');
                }
            });
        </script>
