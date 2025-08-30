<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM notes WHERE user_id=? AND status='archived' ORDER BY updated_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trash</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<h2 class="text-2xl font-bold mb-4">Trash / Archived Notes</h2>

<section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
<?php if($notes->num_rows === 0): ?>
  <div class="col-span-full bg-white p-6 rounded-xl text-center text-gray-600">
    Trash is empty.
  </div>
<?php endif; ?>

<?php while($n = $notes->fetch_assoc()): ?>
<div class="bg-white p-5 rounded-xl shadow flex flex-col">
  <h3 class="text-lg font-semibold"><?= htmlspecialchars($n['title']) ?></h3>
  <p class="mt-2 text-gray-700 line-clamp-4"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
  <div class="mt-4 flex gap-2 flex-wrap">
    <a href="update_status.php?id=<?= $n['id'] ?>&status=active"
       class="px-3 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition">Restore</a>
    <a href="delete_note.php?id=<?= $n['id'] ?>"
       onclick="return confirm('Delete permanently?')"
       class="px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition">Delete</a>
  </div>
</div>
<?php endwhile; ?>
</section>

</body>
</html>
