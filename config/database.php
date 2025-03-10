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
    $host = "sql311.infinityfree.com"; // Usually remains localhost on most hosting providers
    $username = "if0_38486928"; // Your hosting username
    $password = "Nj97T4m7XI"; // Your hosting password
    $database = "if0_38486928_saso"; // Your hosting database name
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