<?php 
include '../../db.php';
session_start();
$student_id = $_SESSION['student_id'];

$query = "SELECT dm.title, dm.code, s.result, c.coordinator_id
FROM results s 
JOIN modules dm ON 
s.module_id = dm.module_id 
JOIN coordinator_modules c ON 
    dm.module_id = c.module_id
WHERE s.student_id =?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();

if ($stmt->errno) {
    echo "Error: " . $stmt->error;
    exit;
}

$students_result = $stmt->get_result();
$results = $students_result->fetch_all(MYSQLI_ASSOC);

// $stmt->close();
// $mysqli->close();
?>

<html>
<head>
    <title>Diploma Manage</title>
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
            background-color: #f2f2f2;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
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
        <h2>Your Exam Results:</h2>
        <table>
            <tr>
                <th>Module</th>
                <th>Module Code</th>
                <th>Lecturer</th>
                <th>Results</th>
            </tr>
            <?php if (!empty($results)): ?>
            <?php foreach ($results as $result): ?>
            <tr>
                <td><?php echo htmlspecialchars($result['title']); ?></td>
                <td><?php echo htmlspecialchars($result['code']); ?></td>
                <?php
                    $coordinator_id = $result['coordinator_id'];
                    
                    $query = "SELECT name FROM coordinators WHERE coordinator_id =?";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param('i', $coordinator_id);
                    $stmt->execute();
                    $coordinator_name = $stmt->get_result()->fetch_assoc();
                    // $stmt->close();
                    // $mysqli->close();
                    echo '<td>'. htmlspecialchars($coordinator_name['name']). '</td>';
                ?>
                <!-- <td><?php echo htmlspecialchars($result['code']); ?></td> -->
                <td><?php echo htmlspecialchars($result['result']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="4">No results found.</td>
            </tr>
            <?php endif; ?>
            <!-- <tr>
                <td>Intro to DS</td>
                <td>CS 3101</td>
                <td>Dhanushka Sisil</td>
                <td>Not Released</td>
            </tr>
            <tr>
                <td>Intro to DS</td>
                <td>CS 3101</td>
                <td>Dhanushka Sisil</td>
                <td>B</td>
            </tr>
            <tr>
                <td>Intro to DS</td>
                <td>CS 3101</td>
                <td>Dhanushka Sisil</td>
                <td>C</td>
            </tr> -->
        </table>
    </div>
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>
