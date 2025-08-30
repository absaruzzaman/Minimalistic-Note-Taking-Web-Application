<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, full_name FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$displayName = $user['full_name'] ?: $user['username']; // use full_name if exists, otherwise username


$category = isset($_GET['category']) ? trim($_GET['category']) : "";
$q = isset($_GET['q']) ? trim($_GET['q']) : "";

// Fetch distinct categories
$cats = [];
$resCats = $conn->query("SELECT DISTINCT category FROM notes WHERE user_id=$user_id");
while($row = $resCats->fetch_assoc()){ $cats[] = $row['category']; }

// Build notes query (category + optional search)
if ($q !== "") {
    $stmt = $conn->prepare("SELECT id, title, content, category, status, created_at, updated_at
                            FROM notes
                            WHERE user_id=? AND (? = '' OR category=?)
                              AND (title LIKE CONCAT('%', ?, '%') OR content LIKE CONCAT('%', ?, '%'))
                            ORDER BY FIELD(status,'pinned','active','archived'), created_at DESC");
    $stmt->bind_param("issss", $user_id, $category, $category, $q, $q);
} else {
    $stmt = $conn->prepare("SELECT id, title, content, category, status, created_at, updated_at
                            FROM notes
                            WHERE user_id=? AND (? = '' OR category=?)
                            ORDER BY FIELD(status,'pinned','active','archived'), created_at DESC");
    $stmt->bind_param("iss", $user_id, $category, $category);
}
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Notes</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { darkMode: 'class' }
</script>
<style>
  html { transition: background-color 0.3s, color 0.3s; }
</style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">

<!-- Top Bar -->
<header class="w-full bg-white/90 dark:bg-gray-800 backdrop-blur-sm shadow-md">
  <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600"></div>
      <h1 class="text-lg sm:text-xl font-bold">Dashboard</h1>
    </div>
    <nav class="flex items-center gap-3">
      <a href="profile.php" class="hover:text-indigo-500 transition">Profile</a>
      <button id="dark-toggle" class="px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">🌙</button>
      <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white transition">Logout</a>
    </nav>
  </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-6">
  <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-4">
    <h2 class="text-xl font-semibold">Welcome, <?= htmlspecialchars($displayName) ?></h2>
    <a href="add_note.php" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-md transition transform hover:-translate-y-0.5">
      + New Note
    </a>
  </div>

  <!-- Filters -->
  <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-white/90 dark:bg-gray-800/80 p-4 rounded-2xl shadow mb-6">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search title or content..."
      class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 outline-none focus:ring-2 focus:ring-blue-400 transition hover:ring-blue-300">
    <select name="category" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 outline-none focus:ring-2 focus:ring-blue-400 transition hover:ring-blue-300">
      <option value="">All Categories</option>
      <?php foreach($cats as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>" <?= ($category===$c)?'selected':''; ?>><?= htmlspecialchars($c) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="px-4 py-2 rounded-lg bg-gray-900 dark:bg-gray-600 text-white hover:bg-black dark:hover:bg-gray-700 transition">Apply</button>
  </form>

  <!-- Notes Grid -->
  <section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if($notes->num_rows === 0): ?>
      <div class="col-span-full bg-white/90 dark:bg-gray-800/80 p-8 rounded-2xl text-center text-gray-600 dark:text-gray-300">
        No notes found. Create your first one!
      </div>
    <?php endif; ?>

    <?php while($n = $notes->fetch_assoc()): ?>
  <article class="relative <?= ($n['status']==='pinned') ? 'border-2 border-yellow-400 dark:border-yellow-500' : '' ?> 
                        bg-white/90 dark:bg-gray-800/80 rounded-2xl shadow p-5 flex flex-col transform transition-transform duration-200 hover:scale-[1.02] hover:shadow-lg">
    
    <?php if($n['status']==='pinned'): ?>
      <span class="absolute top-2 right-2 text-xs font-bold px-2 py-1 rounded-full bg-yellow-300 dark:bg-yellow-500 text-gray-800 dark:text-gray-900">PINNED</span>
    <?php endif; ?>
    
    <div class="flex items-start justify-between gap-2">
      <h3 class="text-lg font-semibold"><?= htmlspecialchars($n['title']) ?></h3>
      <?php if(!empty($n['category'])): ?>
        <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-700 text-indigo-700 dark:text-indigo-100"><?= htmlspecialchars($n['category']) ?></span>
      <?php endif; ?>
    </div>
    <p class="mt-2 line-clamp-4"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
    <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
      Created: <?= htmlspecialchars($n['created_at']) ?><?php if($n['updated_at'] && $n['updated_at'] !== $n['created_at']): ?> • Updated: <?= htmlspecialchars($n['updated_at']) ?><?php endif; ?>
    </div>

    <!-- Status Buttons -->
    <div class="mt-4 flex flex-wrap gap-2">
      <!-- Pin / Unpin -->
      <a href="update_status.php?id=<?= (int)$n['id'] ?>&status=<?= ($n['status']==='pinned') ? 'active' : 'pinned' ?>"
         class="px-2 py-1 rounded-lg text-xs font-medium transition
                <?= ($n['status']==='pinned') ? 'bg-yellow-300 dark:bg-yellow-500 text-gray-800 dark:text-gray-100' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600' ?>">
        <?= ($n['status']==='pinned') ? 'Unpin' : 'Pin' ?>
      </a>

      <!-- Archive / Restore -->
      <?php if($n['status']!=='archived'): ?>
        <a href="update_status.php?id=<?= (int)$n['id'] ?>&status=archived"
           class="px-2 py-1 rounded-lg text-xs font-medium bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
          Archive
        </a>
      <?php else: ?>
        <a href="update_status.php?id=<?= (int)$n['id'] ?>&status=active"
           class="px-2 py-1 rounded-lg text-xs font-medium bg-green-200 dark:bg-green-600 text-gray-800 dark:text-gray-100 hover:bg-green-300 dark:hover:bg-green-500 transition">
          Restore
        </a>
      <?php endif; ?>
    </div>

    <!-- Edit/Delete Buttons -->
    <div class="mt-4 flex items-center gap-2">
      <a href="edit_note.php?id=<?= (int)$n['id'] ?>" class="flex-1 text-center px-3 py-2 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition transform hover:-translate-y-0.5">Edit</a>
      <a href="delete_note.php?id=<?= (int)$n['id'] ?>" onclick="return confirm('Delete this note?')" class="flex-1 text-center px-3 py-2 rounded-lg bg-red-500 dark:bg-red-600 text-white hover:bg-red-600 dark:hover:bg-red-700 transition transform hover:-translate-y-0.5">Delete</a>
    </div>

  </article>
<?php endwhile; ?>

  </section>
</main>

<script>
const toggle = document.getElementById('dark-toggle');

function updateIcon() {
    if(document.documentElement.classList.contains('dark')){
        toggle.textContent = '☀️'; // sun for dark mode
    } else {
        toggle.textContent = '🌙'; // moon for light mode
    }
}

toggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    if(document.documentElement.classList.contains('dark')){
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
    updateIcon();
});

// Apply saved theme and update icon on page load
if(localStorage.getItem('theme') === 'dark'){
    document.documentElement.classList.add('dark');
}
updateIcon();
</script>
</body>
</html>
