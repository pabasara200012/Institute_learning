<?php
session_start();
require_once '../../db.php'; // Your database connection

// Handle form submission and login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to dashboard or another page
            header('Location: dashboard.php');
            exit;
        } else {
            // Invalid password
            $error = "Invalid password. Please try again.";
        }
    } else {
        // Invalid username
        $error = "Invalid username. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Learning Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
                body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .header {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header .user-menu {
            position: relative;
            display: inline-block;
        }
        .header .user-menu .fa-user-circle {
            font-size: 24px;
            cursor: pointer;
        }
        .header .user-menu .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .header .user-menu .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .header .user-menu .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .header .user-menu:hover .dropdown-content {
            display: block;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px);
            padding: 20px;
        }
        .login-box {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-box h2 {
            margin: 0 0 20px;
            font-size: 18px;
            color: #333;
        }
        .login-box label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #333;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-box a {
            display: block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
            font-size: 12px;
            text-align: right;
            margin-top: -10px;
        }
        .login-box a:hover {
            text-decoration: underline;
        }
        .login-box .btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            background-color: #d9534f;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .heading-a{
            text-decoration: none;
            color: white;
        }
        @media (max-width: 600px) {
            .header h1 {
                font-size: 16px;
            }
            .header .user-menu .fa-user-circle {
                font-size: 20px;
            }
            .login-box {
                padding: 15px;
            }
            .login-box h2 {
                font-size: 16px;
            }
            .login-box .btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <!--Navigation Bar -->
    <div class="header">
        <h1>Learning management system</h1>
        <div class="user-menu">
            <i class="fas fa-user-circle"></i>
            <div class="dropdown-content">
                <a href="#">Student Login</a>
                <a href="#">Student Register</a>
                <a href="#">Coordinator Login</a>
                <a href="#">Admin Login</a>
                <a href="#">Logout</a>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-box">
            <h2>LOG INTO YOUR ACCOUNT</h2>

            <?php if (isset($error)): ?>
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <label for="username">ENTER YOUR USER NAME</label>
                <input type="text" id="username" name="username" placeholder="Eg:- DhanushkaSisil" required>
                
                <label for="password">ENTER YOUR PASSWORD</label>
                <input type="password" id="password" name="password" required>

                <a href="#">FORGET YOUR PASSWORD?</a>
                
                <button type="submit" class="btn">LOGIN NOW</button>

                <a href="views/student/register.php" class="btn" style="display: inline-block; text-align: center; text-decoration: none; color: white;">REGISTER NOW</a>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>
