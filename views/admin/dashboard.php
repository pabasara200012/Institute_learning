<?php
require_once 'functions.php'; 
?>

<html>
<head>
    <title>Learning Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            padding: 20px;
            text-align: left;
            font-size: 18px;
        }
        .admin-functions {
            position: relative;
            display: inline-block;
            float: right;
            margin-top: -40px;
            margin-right: 20px;
        }
        .admin-functions button {
            background-color: #333;
            color: white;
            padding: 8px;
            font-size: 18px;
            border: none;
            cursor: pointer;
        }
        .admin-functions ul {
            display: none;
            position: absolute;
            background-color: #333;
            color: white;
            list-style-type: none;
            padding: 0;
            margin: 0;
            min-width: 160px;
            z-index: 1;
        }
        .heading-a{
            text-decoration: none;
            color: white;
        }
        .admin-functions ul li {
            padding: 10px;
            text-align: left;
        }
        .admin-functions ul li:hover {
            background-color: #575757;
        }
        .admin-functions:hover ul {
            display: block;
        }
        .content {
            padding: 20px;
        }
        .section-title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .section-title.registered-students {
            margin-top: 40px; /* Added top space */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #ccc;
        }
        .action-buttons a {
            color: red;
            text-decoration: none;
            margin-right: 10px;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .select-diploma {
            float: right;
            margin-top: -40px;
            margin-right: 20px;
        }
        .select-diploma select {
            padding: 5px;
        }
        @media (max-width: 768px) {
            .header, .footer {
                text-align: center;
            }
            .admin-functions {
                float: none;
                margin: 0;
                text-align: center;
            }
            .select-diploma {
                float: none;
                margin: 20px 0;
                text-align: center;
            }
            table, th, td {
                display: block;
                width: 100%;
            }
            th, td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            th::before, td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }
            th, td {
                padding: 10px 10px 10px 50%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        Learning management system
    </div>
    <div class="admin-functions">
        <button>Admin Functions</button>
        <ul>
        <li><a href="dashboard.php" class="heading-a">Manage Students</a></li>
            <li><a href="coordinators.php" class="heading-a">Manage Coordinators</a></li>
            <li><a href="diplomas.php" class="heading-a">Manage Diplomas</a></li>
            <li><a href="modules.php" class="heading-a">Manage Modules</a></li>
            <li><a href="results.php" class="heading-a">Exam Results</a></li>
            <li><a href="modules.php" class="heading-a">Logout</a></li>
        </ul>
    </div>
    <div class="content">

        <?php
            displayPendingStudents();
        ?>

        <!-- <div class="section-title registered-students">Registered Students</div>
        <div class="select-diploma">
            <label for="diploma">Select Diploma</label>
            <select id="diploma">
                <option>Diploma 01</option>
                <option>Diploma 01</option>
            </select>
        </div>
        <table>
            <tr>
                <th data-label="Student Name">Student Name</th>
                <th data-label="Student ID">Student ID</th>
                <th data-label="Registered Diploma">Registered Diploma</th>
                <th data-label="Approve/Disapprove">Approve/Disapprove</th>
            </tr>
            <tr>
                <td data-label="Student Name">Student Name</td>
                <td data-label="Student ID">Student ID</td>
                <td data-label="Registered Diploma">Registered Diploma</td>
                <td data-label="Approve/Disapprove" class="action-buttons">
                    <a href="#">Delete</a>
                </td>
            </tr>
            <tr>
                <td data-label="Student Name">Student Name</td>
                <td data-label="Student ID">Student ID</td>
                <td data-label="Registered Diploma">Registered Diploma</td>
                <td data-label="Approve/Disapprove" class="action-buttons">
                    <a href="#">Delete</a>
                </td>
            </tr>
            <tr>
                <td data-label="Student Name">Student Name</td>
                <td data-label="Student ID">Student ID</td>
                <td data-label="Registered Diploma">Registered Diploma</td>
                <td data-label="Approve/Disapprove" class="action-buttons">
                    <a href="#">Delete</a>
                </td>
            </tr>
            <tr>
                <td data-label="Student Name">Student Name</td>
                <td data-label="Student ID">Student ID</td>
                <td data-label="Registered Diploma">Registered Diploma</td>
                <td data-label="Approve/Disapprove" class="action-buttons">
                    <a href="#">Delete</a>
                </td>
            </tr>
        </table>
    </div> -->

    <!-- Diploma Selection Form -->
    <div class="section-title registered-students">Registered Students</div>
    <form method="POST" action="">
        <div class="select-diploma">
            <label for="diploma">Select Diploma</label>
            <select id="diploma" name="diploma" onchange="this.form.submit()">
                <option value="">-- Select Diploma --</option>
                <?php
                // Fetch all diplomas and populate the dropdown
                $diplomas = fetchDiplomas();
                if ($diplomas && $diplomas->num_rows > 0) {
                    while ($diploma = $diplomas->fetch_assoc()) {
                        $selected = ($selected_diploma == $diploma['diploma_id']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($diploma['diploma_id']) . "' $selected>" . htmlspecialchars($diploma['title']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No diplomas available</option>";
                }
                ?>
            </select>
        </div>
    </form>

    <!-- Registered Students Table -->
    <table>
        <tr>
            <th data-label="Student Name">Student Name</th>
            <th data-label="Student ID">Student ID</th>
            <th data-label="Registered Diploma">Registered Diploma</th>
            <th data-label="Delete">Delete</th>
        </tr>

        <?php
        // If a diploma is selected, fetch and display the students
        if ($selected_diploma) {
            $students = fetchStudentsByDiploma($selected_diploma);
            if ($students->num_rows > 0) {
                while ($row = $students->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['student_number']) . "</td>
                            <td>" . htmlspecialchars($row['diploma_id']) . "</td>
                            <td class='action-buttons'>
                                <a href='?delete_id=" . htmlspecialchars($row['student_number']) . "' onclick='return confirm(\"Are you sure you want to delete this student?\");'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No students found for the selected diploma.</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Please select a diploma to view students.</td></tr>";
        }
        ?>
    </table>

    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>