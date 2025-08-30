<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$conn->query("DELETE FROM notes WHERE id=$id AND user_id=$user_id");
header("Location: index.php");
?>
