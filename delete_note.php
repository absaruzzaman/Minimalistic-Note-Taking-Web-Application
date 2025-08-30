<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("DELETE FROM notes WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

header("Location: index.php");
exit;
