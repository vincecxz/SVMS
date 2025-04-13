<?php
// Function to determine if we're on localhost
function isLocalhost() {
    $whitelist = array('127.0.0.1', '::1', 'localhost');
    return in_array($_SERVER['SERVER_NAME'], $whitelist);
}

// Set database credentials based on environment
if (isLocalhost()) {
    // Local database credentials
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "saso";
} else {
    // Hosting database credentials
    $host = "localhost"; // Usually remains localhost on most hosting providers
    $username = "root"; // Your hosting username
    $password = ""; // Your hosting password
    $database = "saso"; // Your hosting database name
}

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set UTF-8 character set
mysqli_set_charset($conn, "utf8mb4");
?> 