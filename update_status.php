<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = $_GET['status'] ?? '';

if($id > 0 && in_array($status, ['active','pinned','archived'])){
    $stmt = $conn->prepare("UPDATE notes SET status=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sii", $status, $id, $user_id);
    $stmt->execute();
}

header("Location: index.php");
exit;
