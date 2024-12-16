<?php
session_start();
include '../../db.php'; // Your database connection

// Function to fetch modules assigned to the coordinator
function fetchModulesForCoordinator($mysqli, $coordinator_id) {
    $query = "SELECT m.module_id, m.title 
              FROM coordinator_modules cm
              JOIN modules m ON cm.module_id = m.module_id
              WHERE cm.coordinator_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $coordinator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $modules = [];

    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }

    return $modules;
}

// Function to fetch students in a specific module
function fetchStudentsByModule($mysqli, $module_id) {
    $query = "SELECT s.student_number, s.name 
              FROM students s
              JOIN diploma_module dm ON s.diploma_id = dm.diploma_id
              WHERE dm.module_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $module_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    return $students;
}

// Function to save attendance data
function saveAttendance($mysqli, $attendance_data) {
    $query = "INSERT INTO attendance (student_id, module_id, week, attendance_status) 
              VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    foreach ($attendance_data as $attendance) {
        $stmt->bind_param("iiis", 
            $attendance['student_id'], 
            $attendance['module_id'], 
            $attendance['week'], 
            $attendance['attendance_status']);
        $stmt->execute();
    }

    return true;
}

// Handle GET request to fetch modules
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SESSION['coordinator_id'])) {
    $coordinator_id = $_SESSION['coordinator_id'];
    $modules = fetchModulesForCoordinator($mysqli, $coordinator_id);
    echo json_encode($modules);
}

// Handle POST request to save attendance and fetch students
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['module_id'])) {
        $module_id = $_POST['module_id'];
        $students = fetchStudentsByModule($mysqli, $module_id);
        echo json_encode($students);
    } elseif (isset($_POST['attendance_data'])) {
        $attendance_data = json_decode($_POST['attendance_data'], true);
        $success = saveAttendance($mysqli, $attendance_data);

        if ($success) {
            echo "Attendance saved successfully!";
        } else {
            echo "Failed to save attendance!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            padding: 5px;
            width: 100%;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Your Selected Module: <span id="selected-module-name">CS 3101</span></h2>
    <div class="form-group">
        <div>
            <label for="week">ENTER WEEK</label>
            <input type="text" id="week" placeholder="Eg:- 4">
        </div>
        <div>
            <label for="module">SELECT MODULE</label>
            <select id="module">
                <option>Select Module</option>
            </select>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Absent/Present</th>
            </tr>
        </thead>
        <tbody id="students-tbody">
            <!-- Dynamic student rows will be inserted here -->
        </tbody>
    </table>
    
    <button class="submit-btn">SUBMIT ATTENDANCE</button>

    <script>
// Fetch modules and populate the select dropdown
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('module');
    
    fetch('attendance.php', {
        method: 'GET',
    })
    .then(response => response.json())
    .then(modules => {
        modules.forEach(module => {
            const option = document.createElement('option');
            option.value = module.module_id;
            option.textContent = module.title;
            moduleSelect.appendChild(option);
        });
    });
});

// Handle module selection and fetch students
document.getElementById('module').addEventListener('change', function() {
    const moduleId = this.value;
    const moduleName = this.options[this.selectedIndex].textContent;
    document.getElementById('selected-module-name').textContent = moduleName;

    fetch('attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `module_id=${moduleId}`
    })
    .then(response => response.json())
    .then(students => {
        const tbody = document.getElementById('students-tbody');
        tbody.innerHTML = ''; // Clear existing rows
        students.forEach(student => {
            const row = `
                <tr>
                    <td>${student.name}</td>
                    <td>${student.student_number}</td>
                    <td><input type="checkbox"></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    });
});

// Function to validate form inputs
function validateForm() {
    const weekInput = document.getElementById('week').value;
    const moduleSelect = document.getElementById('module').value;

    if (!weekInput || isNaN(weekInput) || weekInput <= 0) {
        alert("Please enter a valid week number.");
        return false;
    }

    if (moduleSelect === "Select Module" || moduleSelect === "") {
        alert("Please select a module.");
        return false;
    }

    return true;
}

// Handle attendance submission
document.querySelector('.submit-btn').addEventListener('click', function() {
    if (!validateForm()) {
        return; // Stop submission if validation fails
    }

    const attendanceRows = document.querySelectorAll('tbody tr');
    const attendanceData = [];
    const moduleId = document.getElementById('module').value;
    const week = document.getElementById('week').value;
    
    attendanceRows.forEach(row => {
        const studentId = row.querySelector('td:nth-child(2)').textContent;
        const attendanceStatus = row.querySelector('input[type="checkbox"]').checked ? 'Present' : 'Absent';
        
        attendanceData.push({
            student_id: studentId,
            module_id: moduleId,
            week: week,
            attendance_status: attendanceStatus
        });
    });

    fetch('attendance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `attendance_data=${JSON.stringify(attendanceData)}`
    })
    .then(response => response.text())
    .then(message => {
        alert(message);
    });
});
    </script>
</body>
</html>
