<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM users WHERE id = $id");
header("Location: manage_users.php");
exit;
