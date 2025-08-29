<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "note_app_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
