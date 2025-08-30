<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id > 0){
    // First, check the current status
    $stmt = $conn->prepare("SELECT status FROM notes WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();

    if($note){
        if($note['status'] === 'archived'){
            // Permanently delete if already archived
            $stmt = $conn->prepare("DELETE FROM notes WHERE id=? AND user_id=?");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
        } else {
            // Move active/pinned note to archived
            $stmt = $conn->prepare("UPDATE notes SET status='archived' WHERE id=? AND user_id=?");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
        }
    }
}

header("Location: index.php");
exit;
