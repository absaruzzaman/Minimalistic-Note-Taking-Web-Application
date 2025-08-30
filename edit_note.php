<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch note
$stmt = $conn->prepare("SELECT id, title, content, category FROM notes WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();
if(!$note){ header("Location: index.php"); exit; }

$error = "";
if (isset($_POST['update'])) {
  $title = trim($_POST['title']);
  $content = trim($_POST['content']);
  $category = trim($_POST['category']);
  if ($title === "") {
    $error = "Title is required.";
  } else {
    $upd = $conn->prepare("UPDATE notes SET title=?, content=?, category=? WHERE id=? AND user_id=?");
    $upd->bind_param("sssii", $title, $content, $category, $id, $user_id);
    $upd->execute();
    header("Location: index.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Note</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>.gradient-bg{background:linear-gradient(180deg,#00b7ffff 0%,#5c3079ff 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}</style>
</head>
<body class="gradient-bg">
  <div class="w-full max-w-2xl bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Edit Note</h2>
      <a href="index.php" class="text-sm px-3 py-1.5 bg-gray-900 text-white rounded-lg hover:bg-black">Back</a>
    </div>

    <?php if($error): ?>
      <p class="text-red-600 text-sm bg-red-50 px-4 py-2 rounded mb-3"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($note['title']) ?>" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none">
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Category (optional)</label>
        <input type="text" name="category" value="<?= htmlspecialchars($note['category']) ?>"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none">
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Content</label>
        <textarea name="content" rows="8"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none"><?= htmlspecialchars($note['content']) ?></textarea>
      </div>
      <button type="submit" name="update"
              class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition shadow">
        Update Note
      </button>
    </form>
  </div>
</body>
</html>
