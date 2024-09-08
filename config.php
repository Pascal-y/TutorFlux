<?php
// Database credentials
$servername = "localhost"; // usually "localhost"
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "tutorflux_db"; // the name of your database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    return;
}
?>
