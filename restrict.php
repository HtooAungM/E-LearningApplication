<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .restrict-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .restrict-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .restrict-icon {
            font-size: 80px;
            color: #f44336;
            margin-bottom: 20px;
        }

        .restrict-title {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .restrict-message {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="restrict-container">
        <div class="restrict-box">
            <div class="restrict-icon">🚫</div>
            <h1 class="restrict-title">Access Restricted</h1>
            <p class="restrict-message">Sorry, you don't have permission to access this page.</p>
            <a href="AdminHomePage.php" class="back-button">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 