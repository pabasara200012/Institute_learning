<?php
require_once '../../db.php';

// Fetch departments from the database
function fetchDepartments() {
    global $mysqli;
    $query = "SELECT department_id, department_name FROM departments";
    return $mysqli->query($query);
}

// Fetch modules from the database
function fetchModules() {
    global $mysqli;
    $query = "SELECT module_id, title FROM modules";
    return $mysqli->query($query);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $mysqli->real_escape_string($_POST['name']);
    $lecturer_id = $mysqli->real_escape_string($_POST['lecturer_id']);
    $department_id = (int)$_POST['department'];
    $account_password = $mysqli->real_escape_string($_POST['password']); // Hash the password

    // Insert into coordinators table
    $query = "INSERT INTO coordinators (lecturer_id, name, department_id, account_password) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssis', $lecturer_id, $name, $department_id, $account_password);

    if ($stmt->execute()) {
        $coordinator_id = $stmt->insert_id; // Get the newly inserted coordinator ID

        // Insert assigned modules
        if (isset($_POST['modules'])) {
            foreach ($_POST['modules'] as $module_id) {
                $module_id = (int)$module_id;
                $query = "INSERT INTO coordinator_modules (coordinator_id, module_id) VALUES (?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('ii', $coordinator_id, $module_id);
                $stmt->execute();
            }
        }

        echo "Coordinator and assigned modules added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch coordinators and their assigned modules
function fetchCoordinators() {
    global $mysqli;
    $query = "SELECT c.coordinator_id, c.name, c.lecturer_id, d.department_name
              FROM coordinators c
              JOIN departments d ON c.department_id = d.department_id";
    return $mysqli->query($query);
}

// Fetch assigned modules for a coordinator
function fetchAssignedModules($coordinator_id) {
    global $mysqli;
    $query = "SELECT m.title 
              FROM coordinator_modules cm
              JOIN modules m ON cm.module_id = m.module_id
              WHERE cm.coordinator_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $coordinator_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle delete functionality
if (isset($_GET['delete_id'])) {
    $coordinator_id = (int)$_GET['delete_id'];

    // Delete from coordinator_modules
    $deleteModulesQuery = "DELETE FROM coordinator_modules WHERE coordinator_id = ?";
    $stmt = $mysqli->prepare($deleteModulesQuery);
    $stmt->bind_param('i', $coordinator_id);
    $stmt->execute();

    // Delete from coordinators
    $deleteCoordinatorQuery = "DELETE FROM coordinators WHERE coordinator_id = ?";
    $stmt = $mysqli->prepare($deleteCoordinatorQuery);
    $stmt->bind_param('i', $coordinator_id);
    if ($stmt->execute()) {
        echo "Coordinator deleted successfully.";
    } else {
        echo "Error deleting coordinator: " . $stmt->error;
    }
}

// Fetch all coordinators
$coordinators = fetchCoordinators();
?>

<html>
<head>
    <title>Register New Coordinators</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .container {
            /* width: 800px; */
            margin-left: 15%;
            margin-right: 15%;
            margin-top: 5%;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* display: flex; */
            /* justify-content: center;
            align-items: center; */
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
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .form-group {
            flex: 1;
            margin-right: 20px;
        }
        .form-group:last-child {
            margin-right: 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-group select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="none" stroke="black" stroke-width=".5" d="M2 0L0 2h4z"/></svg>') no-repeat right 10px center;
            background-size: 10px;
        }
        .checkbox-group {
            border: 2px solid #007bff;
            padding: 10px;
            border-radius: 5px;
            width: 48%;
            box-sizing: border-box;
        }
        .checkbox-group label {
            display: block;
            margin-bottom: 5px;
        }
        .checkbox-group input {
            margin-right: 10px;
        }
        .btn {
            background-color: #d9534f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #e0e0e0;
        }
        .edit, .delete, .ok {
            color: red;
            cursor: pointer;
        }
        .separator {
            border-left: 2px solid black;
            height: 100%;
            margin-left: 10px;
            margin-right: 10px;
        }
        .editable {
            border: 1px solid #ddd;
            padding: 5px;
        }
        .heading-a{
            text-decoration: none;
            color: white;
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

    <div class="container">
    <h1>Register New Coordinators</h1>
    <form method="POST" action="coordinators.php">
        <div class="form-row">
            <div class="form-group">
                <label for="coordinator-name">ENTER COORDINATOR NAME</label>
                <input type="text" id="coordinator-name" name="name" placeholder="Eg:- DhanushkaSisil" required>
            </div>
            <div class="form-group">
                <label for="coordinator-id">ENTER COORDINATOR ID</label>
                <input type="text" id="coordinator-id" name="lecturer_id" placeholder="Eg:- 16078" required>
            </div>
            <div class="form-group">
                <label for="account-password">ENTER ACCOUNT PASSWORD</label>
                <input type="password" id="account-password" name="password" placeholder="Eg:- Password123" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="department">SELECT DEPARTMENT</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <?php
                    $departments = fetchDepartments();
                    while ($department = $departments->fetch_assoc()) {
                        echo "<option value='" . $department['department_id'] . "'>" . $department['department_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="checkbox-group">
                <label>SELECT ASSIGNED MODULES</label>
                <?php
                $modules = fetchModules();
                while ($module = $modules->fetch_assoc()) {
                    echo "<label><input type='checkbox' name='modules[]' value='" . $module['module_id'] . "'>" . $module['title'] . "</label><br>";
                }
                ?>
            </div>
        </div>
        <button type="submit" class="btn">ADD COORDINATOR</button>
    </form>

    <div style="margin-top: 100px; margin-bottom: 100px; margin-left: 5%; margin-right: 5%; padding-bottom: 100px">
    <h1>Registered Coordinators</h1>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Lecturer Name</th>
                        <th>Lecturer ID</th>
                        <th>Department</th>
                        <th>Assigned Modules</th>
                        <th>Delete or Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($coordinator = $coordinators->fetch_assoc()): ?>
                        <tr>
                            <td class="lecturer-name"><?php echo htmlspecialchars($coordinator['name']); ?></td>
                            <td class="lecturer-id"><?php echo htmlspecialchars($coordinator['lecturer_id']); ?></td>
                            <td class="department"><?php echo htmlspecialchars($coordinator['department_name']); ?></td>
                            <td class="assigned-modules">
                                <?php
                                // Fetch assigned modules for this coordinator
                                $modules = fetchAssignedModules($coordinator['coordinator_id']);
                                $moduleNames = [];
                                while ($module = $modules->fetch_assoc()) {
                                    $moduleNames[] = $module['title'];
                                }
                                echo implode(', ', $moduleNames);
                                ?>
                            </td>
                            <td>
                                <a href="updateCoordinators.php?id=<?php echo $coordinator['coordinator_id']; ?>">Edit</a> |
                                <a href="?delete_id=<?php echo $coordinator['coordinator_id']; ?>" onclick="return confirm('Are you sure you want to delete this coordinator?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
    </div>

    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>