<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $host = 'host.docker.internal';
// $db = 'dbexhibition';
// $user = 'root';
// $pass = '';

$host = 'sql7.freesqldatabase.com';
$db = 'sql7803545';
$user = 'sql7803545';
$pass = 'SDwxx86F9Z';



$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connected to database successfully!";
}

