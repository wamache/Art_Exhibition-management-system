<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM artworks WHERE id = $id");
header("Location: manage_artworks.php");
exit;
