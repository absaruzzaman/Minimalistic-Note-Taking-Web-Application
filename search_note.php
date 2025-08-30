<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$q = isset($_GET['q']) ? trim($_GET['q']) : "";

$stmt = $conn->prepare("SELECT id, title, content, category, created_at FROM notes
                        WHERE user_id=? AND (title LIKE CONCAT('%', ?, '%') OR content LIKE CONCAT('%', ?, '%'))
                        ORDER BY created_at DESC");
$stmt->bind_param("iss", $user_id, $q, $q);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Search</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>.gradient-bg{background:linear-gradient(180deg,#00b7ffff 0%,#5c3079ff 100%);min-height:100vh}</style>
</head>
<body class="gradient-bg">
  <div class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white/90 rounded-2xl shadow p-6">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Results for “<?= htmlspecialchars($q) ?>”</h2>
        <a href="index.php" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-black">Back</a>
      </div>
      <div class="mt-4 space-y-4">
        <?php if($notes->num_rows===0): ?>
          <div class="text-gray-600">No matching notes.</div>
        <?php endif; ?>

        <?php while($n = $notes->fetch_assoc()): ?>
          <div class="p-4 rounded-xl bg-white border">
            <div class="flex items-start justify-between">
              <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($n['title']) ?></h3>
              <?php if(!empty($n['category'])): ?>
                <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700"><?= htmlspecialchars($n['category']) ?></span>
              <?php endif; ?>
            </div>
            <p class="mt-2 text-gray-700"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
            <div class="mt-2 text-xs text-gray-500">Created: <?= htmlspecialchars($n['created_at']) ?></div>
            <div class="mt-3 flex gap-2">
              <a href="edit_note.php?id=<?= (int)$n['id'] ?>" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Edit</a>
              <a href="delete_note.php?id=<?= (int)$n['id'] ?>" onclick="return confirm('Delete this note?')" class="px-3 py-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600">Delete</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</body>
</html>
