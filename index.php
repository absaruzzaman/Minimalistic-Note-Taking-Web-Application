<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

$category = isset($_GET['category']) ? trim($_GET['category']) : "";
$q = isset($_GET['q']) ? trim($_GET['q']) : "";

// Fetch distinct categories
$cats = [];
$stmtCats = $conn->prepare("SELECT DISTINCT category FROM notes WHERE user_id=? AND category <> '' ORDER BY category");
$stmtCats->bind_param("i", $user_id);
$stmtCats->execute();
$resCats = $stmtCats->get_result();
while($row = $resCats->fetch_assoc()){ $cats[] = $row['category']; }

// Build notes query (category + optional search)
if ($q !== "") {
  $stmt = $conn->prepare("SELECT id, title, content, category, created_at, updated_at
                          FROM notes
                          WHERE user_id=? AND (? = '' OR category=?)
                            AND (title LIKE CONCAT('%', ?, '%') OR content LIKE CONCAT('%', ?, '%'))
                          ORDER BY created_at DESC");
  $stmt->bind_param("issss", $user_id, $category, $category, $q, $q);
} else {
  $stmt = $conn->prepare("SELECT id, title, content, category, created_at, updated_at
                          FROM notes
                          WHERE user_id=? AND (? = '' OR category=?)
                          ORDER BY created_at DESC");
  $stmt->bind_param("iss", $user_id, $category, $category);
}
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Notes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .gradient-bg{background:linear-gradient(180deg,#00b7ffff 0%,#5c3079ff 100%);min-height:100vh}
  </style>
</head>
<body class="gradient-bg">
  <!-- Top Bar -->
  <header class="w-full bg-white/90 backdrop-blur-sm shadow-md">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        <h1 class="text-lg sm:text-xl font-bold text-gray-800">Notes Dashboard</h1>
      </div>
      <nav class="flex items-center gap-3">
        <a href="profile.php" class="text-gray-700 hover:text-indigo-700 font-medium">Profile</a>
        <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600 transition">Logout</a>
      </nav>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-4">
      <h2 class="text-white/90 text-xl font-semibold">Welcome, <?= htmlspecialchars($username) ?></h2>
      <a href="add_note.php" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-md">
        + New Note
      </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-white/90 backdrop-blur-sm p-4 rounded-2xl shadow">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search title or content..."
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none">
      <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none">
        <option value="">All Categories</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= ($category===$c)?'selected':''; ?>><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-black transition">Apply</button>
    </form>

    <!-- Notes Grid -->
    <section class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php if($notes->num_rows === 0): ?>
        <div class="col-span-full bg-white/90 p-8 rounded-2xl text-center text-gray-600">
          No notes found. Create your first one!
        </div>
      <?php endif; ?>

      <?php while($n = $notes->fetch_assoc()): ?>
        <article class="bg-white/90 backdrop-blur-sm rounded-2xl shadow p-5 flex flex-col">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($n['title']) ?></h3>
            <?php if(!empty($n['category'])): ?>
              <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700"><?= htmlspecialchars($n['category']) ?></span>
            <?php endif; ?>
          </div>
          <p class="mt-2 text-gray-700 line-clamp-4"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
          <div class="mt-4 text-xs text-gray-500">
            Created: <?= htmlspecialchars($n['created_at']) ?><?php if($n['updated_at'] && $n['updated_at'] !== $n['created_at']): ?> • Updated: <?= htmlspecialchars($n['updated_at']) ?><?php endif; ?>
          </div>
          <div class="mt-4 flex items-center gap-2">
            <a href="edit_note.php?id=<?= (int)$n['id'] ?>"
               class="flex-1 text-center px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Edit</a>
            <a href="delete_note.php?id=<?= (int)$n['id'] ?>"
               onclick="return confirm('Delete this note?')"
               class="flex-1 text-center px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition">Delete</a>
          </div>
        </article>
      <?php endwhile; ?>
    </section>
  </main>
</body>
</html>
