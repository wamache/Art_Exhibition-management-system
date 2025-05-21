<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') die("Access denied");
include '../config/db.php';

$exhibition_id = $_GET['exhibition_id'];
$artwork_id = $_GET['artwork_id'];

$stmt = $conn->prepare("DELETE FROM exhibition_artworks WHERE exhibition_id = ? AND artwork_id = ?");
$stmt->bind_param("ii", $exhibition_id, $artwork_id);
$stmt->execute();

header("Location: manage_exhibition_artworks.php?exhibition_id=$exhibition_id");
exit;
