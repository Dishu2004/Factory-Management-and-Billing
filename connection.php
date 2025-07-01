<?php
// connection.php

// Database connection settings
$host = 'localhost'; // Change if your database host is different
$db_username = 'root'; // Database username
$db_password = 'root1234'; // Database password
$db_name = 'bill'; // Database name

// Create a connection to the database
$conn = new mysqli($host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>
