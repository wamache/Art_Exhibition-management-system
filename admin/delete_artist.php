<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied");
}
?>
<h2>Admin Panel</h2>
<p>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</p>
