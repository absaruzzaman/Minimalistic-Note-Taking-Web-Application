<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$q = $_GET['q'];

$notes = $conn->query("SELECT * FROM notes WHERE user_id=$user_id AND (title LIKE '%$q%' OR content LIKE '%$q%')");
?>
<h2>Search Results for "<?= htmlspecialchars($q) ?>"</h2>
<a href="index.php">Back</a>
<hr>
<?php while($note = $notes->fetch_assoc()): ?>
    <h3><?= $note['title'] ?> (<?= $note['category'] ?>)</h3>
    <p><?= $note['content'] ?></p>
    <a href="edit_note.php?id=<?= $note['id'] ?>">Edit</a> |
    <a href="delete_note.php?id=<?= $note['id'] ?>" onclick="return confirm('Delete this note?')">Delete</a>
    <hr>
<?php endwhile; ?>
