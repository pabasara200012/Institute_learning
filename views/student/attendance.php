<?php
include '../../db.php';
session_start();

$student_id = $_SESSION['student_id'];

$query = "SELECT * FROM attendance WHERE student_id = ? ORDER BY week ASC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();

if ($stmt->errno) {
    echo "Error: " . $stmt->error;
    exit;
}

$students_result = $stmt->get_result();
$attendance = $students_result->fetch_all(MYSQLI_ASSOC);

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
            font-size: 20px;
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
            background-color: #ccc;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dropdown a {
            display: block;
            color: black;
            text-decoration: none;
            margin: 5px 0;
        }
        .content {
            padding: 20px;
            margin-left: 10%;
            margin-right: 10%;
        }
        .content h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .select-module {
            margin-bottom: 20px;
            text-align: right;
        }
        .select-module label {
            font-weight: bold;
        }
        .select-module select {
            padding: 5px;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        <h2>Your Attendance:</h2>
        <table>
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Module Title</th>
                    <th>Module Code</th>
                    <th>Abbsend/Present</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendance)): ?>
                <?php foreach ($attendance as $att): 
                    $module_id = $att['module_id'];
                    $module_query = "SELECT * FROM modules WHERE module_id =?";
                    $stmt = $mysqli->prepare($module_query);
                    $stmt->bind_param('i', $module_id);
                    $stmt->execute();
                    $module_result = $stmt->get_result();
                    $module = $module_result->fetch_assoc();
                    ?>
                <tr>
                    <td> Week <?php echo htmlspecialchars($att['week']); ?></td>
                    <td><?php echo htmlspecialchars($module['title']); ?></td>
                    <td><?php echo htmlspecialchars($module['code']); ?></td>
                    <td><?php 
                    if ($att['attendance_status'] == '1') {
                        echo '<i class="fas fa-check"></i>';
                    } else {
                        echo '<i class="fas fa-times"></i>';
                    }
                    // echo htmlspecialchars($att['attendance_status']); 
                    ?></td>

                    <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="4">No results found.</td>
            </tr>
            <?php endif; ?>
                <!-- </tr>
                <tr>
                    <td>Week 02</td>
                    <td>CS 3101</td>
                    <td>Dhanushka Sisil</td>
                    <td>No</td>
                </tr>
                <tr>
                    <td>Week 03</td>
                    <td>CS 3101</td>
                    <td>Dhanushka Sisil</td>
                    <td>Yes</td>
                </tr>
                <tr>
                    <td>Week 04</td>
                    <td>CS 3101</td>
                    <td>Dhanushka Sisil</td>
                    <td>No</td>
                </tr> -->
            </tbody>
        </table>
    </div>
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>