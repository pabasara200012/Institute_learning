<?php
require_once '../../db.php'; // Include your database connection

// Fetch departments
function fetchDepartments() {
    global $mysqli;
    $query = "SELECT department_id, department_name FROM departments";
    return $mysqli->query($query);
}

// Fetch coordinator data by ID
function fetchCoordinator($id) {
    global $mysqli;
    $query = "SELECT * FROM coordinators WHERE coordinator_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Fetch modules assigned to coordinator
function fetchAssignedModules($coordinator_id) {
    global $mysqli;
    $query = "SELECT module_id FROM coordinator_modules WHERE coordinator_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $coordinator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignedModules = [];
    while ($row = $result->fetch_assoc()) {
        $assignedModules[] = $row['module_id'];
    }
    return $assignedModules;
}

// Fetch all modules
function fetchModules() {
    global $mysqli;
    $query = "SELECT module_id, title FROM modules";
    return $mysqli->query($query);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coordinator_id = (int)$_POST['coordinator_id'];
    $name = $mysqli->real_escape_string($_POST['name']);
    $lecturer_id = $mysqli->real_escape_string($_POST['lecturer_id']);
    $department_id = (int)$_POST['department'];
    $password = !empty($_POST['password']) ? $mysqli->real_escape_string($_POST['password']) : null; // Remove password_hash

    // Update coordinator data
    if ($password) {
        // Update including the plain text password
        $query = "UPDATE coordinators SET name = ?, lecturer_id = ?, department_id = ?, account_password = ? WHERE coordinator_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssisi', $name, $lecturer_id, $department_id, $password, $coordinator_id);
    } else {
        // Update without modifying the password
        $query = "UPDATE coordinators SET name = ?, lecturer_id = ?, department_id = ? WHERE coordinator_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssii', $name, $lecturer_id, $department_id, $coordinator_id);
    }

    if ($stmt->execute()) {
        // Update assigned modules
        $query = "DELETE FROM coordinator_modules WHERE coordinator_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $coordinator_id);
        $stmt->execute();

        if (isset($_POST['modules'])) {
            foreach ($_POST['modules'] as $module_id) {
                $module_id = (int)$module_id;
                $query = "INSERT INTO coordinator_modules (coordinator_id, module_id) VALUES (?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('ii', $coordinator_id, $module_id);
                $stmt->execute();
            }
        }

        echo "Coordinator updated successfully!";
    } else {
        echo "Error updating coordinator: " . $stmt->error;
    }
}

// Fetch existing coordinator data for editing
if (isset($_GET['id'])) {
    $coordinator = fetchCoordinator((int)$_GET['id']);
    $assignedModules = fetchAssignedModules((int)$_GET['id']);
} else {
    die("Coordinator ID not provided.");
}

// Fetch all departments
$departments = fetchDepartments();

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
    <h1>Update Coordinator</h1>
<form method="POST" action="">
    <input type="hidden" name="coordinator_id" value="<?php echo $coordinator['coordinator_id']; ?>">

    <div class="form-row">
        <div class="form-group">
            <label for="coordinator-name">ENTER COORDINATOR NAME</label>
            <input type="text" id="coordinator-name" name="name" value="<?php echo htmlspecialchars($coordinator['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="coordinator-id">ENTER COORDINATOR ID</label>
            <input type="text" id="coordinator-id" name="lecturer_id" value="<?php echo htmlspecialchars($coordinator['lecturer_id']); ?>" required>
        </div>
        <div class="form-group">
            <label for="account-password">ENTER ACCOUNT PASSWORD (Leave blank to keep current password)</label>
            <input type="password" id="account-password" name="password">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="department">SELECT DEPARTMENT</label>
            <select id="department" name="department" required>
                <?php while ($department = $departments->fetch_assoc()): ?>
                    <option value="<?php echo $department['department_id']; ?>" <?php if ($coordinator['department_id'] == $department['department_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($department['department_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="checkbox-group">
            <label>SELECT ASSIGNED MODULES</label>
            <?php
            $modules = fetchModules(); // Fetch all modules
            while ($module = $modules->fetch_assoc()): ?>
                <label>
                    <input type="checkbox" name="modules[]" value="<?php echo $module['module_id']; ?>"
                        <?php if (in_array($module['module_id'], $assignedModules)) echo 'checked'; ?>>
                    <?php echo htmlspecialchars($module['title']); ?>
                </label><br>
            <?php endwhile; ?>
        </div>
    </div>

    <button type="submit" class="btn">UPDATE COORDINATOR</button>
</form>
    </div>

    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>