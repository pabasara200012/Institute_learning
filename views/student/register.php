<?php
session_start();
include '../../db.php';

function fetchDiplomas() {
    global $mysqli;

    // Query to fetch all diplomas
    $query = "SELECT diploma_id, title FROM diplomas";
    $result = $mysqli->query($query);

    $diplomas = [];
    while ($row = $result->fetch_assoc()) {
        $diplomas[] = $row;
    }
    return $diplomas;
}

// Call the function to fetch diplomas
$diploma_options = fetchDiplomas();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $mysqli->real_escape_string($_POST['name']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $student_id = $mysqli->real_escape_string($_POST['student_id']);
    $address = $mysqli->real_escape_string($_POST['address']);
    $diploma_id = (int)$_POST['diploma']; // Cast diploma_id to integer
    $status = 0; // Set status to 0

    // Insert data into the students table
    $query = "INSERT INTO students (student_number, name, password, address, diploma_id, status) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssssii', $student_id, $name, $password, $address, $diploma_id, $status);

    if ($stmt->execute()) {
        echo "Registration successful!";
        // Redirect to a success page or login page
        header("Location: ../../index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
        .header .menu {
            position: relative;
        }
        .header .menu i {
            font-size: 20px;
            cursor: pointer;
        }
        .header .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ccc;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .header .dropdown a {
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            display: block;
        }
        .header .dropdown a:hover {
            background-color: #ddd;
        }
        .header .menu:hover .dropdown {
            display: block;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px);
        }
        .login-box {
            background-color: #d3d3d3;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            padding-right: 20px;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .login-box label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .login-box input, .login-box select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-box button {
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .login-box button:hover {
            background-color: darkred;
        }
        .login-box .or {
            text-align: center;
            margin: 10px 0;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Learning management system</h1>
        <div class="menu">
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

    <div class="container">
        <div class="login-box" style="padding-right: 20px;">
            <h2>REGISTER NEW STUDENTS</h2>
            <form action="register.php" method="POST">
    <label for="name">ENTER YOUR NAME</label>
    <input type="text" id="name" name="name" placeholder="Eg:- Dhanushka Sisil" required>
    
    <label for="password">ENTER YOUR PASSWORD</label>
    <input type="password" id="password" name="password" required>
    
    <label for="student-id">ENTER YOUR STUDENT ID</label>
    <input type="text" id="student-id" name="student_id" placeholder="Eg:- 16078" required>
    
    <label for="address">ENTER YOUR ADDRESS</label>
    <input type="text" id="address" name="address" placeholder="Eg:- 23/1, Melder Place, Nugegoda" required>
    
    <label for="diploma">SELECT YOUR DIPLOMA</label>
    <select id="diploma" name="diploma" required>
        <?php
        // Populate diploma options from the database
        foreach ($diploma_options as $diploma) {
            echo "<option value='" . htmlspecialchars($diploma['diploma_id']) . "'>" . htmlspecialchars($diploma['title']) . "</option>";
        }
        ?>
    </select>
    
    <button type="submit" class="btn">REGISTER NOW</button>
</form>

            <div class="or">OR</div>
            
            <!-- Link to Login Page -->
            <a href="login.php" class="btn">LOGIN PAGE</a>
        </div>
    </div>

    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>