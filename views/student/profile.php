<?php
// Start the session
session_start();

// Include your database connection
include '../../db.php';

// Check if session is set
if (!isset($_SESSION['student_id'])) {
    echo "You are not logged in!";
    exit;
}

// Get the student_id from session
$student_id = $_SESSION['student_id'];

// Query to get the student details and diploma program
$studentQuery = "
    SELECT students.name AS student_name, diplomas.code AS diploma_code, diplomas.title AS diploma_title
    FROM students
    JOIN diplomas ON students.diploma_id = diplomas.diploma_id
    WHERE students.student_number = ?
";

if ($stmt = $mysqli->prepare($studentQuery)) {
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_name, $diploma_code, $diploma_title);
    $stmt->fetch();
    $stmt->close();
}

// Query to get the student's modules
$moduleQuery = "
    SELECT modules.module_id, modules.title AS module_name, modules.code AS module_code
    FROM diploma_modules
    JOIN modules ON diploma_modules.module_id = modules.module_id
    WHERE diploma_modules.diploma_id = (
        SELECT diploma_id FROM students WHERE student_number = ?
    )
";

$modules = [];
if ($stmt = $mysqli->prepare($moduleQuery)) {
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
    
    $stmt->close();
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
            background-color: #f5f5f5;
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
            font-size: 18px;
        }
        .admin-functions {
            position: relative;
        }
        .admin-functions:hover .dropdown {
            display: block;
        }
        .dropdown {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: #666;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        .dropdown a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 5px 0;
        }
        .content {
            padding: 20px;
            margin-left: 10%;
            margin-right: 10%;
        }
        .content h2 {
            font-size: 24px;
        }
        .content p {
            font-size: 18px;
        }
        .modules {
            margin-top: 20px;
        }
        .modules table {
            width: 100%;
            border-collapse: collapse;
        }
        .modules th, .modules td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .modules th {
            background-color: #e0e0e0;
        }
        .visit-button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
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
    <h2>Welcome <?php echo htmlspecialchars($student_name); ?>!</h2>
<p>Your Diploma Program: <?php echo htmlspecialchars($diploma_code); ?> - <?php echo htmlspecialchars($diploma_title); ?></p>

<div class="modules">
    <h3>Your Modules:</h3>
    <table>
        <tr>
            <th>Module Name</th>
            <th>Module Code</th>
            <th>Visit Module</th>
        </tr>
        <?php foreach ($modules as $module) { ?>
        <tr>
            <td><?php echo htmlspecialchars($module['module_name']); ?></td>
            <td><?php echo htmlspecialchars($module['module_code']); ?></td>
            <td>
                <form action="modules.php" method="get">
                    <input type="hidden" name="module_id" value="<?php echo $module['module_id']; ?>">
                    <button type="submit" class="visit-button">VISIT</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
    </div>
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>