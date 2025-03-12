<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn2gether - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Login</h1>
            <?php
            session_start();
            
            if (isset($_GET['registered'])) {
                echo "<p class='success'>Registration successful! Please login.</p>";
            }
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                include 'config.php';
                
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);
                
                // Check User table only
                $sql = "SELECT * FROM User WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_type'] = $user['user_type'];
                        $_SESSION['name'] = $user['name'];
                        
                        // Redirect based on user type
                        if ($user['user_type'] === 'admin') {
                            header("Location: AdminHomePage.php");
                        } else if ($user['user_type'] === 'instructor') {
                            header("Location: AdminHomePage.php");
                        }
                        exit();
                    } else {
                        echo "<p class='error'>Invalid password!</p>";
                    }
                } else {
                    echo "<p class='error'>Email not found!</p>";
                }
                
                $stmt->close();
                $conn->close();
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="auth-form">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="auth-button">Login</button>
                </div>
                <div class="auth-links">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
