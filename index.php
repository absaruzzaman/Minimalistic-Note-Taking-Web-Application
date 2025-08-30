<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$notes = $conn->query("SELECT * FROM notes WHERE user_id=$user_id ORDER BY created_at DESC");
?>
<h2>Welcome, <?= $_SESSION['username'] ?></h2>
<a href="add_note.php">Add Note</a> | 
<a href="profile.php">Profile</a> | 
<a href="logout.php">Logout</a>

<form method="GET" action="search.php">
    <input type="text" name="q" placeholder="Search notes...">
    <button type="submit">Search</button>
</form>
<hr>
<?php while($note = $notes->fetch_assoc()): ?>
    <h3><?= $note['title'] ?> (<?= $note['category'] ?>)</h3>
    <p><?= $note['content'] ?></p>
    <a href="edit_note.php?id=<?= $note['id'] ?>">Edit</a> |
    <a href="delete_note.php?id=<?= $note['id'] ?>" onclick="return confirm('Delete this note?')">Delete</a>
    <hr>
<?php endwhile; ?>
