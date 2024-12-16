<?php
session_start();
include '../../db.php'; // Include database connection

// Check if module_id is passed in the URL
if (isset($_GET['module_id'])) {
    $module_id = (int)$_GET['module_id'];

    // Fetch the module details (e.g., title)
    $stmt = $mysqli->prepare("SELECT title FROM modules WHERE module_id = ?");
    $stmt->bind_param('i', $module_id);
    $stmt->execute();
    $module_result = $stmt->get_result();
    $module = $module_result->fetch_assoc();

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['content'])) {
        $week = (int)$_POST['week'];
        $file = $_FILES['content'];

        // Save the uploaded file
        if ($file['error'] == 0) {
            $upload_dir = 'uploads/'; // Ensure this directory exists and is writable
            $file_path = $upload_dir . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Insert the uploaded file information into the database
                $stmt = $mysqli->prepare("INSERT INTO module_content (module_id, week, content_path) VALUES (?, ?, ?)");
                $stmt->bind_param('iis', $module_id, $week, $file_path);
                $stmt->execute();
                echo "File uploaded successfully!";
            } else {
                echo "Error uploading file.";
            }
        }
    }

    // Handle file deletion
    if (isset($_GET['delete_id'])) {
        $content_id = (int)$_GET['delete_id'];

        // Fetch the file path to delete the file from the server
        $stmt = $mysqli->prepare("SELECT content_path FROM module_content WHERE id = ?");
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        // Delete the file from the server
        if (file_exists($file['content_path'])) {
            unlink($file['content_path']);
        }

        // Remove the record from the database
        $stmt = $mysqli->prepare("DELETE FROM module_content WHERE id = ?");
        $stmt->bind_param('i', $content_id);
        $stmt->execute();
        echo "File deleted successfully!";
    }

    // Fetch all uploaded content for the selected module
    $stmt = $mysqli->prepare("SELECT id, week, content_path FROM module_content WHERE module_id = ?");
    $stmt->bind_param('i', $module_id);
    $stmt->execute();
    $content_result = $stmt->get_result();

} else {
    die("Module ID not provided.");
}

// Fetch students registered for the module

// Fetch students based on module_id (through diploma_module mapping)
$query = "
    SELECT s.student_number, s.name 
    FROM students s
    JOIN diploma_modules dm ON s.diploma_id = dm.diploma_id
    WHERE dm.module_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $module_id);
$stmt->execute();
$students_result = $stmt->get_result();
$students = $students_result->fetch_all(MYSQLI_ASSOC);

// Handle form submission to save attendance
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $week = $_POST['week'];
//     foreach ($_POST['attendance'] as $student_id => $status) {
//         $attendance_status = ($status == 'on') ? 1 : 0;  // checkbox checked = present

//         // Insert attendance record into the database
//         $insert_query = "
//             INSERT INTO attendance (student_id, module_id, week, attendance_status)
//             VALUES (?, ?, ?, ?)
//             ON DUPLICATE KEY UPDATE attendance_status = ?";
        
//         $insert_stmt = $mysqli->prepare($insert_query);
//         $insert_stmt->bind_param('iisii', $student_id, $module_id, $week, $attendance_status, $attendance_status);
//         $insert_stmt->execute();
//     }
//     echo "Attendance saved successfully!";
// }

// if (isset($_POST['attendance'])) {
//     foreach ($_POST['attendance'] as $student_id => $status) {
//         $attendance_status = ($status == 'on') ? 1 : 0;  // checkbox checked = present

//         // Insert attendance record into the database
//         $insert_query = "
//             INSERT INTO attendance (student_id, module_id, week, attendance_status)
//             VALUES (?, ?, ?, ?)
//             ON DUPLICATE KEY UPDATE attendance_status = ?";
        
//         $insert_stmt = $mysqli->prepare($insert_query);
//         $insert_stmt->bind_param('iisii', $student_id, $module_id, $week, $attendance_status, $attendance_status);
//         $insert_stmt->execute();
//     }
//     echo "Attendance saved successfully!";
// } else {
//     echo "No attendance submitted.";
// }

