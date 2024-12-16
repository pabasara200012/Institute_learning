<?php
require_once '../../db.php';

// Fetch departments from the database
function fetchDepartments() {
    global $mysqli;
    $query = "SELECT department_id, department_name FROM departments";
    return $mysqli->query($query);
}

// Fetch coordinators based on selected department
if (isset($_GET['department_id'])) {
    $department_id = (int)$_GET['department_id'];

    // Log the department ID to check if it's being received correctly
    error_log('Department ID: ' . $department_id);

    $query = "SELECT coordinator_id, name FROM coordinators WHERE department_id = ?";
    $stmt = $mysqli->prepare($query);
    if ($stmt) {
        $stmt->bind_param('i', $department_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $coordinators = [];
        while ($row = $result->fetch_assoc()) {
            $coordinators[] = $row;
        }
        echo json_encode($coordinators);
    } else {
        error_log('Failed to prepare statement: ' . $mysqli->error);  // Log any SQL errors
        echo json_encode([]);  // Return an empty array if the query fails
    }
    exit;
}


// Handle form submission to add a new module
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $module_name = $mysqli->real_escape_string($_POST['module_name']);
    $module_code = $mysqli->real_escape_string($_POST['module_code']);
    $credit_value = (int)$_POST['credit_value'];
    $department_id = (int)$_POST['department_id'];
    $coordinator_ids = $_POST['coordinator_ids']; // Array of coordinator IDs

    // Insert new module into the modules table
    $query = "INSERT INTO modules (code, title, credit_value, department) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssii', $module_code, $module_name, $credit_value, $department_id);

    if ($stmt->execute()) {
        $module_id = $stmt->insert_id; // Get the newly inserted module ID

        // Insert assigned coordinators into the coordinator_modules table
        foreach ($coordinator_ids as $coordinator_id) {
            $query = "INSERT INTO coordinator_modules (coordinator_id, module_id) VALUES (?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ii', $coordinator_id, $module_id);
            $stmt->execute();
        }

        echo "Module added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch all departments to populate the dropdown
$departments = fetchDepartments();

// Fetch all added modules along with their departments and assigned coordinators
function fetchModules() {
    global $mysqli;
    $query = "
        SELECT m.module_id, m.title AS module_name, m.code AS module_code, m.credit_value, d.department_name
        FROM modules m
        JOIN departments d ON m.department = d.department_id
    ";
    return $mysqli->query($query);
}

// Fetch the coordinators assigned to each module
function fetchModuleCoordinators($module_id) {
    global $mysqli;
    $query = "
        SELECT c.name 
        FROM coordinator_modules cm
        JOIN coordinators c ON cm.coordinator_id = c.coordinator_id
        WHERE cm.module_id = ?
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $module_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle delete module request
if (isset($_GET['delete_id'])) {
    $module_id = (int)$_GET['delete_id'];

    // Delete from coordinator_modules
    $query = "DELETE FROM coordinator_modules WHERE module_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $module_id);
    $stmt->execute();

    // Delete from modules table
    $query = "DELETE FROM modules WHERE module_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $module_id);
    if ($stmt->execute()) {
        echo "Module deleted successfully!";
    } else {
        echo "Error deleting module: " . $stmt->error;
    }
}

// Fetch all modules to display
$modules = fetchModules();

?>

<html>
<head>
    <title>Register New Coordinators</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // AJAX to fetch coordinators based on selected department
    function fetchCoordinators(department_id) {
        console.log('Selected department:', department_id);  // Log the selected department ID
        $.get('modules.php', { department_id: department_id }, function(data) {
            console.log('Returned data:', data);  // Log the returned data from the PHP script
            const coordinators = JSON.parse(data);
            let coordinatorCheckboxes = '';
            coordinators.forEach(coordinator => {
                coordinatorCheckboxes += `<label><input type="checkbox" name="coordinator_ids[]" value="${coordinator.coordinator_id}"> ${coordinator.name}</label><br>`;
            });
            document.getElementById('module-coordinators').innerHTML = coordinatorCheckboxes;
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching coordinators:', textStatus, errorThrown);  // Log any errors
        });
    }
</script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .heading-a{
            text-decoration: none;
            color: white;
        }
        .container {
            /* width: 800px; */
            margin-left: 15%;
            margin-right: 15%;
            margin-top: 5%;
            margin-bottom: 5%;
            padding-bottom: 5%
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
        
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            margin-top: 5%;
        }
        h2 {
            font-size: 20px;
        }
        .form-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #000;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
            padding: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], select, .checkbox-group {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #000;
            border-radius: 4px;
        }
        .checkbox-group {
            padding: 10px;
            border: 1px solid #000;
            border-radius: 4px;
            background-color: #e0e0e0;
        }
        .checkbox-group label {
            display: block;
            margin-bottom: 5px;
        }
        .btn {
            background-color: #d32f2f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn:hover {
            background-color: #b71c1c;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
        }
        .form-row .form-group {
            flex: 1;
            margin-right: 20px;
        }
        .form-row .form-group:last-child {
            margin-right: 0;
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
            background-color: #d3d3d3;
        }
        .edit, .delete {
            color: red;
            cursor: pointer;
        }
        .edit {
            margin-right: 10px;
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

    <div class="form-section">
    <h2>Introduce a new Module</h2>
    <form method="POST" action="modules.php">
        <div class="form-row">
            <div class="form-group">
                <label for="module-name">MODULE NAME</label>
                <input type="text" id="module-name" name="module_name" placeholder="Eg:- DhanushkaSisil" required>
            </div>
            <div class="form-group">
                <label for="module-code">MODULE CODE</label>
                <input type="text" id="module-code" name="module_code" placeholder="Eg:- 16078" required>
            </div>
            <div class="form-group">
                <label for="credit-value">CREDIT VALUE</label>
                <input type="text" id="credit-value" name="credit_value" placeholder="Eg:- 3" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="module-department">SELECT DEPARTMENT</label>
                <select id="module-department" name="department_id" onchange="fetchCoordinators(this.value)" required>
                    <option value="">Select Department</option>
                    <?php
                    while ($department = $departments->fetch_assoc()) {
                        echo "<option value='" . $department['department_id'] . "'>" . $department['department_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="module-coordinators">SELECT MODULE COORDINATORS</label>
                <div class="checkbox-group" id="module-coordinators">
                    <!-- Coordinators will be dynamically loaded here based on department selection -->
                </div>
            </div>
        </div>
        <button type="submit" class="btn">ADD MODULE</button>
    </form>

    <h2 style="margin-top: 100px">Added Modules</h2>
<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Module Name</th>
            <th>Module Code</th>
            <th>Department</th>
            <th>Assigned Coordinators</th>
            <th>Credit Value</th>
            <th>Delete or Edit</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($module = $modules->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($module['module_name']); ?></td>
                <td><?php echo htmlspecialchars($module['module_code']); ?></td>
                <td><?php echo htmlspecialchars($module['department_name']); ?></td>
                <td>
                    <?php
                    // Fetch and display the coordinators assigned to this module
                    $coordinators = fetchModuleCoordinators($module['module_id']);
                    $coordinatorNames = [];
                    while ($coordinator = $coordinators->fetch_assoc()) {
                        $coordinatorNames[] = $coordinator['name'];
                    }
                    echo implode(', ', $coordinatorNames);
                    ?>
                </td>
                <td><?php echo htmlspecialchars($module['credit_value']); ?></td>
                <td>
                    <a href="editModules.php?id=<?php echo $module['module_id']; ?>">Edit</a> |
                    <a href="?delete_id=<?php echo $module['module_id']; ?>" onclick="return confirm('Are you sure you want to delete this module?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
    </div>
                </div>


    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>