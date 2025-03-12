<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn2gether - Admin Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Admin Registration</h1>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                include 'config.php';
                
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);
                
                // Validate empty fields
                if (empty($name) || empty($email) || empty($password)) {
                    echo "<p class='error'>All fields are required!</p>";
                } else {
                    // Check if email already exists
                    $checkEmail = "SELECT id FROM User WHERE email = ?";
                    $stmt = $conn->prepare($checkEmail);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        echo "<p class='error'>Email already exists!</p>";
                    } else {
                        // Hash password
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insert new user with type 'admin'
                        $sql = "INSERT INTO User (name, email, password, gender, date_of_birth, user_type) VALUES (?, ?, ?, '', '', 'admin')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $name, $email, $hashedPassword);
                        if ($stmt->execute()) {
                            // Use PHP's header() function to redirect
                            if (!headers_sent()) {
                                header("Location: login.php?registered=true");
                                exit();
                            } else {
                                echo "<script>window.location.href='login.php?registered=true';</script>";
                                echo '<noscript>Please click <a href="login.php?registered=true">here</a> to continue.</noscript>';
                                exit();
                            }
                        } else {
                            echo "<p class='error'>Registration failed. Please try again.</p>";
                        }
                    }
                    $stmt->close();
                }
                $conn->close();
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="auth-form">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="auth-button">Register</button>
                </div>
                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
