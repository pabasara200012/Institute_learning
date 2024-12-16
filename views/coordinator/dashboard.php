<?php
// dashboard.php
session_start();
include '../../db.php'; // Assuming db_connect.php handles your database connection

// If not logged in, redirect to login page
if (!isset($_SESSION['lecturer_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the assigned modules for the logged-in coordinator
$lecturer_id = $_SESSION['lecturer_id'];
$coordinator_id = $_SESSION['coordinator_id'];
$query = "SELECT m.module_id, m.title, m.credit_value 
          FROM coordinator_modules cm
          JOIN modules m ON cm.module_id = m.module_id
          WHERE cm.coordinator_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();
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
            background-color: #f4f4f4;
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
            font-size: 24px;
        }
        .admin-functions {
            position: relative;
            display: inline-block;
        }
        .admin-functions span {
            cursor: pointer;
        }
        .dropdown {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: #ccc;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        .dropdown a {
            display: block;
            color: black;
            text-decoration: none;
            margin: 5px 0;
        }
        .admin-functions:hover .dropdown {
            display: block;
        }
        .content {
            padding: 20px;
            margin-left: 10%;
            margin-right: 10%;
        }
        .content h2 {
            font-size: 24px;
        }
        .content h3 {
            font-size: 20px;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .manage-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Learning management system</h1>
        <div class="admin-functions">
            <span>Admin Functions</span>
            <div class="dropdown">
                <a href="#">Dashboard</a>
                <a href="#">Exams</a>
                <a href="#">Results</a>
                <a href="#">Logout</a>
            </div>
        </div>
    </div>

    <div class="content">
        <h2>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <h3>Your Assigned Modules</h3>
        <table>
    <tr>
        <th>Module Name</th>
        <th>Credit Value</th>
        <th>Manage</th> <!-- New column for Manage button -->
    </tr>
    <?php
    // Loop through the modules and display them
    while ($row = $result->fetch_assoc()) {
        // Escape the module name and credit value for security
        $module_name = htmlspecialchars($row['title']);
        $credit_value = htmlspecialchars($row['credit_value']);
        $module_id = $row['module_id']; // Assuming module_id is part of the result set

        // Display table row with Manage button
        echo "<tr>
                <td>{$module_name}</td>
                <td>{$credit_value}</td>
                <td><a href='lecture_materials.php?module_id={$module_id}' class='manage-button'>Manage</a></td>
              </tr>";
    }
    ?>
</table>
    </div>

    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>