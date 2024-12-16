<?php
// db.php
$host = 'localhost';  // Change if needed
$db   = 'diploma_institute';        // Database name
$user = 'root';       // Database user
$pass = '';           // Database password

// Create a connection using MySQLi
$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
