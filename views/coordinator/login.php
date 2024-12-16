<?php
// login.php
session_start();
include '../../db.php'; // Assuming db_connect.php handles your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lecturer_id = $_POST['username'];
    $password = $_POST['password'];

    if ($mysqli) {
        // Query to check user credentials based on lecturer_id
        $stmt = $mysqli->prepare("SELECT * FROM coordinators WHERE lecturer_id = ?");
        $stmt->bind_param("s", $lecturer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verifying the password using password_verify
            if ($password === $user['account_password']) {
                // Password is correct, set session variables
                $_SESSION['lecturer_id'] = $user['lecturer_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['coordinator_id'] = $user['coordinator_id'];
                header("Location: dashboard.php"); // Redirect to dashboard if login is successful
                exit();
            } else {
                // Password is incorrect
                $error_message = "Invalid Username or Password!";
                $error_message = "Invalid Password!";
            }
        } else {
            // User not found in the database
            $error_message = "Invalid Username or Password!";
            $error_message = "Invalid Username or Password! Else Main";
        }
    } else {
        echo "Database connection failed!";
    }
}
?>

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
            color: #fff;
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
        .header .user-menu .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .header .user-menu .dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .header .user-menu .dropdown a:hover {
            background-color: #f1f1f1;
        }
        .header .user-menu:hover .dropdown {
            display: block;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px);
        }
        .login-box {
            background-color: #e0e0e0;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }
        .login-box h2 {
            margin: 0 0 20px;
            font-size: 18px;
        }
        .login-box label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-left: 10px;
        }
        .login-box button {
            width: calc(100% - 20px);
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: #fff;
            background-color: #d32f2f;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
        }
        .login-box button:hover {
            background-color: #b71c1c;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Learning management system</h1>
        <div class="user-menu">
            <i class="fas fa-user-circle"></i>
            <div class="dropdown">
                <a href="#">Student Login</a>
                <a href="#">Student Register</a>
                <a href="#">Coordinator Login</a>
                <a href="#">Admin Login</a>
                <a href="#">Logout</a>
            </div>
        </div>
    </div>
    <div class="login-container">
        <div class="login-box">
            <h2>LOG INTO YOUR ACCOUNT</h2>
            <?php if (isset($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
            <form method="POST" action="login.php">
                <label for="username">ENTER YOUR USER NAME</label>
                <input type="text" id="username" name="username" placeholder="Eg:- DhanushkaSisil" required>
                
                <label for="password">ENTER YOUR PASSWORD</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">LOGIN NOW</button>
            </form>
        </div>
    </div>
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>