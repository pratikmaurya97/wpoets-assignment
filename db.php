<?php
$host = "localhost"; // Change if using a remote server
$user = "root"; // Default user in XAMPP/WAMP
$pass = ""; // Leave blank if no password
$dbname = "wppoet"; // Use the database you created

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>