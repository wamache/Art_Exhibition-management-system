<?php
$host = 'localhost';
$db = 'dbexhibition';
$user = 'root';
<<<<<<< HEAD
$pass = ''; // default for XAMPP
=======
$pass = '';
>>>>>>> 1c73759ed0b50120e64caf8151fcc524432d3bd7

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
