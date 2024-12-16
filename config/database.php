<?php
// Configuration for database connection
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'diploma_institute');

// Create connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists
if (!$conn->query("SHOW DATABASES LIKE '" . DB_NAME . "'")->num_rows) {
    // Create database
    $sql = "CREATE DATABASE " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }
}

// Select database
$conn->select_db(DB_NAME);

// Create tables
$sql = "
    CREATE TABLE students (
        student_number INT PRIMARY KEY,
        name VARCHAR(255),
        address VARCHAR(255),
        diploma_id INT
    );

    CREATE TABLE diplomas (
        diploma_id INT PRIMARY KEY,
        code VARCHAR(255),
        title VARCHAR(255),
        description TEXT
    );

    CREATE TABLE modules (
        module_id INT PRIMARY KEY,
        code VARCHAR(255),
        title VARCHAR(255),
        credit_value INT,
        coordinator_id INT,
        department VARCHAR(255)
    );

    CREATE TABLE coordinators (
        coordinator_id INT PRIMARY KEY,
        lecturer_id INT,
        name VARCHAR(255),
        department VARCHAR(255)
    );

    CREATE TABLE semesters (
        semester_id INT PRIMARY KEY,
        start_date DATE,
        end_date DATE
    );

    CREATE TABLE attendance (
        attendance_id INT PRIMARY KEY,
        student_id INT,
        module_id INT,
        week INT,
        attendance_status BOOLEAN
    );

    CREATE TABLE enrollment (
        enrollment_id INT PRIMARY KEY,
        student_id INT,
        module_id INT,
        semester_id INT
    );

    CREATE TABLE lecturer_modules (
        lecturer_id INT,
        module_id INT,
        PRIMARY KEY (lecturer_id, module_id)
    );

    CREATE TABLE users (
        user_id INT PRIMARY KEY,
        username VARCHAR(255),
        password VARCHAR(255)
    );
";

if ($conn->multi_query($sql) === TRUE) {
    echo "Tables created successfully";
} else {
    echo "Error creating tables: " . $conn->error;
}

// Create initial admin user
$username = 'admin';
$password = 'admin';
$role = 'admin';

$sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password')";
if ($conn->query($sql) === TRUE) {
    echo "Initial admin user created successfully";
} else {
    echo "Error creating initial admin user: " . $conn->error;
}

$conn->close();
?>