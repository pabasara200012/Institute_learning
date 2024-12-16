<?php
// Start the session
session_start();

// Include the database connection
include '../../db.php';

// Check if the module_id is set in the GET request
if (!isset($_GET['module_id'])) {
    echo "No module selected!";
    exit;
}

// Get the module_id from the GET request
$module_id = $_GET['module_id'];

// Query to get the module details
$moduleQuery = "
    SELECT title, code
    FROM modules
    WHERE module_id = ?
";

$module_title = '';
$module_code = '';

if ($stmt = $mysqli->prepare($moduleQuery)) {
    $stmt->bind_param("i", $module_id);
    $stmt->execute();
    $stmt->bind_result($module_title, $module_code);
    $stmt->fetch();
    $stmt->close();
}

// Query to get the module content and coordinator's name
$contentQuery = "
    SELECT mc.content_path, mc.id AS content_id, mc.week, c.name AS coordinator_name
    FROM module_content mc
    JOIN coordinator_modules cm ON cm.module_id = mc.module_id
    JOIN coordinators c ON cm.coordinator_id = c.coordinator_id
    WHERE mc.module_id = ?
    ORDER BY mc.week
";

$contents = [];
if ($stmt = $mysqli->prepare($contentQuery)) {
    $stmt->bind_param("i", $module_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $contents[] = $row;
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
        .header .title {
            font-size: 18px;
        }
        .header .admin-functions {
            font-size: 14px;
            text-align: right;
            position: relative;
        }
        .header .admin-functions:hover .dropdown-content {
            display: block;
        }
        .header .admin-functions .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #666;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .header .admin-functions .dropdown-content div {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .header .admin-functions .dropdown-content div:hover {
            background-color: #777;
        }
        .content {
            padding: 20px;
            margin-left: 10%;
            margin-right: 10%;
        }
        .content h2 {
            font-size: 20px;
        }
        .content h3 {
            font-size: 16px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #e0e0e0;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
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
        <div class="title">Learning management system</div>
        <div class="admin-functions">
            Admin Functions
            <div class="dropdown-content">
                <div>Dashboard</div>
                <div>Exams | Results</div>
                <div>Logout</div>
            </div>
        </div>
    </div>
    <div class="content">
    <h2>Your Selected Module: <?php echo htmlspecialchars($module_code); ?> - <?php echo htmlspecialchars($module_title); ?></h2>
<h3>Content for Refer:</h3>
<table border="1">
    <tr>
        <th>Week</th>
        <th>Uploaded Materials</th>
        <th>Uploaded By</th>
    </tr>
    <?php foreach ($contents as $content) { ?>
    <tr>
        <td><?php echo htmlspecialchars('Week ' . $content['week']); ?></td>
        <!-- <td><?php echo htmlspecialchars($content['content_path']); ?></td> -->
        <td><a href="../coordinator/<?php echo htmlspecialchars($content['content_path']); ?>" target="_blank"><?php echo basename($content['content_path']); ?></a></td>
        <td><?php echo htmlspecialchars($content['coordinator_name']); ?></td>
    </tr>
    <?php } ?>
</table>
    </div>
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>