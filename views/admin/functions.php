<?php
require_once '../../db.php';

// Fetch pending students where status is 0
function fetchPendingStudents() {
    global $mysqli;
    
    $query = "SELECT student_number, name, diploma_id FROM students WHERE status = 0";
    $result = $mysqli->query($query);
    
    return $result;
}

// Display the pending students in an HTML table
function displayPendingStudents() {
    $students = fetchPendingStudents();
    
    if ($students->num_rows > 0) {
        echo '<div class="section-title">Pending Students</div>';
        echo '<table border="1" cellpadding="10">
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Registered Diploma</th>
                    <th>Approve/Disapprove</th>
                </tr>';
        
        while ($row = $students->fetch_assoc()) {
            echo '<tr>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                    <td>' . htmlspecialchars($row['student_number']) . '</td>
                    <td>' . htmlspecialchars($row['diploma_id']) . '</td>
                    <td class="action-buttons">
                        <a href="?action=approve&id=' . $row['student_number'] . '">Approve</a> |
                        <a href="?action=reject&id=' . $row['student_number'] . '">Reject</a>
                    </td>
                  </tr>';
        }
        
        echo '</table>';
    } else {
        echo '<div class="section-title">Pending Students</div>';
        echo '<p>No pending students found.</p>';
    }
}

function approveStudent() {
    global $mysqli;
    if (isset($_GET['id'])) {
        $student_id = $mysqli->real_escape_string($_GET['id']);
    
        // Update the status of the student to 1 (approved)
        $query = "UPDATE students SET status = 1 WHERE student_number = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $student_id);
    
        if ($stmt->execute()) {
            // Redirect back to the pending students page
            header('Location: dashboard.php');
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

function  rejectStudent(){
    global $mysqli;
    if (isset($_GET['id'])) {
        $student_id = $mysqli->real_escape_string($_GET['id']);
    
        // Delete the student from the database
        $query = "DELETE FROM students WHERE student_number = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $student_id);
    
        if ($stmt->execute()) {
            // Redirect back to the pending students page
            header('Location: dashboard.php');
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Handle actions from the URL (approve or reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $student_id = $_GET['id'];

    // Call the appropriate function based on the action
    if ($action == 'approve') {
        approveStudent($student_id);
    } elseif ($action == 'reject') {
        rejectStudent($student_id);
    }
}

// Fetch all diplomas from the database
function fetchDiplomas() {
    global $mysqli;
    
    // Query to get all diplomas
    $query = "SELECT diploma_id, title FROM diplomas";
    $result = $mysqli->query($query);
    
    // Check for errors in the query
    if (!$result) {
        echo "Error fetching diplomas: " . $mysqli->error;
        return null;
    }

    return $result;
}

// Fetch students based on selected diploma
function fetchStudentsByDiploma($diploma_id) {
    global $mysqli;
    $query = "SELECT student_number, name, diploma_id FROM students WHERE diploma_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $diploma_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle student deletion
if (isset($_GET['delete_id'])) {
    $student_id = $mysqli->real_escape_string($_GET['delete_id']);
    
    // Delete the student from the database
    $query = "DELETE FROM students WHERE student_number = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $student_id);
    if ($stmt->execute()) {
        echo "Student with ID $student_id has been deleted.";
    } else {
        echo "Error deleting student: " . $stmt->error;
    }
}

// Handle diploma selection
$selected_diploma = isset($_POST['diploma']) ? $_POST['diploma'] : null;


?>
