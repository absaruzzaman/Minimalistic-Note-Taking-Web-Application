<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$note = $conn->query("SELECT * FROM notes WHERE id=$id AND user_id=$user_id")->fetch_assoc();

if(isset($_POST['update'])){
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE notes SET title=?, content=?, category=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sssii", $title, $content, $category, $id, $user_id);
    $stmt->execute();
    header("Location: index.php");
}
?>
<h2>Edit Note</h2>
<form method="POST">
    Title:<br><input type="text" name="title" value="<?= $note['title'] ?>" required><br>
    Content:<br><textarea name="content"><?= $note['content'] ?></textarea><br>
    Category:<br><input type="text" name="category" value="<?= $note['category'] ?>"><br>
    <button type="submit" name="update">Update Note</button>
</form>
<a href="index.php">Back to Notes</a>