$student_ids = array_column($students, 'student_number');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    
    $week = $_POST['week'];
    $attendance = $_POST['attendance'] ?? []; // default to empty array if not set

    foreach ($student_ids as $student_id) {
        $attendance_status = isset($attendance[$student_id]) ? 1 : 0; // 1 if checked, 0 if not

        // Insert attendance record into the database
        $insert_query = "
            INSERT INTO attendance (student_id, module_id, week, attendance_status)
            VALUES (?, ?, ?, ?)
            ";
        
        $insert_stmt = $mysqli->prepare($insert_query);
        $insert_stmt->bind_param('iisi', $student_id, $module_id, $week, $attendance_status);
        $insert_stmt->execute();
    }
    echo "Attendance saved successfully!";
}

// Handle form submission to save results
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['results'])) {
    $results = $_POST['results']; // Fetch results from POST data

    foreach ($results as $student_id => $result) {
        if ($result != 'Select Results') { // Skip if no result selected
            // Insert or update the result for each student
            $insert_query = "
                INSERT INTO results (student_id, module_id, result)
                VALUES (?, ?, ?)
                ";
            
            $insert_stmt = $mysqli->prepare($insert_query);
            $insert_stmt->bind_param('iis', $student_id, $module_id, $result);
            
            try {
                $insert_stmt->execute();
            } catch (mysqli_sql_exception $e) {
                echo "Error: " . $e->getMessage();
                exit;
            }
            // $insert_stmt->execute();
        }
    }
    echo "Results saved successfully!";
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
            right: 0;
            background-color: #ccc;
            padding: 10px;
            border-radius: 5px;
        }
        .dropdown a {
            display: block;
            color: black;
            text-decoration: none;
            margin: 5px 0;
        }
        .container {
            padding: 20px;
            margin: 0 10%;
        }
        .module-selection {
            margin-bottom: 20px;
        }
        .upload-form {
            background-color: #e0e0e0;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .upload-form input[type="text"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .upload-form input[type="file"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0 75px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .upload-form button {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .upload-form button:hover {
            background-color: darkred;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 75px;
        }
        .content-table th, .content-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .content-table th {
            background-color: #e0e0e0;
        }
        .delete-button {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: darkred;
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
        .form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

.form-group input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}

th {
    background-color: #e0e0e0;
}

.submit-btn {
    background-color: red;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 75px;
}

.submit-btn:hover {
    background-color: #3e8e41;
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
    <div class="container">
        <div class="module-selection">
            <h2>Your Selected Module: <?php echo htmlspecialchars($module['title']); ?></h2>
        </div>
        <div class="upload-form">
            <form method="POST" enctype="multipart/form-data">
                <label for="week">WEEK</label><br>
                <input type="number" id="week" name="week" required><br>
                <label for="content">ADD CONTENT</label><br>
                <input type="file" id="content" name="content" required><br>
                <button type="submit">UPLOAD CONTENT</button>
            </form>
        </div>

        <div class="uploaded-content">
            <h2>Your Uploaded Content:</h2>
            <table class="content-table">
                <tr>
                    <th>Week</th>
                    <th>Uploaded Materials</th>
                    <th>Remove Content</th>
                </tr>
                <?php while ($row = $content_result->fetch_assoc()): ?>
                    <tr>
                        <td>Week <?php echo htmlspecialchars($row['week']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['content_path']); ?>" target="_blank"><?php echo basename($row['content_path']); ?></a></td>
                        <td><a href="?module_id=<?php echo $module_id; ?>&delete_id=<?php echo $row['id']; ?>" class="delete-button">DELETE</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <h2>Your Selected Module: <?php echo htmlspecialchars($module['title']); ?></h2>
    <form method="POST">
        <div class="form-group">
            <label for="week">ENTER WEEK</label>
            <input type="text" id="week" name="week" placeholder="Eg:- 4" required>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Absent/Present</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                    <td><input type="checkbox" name="attendance[<?php echo $student['student_number']; ?>]"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button class="submit-btn" type="submit">SUBMIT ATTENDANCE</button>
    </form>

        <!-- Form to submit results -->
        <h2>Enter Results for Students</h2>
        <form action="" method="POST">
            <table border="1">
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Enter Results</th>
                </tr>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                    <td>
                        <select name="results[<?php echo $student['student_number']; ?>]">
                            <option>Select Results</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="Failed">Failed</option>
                            <option value="Not Released">Not Released</option>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" class="submit-btn">Submit Results</button>
        </form>

    </div>
    <div class="footer">
        <p>All rights are reserved. Designed and developed by Dhanushka Sisil</p>
    </div>
</body>
</html>