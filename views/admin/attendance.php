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
        .header .admin-functions {
            position: relative;
        }
        .header .admin-functions:hover .dropdown {
            display: block;
        }
        .header .admin-functions .dropdown {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ccc;
            color: black;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header .admin-functions .dropdown a {
            display: block;
            color: black;
            text-decoration: none;
            margin: 5px 0;
        }
        .container {
            padding: 20px 40px; /* Added padding to left and right */
            margin-left: 10%;
            margin-right: 10%;
        }
        .container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .dropdown-select {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .dropdown-select div {
            width: 30%;
        }
        .dropdown-select label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }
        .dropdown-select select {
            padding: 10px;
            font-size: 16px;
            width: 100%;
        }
        .show-attendance-btn, .extract-pdf-btn {
            background-color: #d50000; /* Red color from the screenshot */
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 20px 0;
            display: block;
            width: 100%;
            text-align: center;
        }
        .extract-pdf-btn {
            margin-bottom: 50px; /* Added bottom padding */
        }
        .attendance-info {
            margin: 20px 0;
        }
        .attendance-info p {
            font-size: 18px;
            margin: 5px 0;
        }
        .attendance-info .module-info {
            padding-top: 100px; /* Added top padding */
        }
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .attendance-table th, .attendance-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .attendance-table th {
            background-color: #e0e0e0;
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
        .heading-a{
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Learning management system</h1>
        <div class="admin-functions">
            <span>Admin Functions</span>
            <div class="dropdown">
            <li><a href="dashboard.php" class="heading-a">Manage Students</a></li>
            <li><a href="coordinators.php" class="heading-a">Manage Coordinators</a></li>
            <li><a href="diplomas.php" class="heading-a">Manage Diplomas</a></li>
            <li><a href="modules.php" class="heading-a">Manage Modules</a></li>
            <li><a href="results.php" class="heading-a">Exam Results</a></li>
            <li><a href="modules.php" class="heading-a">Logout</a></li>
            </div>
        </div>
    </div>
    <div class="container">
        <h2>Attendance of students</h2>
        <div class="dropdown-select">
            <div>
                <label for="department">SELECT DEPARTMENT</label>
                <select id="department">
                    <option>Select Department</option>
                    <option>Stat Department</option>
                    <option>Maths Department</option>
                </select>
            </div>
            <div>
                <label for="module">SELECT MODULE</label>
                <select id="module">
                    <option>Select Department</option>
                    <option>Stat Department</option>
                    <option>Maths Department</option>
                </select>
            </div>
            <div>
                <label for="week">SELECT WEEK</label>
                <select id="week">
                    <option>Select Department</option>
                    <option>Stat Department</option>
                    <option>Maths Department</option>
                </select>
            </div>
        </div>
        <button class="show-attendance-btn">SHOW ATTENDANCE</button>
        <div class="attendance-info">
            <p class="module-info">Module : CS 3101</p>
            <p>Week : 15</p>
            <p>Attendance:</p>
        </div>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Attendance : Yes</th>
                    <th>Register No</th>
                    <th>Attendance : No</th>
                    <th>Register No</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                </tr>
                <tr>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                </tr>
                <tr>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                </tr>
                <tr>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                </tr>
                <tr>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                    <td>W M D S B Wijekoon</td>
                    <td>4578</td>
                </tr>
            </tbody>
        </table>
        <button class="extract-pdf-btn">EXTRACT TO A PDF</button>
    </div>
    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>