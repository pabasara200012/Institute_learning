<?php
require_once '../../db.php';

// Fetch departments from the database
function fetchDepartments() {
    global $mysqli;
    $query = "SELECT department_id, department_name FROM departments";
    return $mysqli->query($query);
}

// Fetch modules by department ID
function fetchModulesByDepartment($department_id) {
    global $mysqli;
    $query = "SELECT module_id, title FROM modules WHERE department = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $department_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Check if the request is an AJAX request to fetch modules
if (isset($_GET['department_id'])) {
    $department_id = (int)$_GET['department_id'];
    $modules = fetchModulesByDepartment($department_id);
    $moduleArray = [];

    while ($module = $modules->fetch_assoc()) {
        $moduleArray[] = [
            'module_id' => $module['module_id'],
            'title' => $module['title'],
        ];
    }

    // Return JSON response for AJAX
    echo json_encode($moduleArray);
    exit; // Prevent further script execution
}

// Fetch departments for the form
$departments = fetchDepartments();

// Check if diploma code is unique
function isDiplomaCodeUnique($diploma_code) {
    global $mysqli;
    $query = "SELECT diploma_id FROM diplomas WHERE code = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $diploma_code);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0; // Return true if no rows are found (i.e., code is unique)
}


// Handle the form submission to insert a new diploma with validation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $diploma_name = trim($mysqli->real_escape_string($_POST['diploma_name']));
    $diploma_code = trim($mysqli->real_escape_string($_POST['diploma_code']));
    $description = trim($mysqli->real_escape_string($_POST['description']));
    $selected_modules = isset($_POST['module_ids']) ? $_POST['module_ids'] : []; // Array of selected module IDs

    // Validate form inputs
    if (empty($diploma_name)) {
        $errors[] = "Diploma name is required.";
    }

    if (empty($diploma_code)) {
        $errors[] = "Diploma code is required.";
    } elseif (!isDiplomaCodeUnique($diploma_code)) {
        $errors[] = "Diploma code must be unique.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if (empty($selected_modules)) {
        $errors[] = "At least one module must be selected.";
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        // Insert the new diploma into the diplomas table
        $query = "INSERT INTO diplomas (code, title, description) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sss', $diploma_code, $diploma_name, $description);
        if ($stmt->execute()) {
            $diploma_id = $mysqli->insert_id;

            // Insert the selected modules for the diploma
            foreach ($selected_modules as $module_id) {
                $query = "INSERT INTO diploma_modules (diploma_id, module_id) VALUES (?, ?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('ii', $diploma_id, $module_id);
                $stmt->execute();
            }

            echo "Diploma added successfully!";
        } else {
            echo "Error adding diploma: " . $stmt->error;
        }
    } else {
        // Output validation errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>

<html>
<head>
<title>Introduce a new Diploma</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Fetch modules dynamically when a department is selected
    function fetchModules(department_id) {
        $.get('diplomas.php', { department_id: department_id }, function(data) {
            const modules = JSON.parse(data);
            let moduleCheckboxes = '';
            modules.forEach(module => {
                moduleCheckboxes += `<label><input type="checkbox" name="module_ids[]" value="${module.module_id}"> ${module.title}</label><br>`;
            });
            document.getElementById('modules').innerHTML = moduleCheckboxes;
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching modules:', textStatus, errorThrown);
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
        .heading-a{
            text-decoration: none;
            color: white;
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
    <div class="form-section">
    <h2>Introduce a new Diploma</h2>
        <form method="POST" action="diplomas.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="diploma-name">DIPLOMA NAME</label>
                    <input type="text" id="diploma-name" name="diploma_name" placeholder="Eg:- DhanushkaSisil" required>
                </div>
                <div class="form-group">
                    <label for="diploma-code">DIPLOMA CODE</label>
                    <input type="text" id="diploma-code" name="diploma_code" placeholder="Eg:- 16078" required>
                </div>
                <div class="form-group">
                    <label for="description">DESCRIPTION</label>
                    <input type="text" id="description" name="description" placeholder="Eg:- Lorem Ipsum Lorem Ipsum" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="department">SELECT DEPARTMENT</label>
                    <select id="department" name="department" onchange="fetchModules(this.value)" required>
                        <option value="">Select Department</option>
                        <?php while ($department = $departments->fetch_assoc()): ?>
                            <option value="<?php echo $department['department_id']; ?>">
                                <?php echo htmlspecialchars($department['department_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modules">SELECT MODULES</label>
                    <div class="checkbox-group" id="modules">
                        <!-- Dynamic checkboxes will be loaded here -->
                    </div>
                </div>
            </div>
            <button type="submit" class="btn">ADD DIPLOMA</button>
        </form>
    </div>


    <h2>Added Diplomas</h2>
    <table>
        <thead>
            <tr>
                <th>Diploma Name</th>
                <th>Diploma Code</th>
                <th>Department</th>
                <th>Assigned Modules</th>
                <th>Delete or Edit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>W M D S B Wijekoon</td>
                <td>4578</td>
                <td>Statistics</td>
                <td>Maths</td>
                <td><span class="edit">Edit</span><span class="delete">Delete</span></td>
            </tr>
            <tr>
                <td>W M D S B Wijekoon</td>
                <td>4578</td>
                <td>Statistics</td>
                <td>Maths</td>
                <td><span class="edit">Edit</span><span class="delete">Delete</span></td>
            </tr>
            <tr>
                <td>W M D S B Wijekoon</td>
                <td>4578</td>
                <td>Statistics</td>
                <td>Maths</td>
                <td><span class="edit">Edit</span><span class="delete">Delete</span></td>
            </tr>
        </tbody>
    </table>
    </div>


    <div class="footer">
        All rights are reserved. Designed and developed by Dhanushka Sisil
    </div>
</body>
</html>