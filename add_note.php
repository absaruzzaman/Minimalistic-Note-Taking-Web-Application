<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if(isset($_POST['add'])){
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $content, $category);
    $stmt->execute();
    header("Location: index.php");
}
?>
<h2>Add Note</h2>
<form method="POST">
    Title:<br><input type="text" name="title" required><br>
    Content:<br><textarea name="content"></textarea><br>
    Category:<br><input type="text" name="category"><br>
    <button type="submit" name="add">Add Note</button>
</form>
<a href="index.php">Back to Notes</a>
