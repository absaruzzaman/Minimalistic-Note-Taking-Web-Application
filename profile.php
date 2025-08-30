<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$notes_count = $conn->query("SELECT COUNT(*) as total FROM notes WHERE user_id=$user_id")->fetch_assoc()['total'];
?>
<h2>Profile</h2>
<p>Username: <?= $user['username'] ?></p>
<p>Email: <?= $user['email'] ?></p>
<p>Joined: <?= $user['created_at'] ?></p>
<p>Total Notes: <?= $notes_count ?></p>
<a href="index.php">Back to Notes</a>
