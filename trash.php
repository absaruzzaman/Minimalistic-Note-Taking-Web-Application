<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Fetch archived notes
$stmt = $conn->prepare("SELECT id, title, content, category, created_at, updated_at FROM notes WHERE user_id=? AND status='archived' ORDER BY updated_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notes = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Trash</title>
<script src="https://cdn.tailwindcss.com"></script>
<script> tailwind.config = { darkMode: 'class' } </script>
<style> html { transition: background-color 0.3s, color 0.3s; } </style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">

<header class="w-full bg-white/90 dark:bg-gray-800 backdrop-blur-sm shadow-md">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600"></div>
            <h1 class="text-lg sm:text-xl font-bold">Trash</h1>
        </div>
        <nav class="flex items-center gap-3">
            <a href="index.php" class="hover:text-indigo-500">Dashboard</a>
            <button id="dark-toggle" class="px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">🌙</button>
            <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-6">
    <h2 class="text-xl font-semibold mb-4">Archived Notes</h2>

    <section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if($notes->num_rows === 0): ?>
            <div class="col-span-full bg-white/90 dark:bg-gray-800/80 p-8 rounded-2xl text-center text-gray-600 dark:text-gray-300">
                No archived notes.
            </div>
        <?php endif; ?>

        <?php while($n = $notes->fetch_assoc()): ?>
            <article class="bg-white/90 dark:bg-gray-800/80 rounded-2xl shadow p-5 flex flex-col hover:scale-[1.02] transition-transform">
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
                <div class="mt-4 flex items-center gap-2">
                    <a href="update_status.php?id=<?= (int)$n['id'] ?>&status=active" class="flex-1 text-center px-3 py-2 rounded-lg bg-green-500 dark:bg-green-600 text-white hover:bg-green-600 dark:hover:bg-green-500 transition">Restore</a>
                    <a href="delete_note.php?id=<?= (int)$n['id'] ?>" onclick="return confirm('Delete permanently?')" class="flex-1 text-center px-3 py-2 rounded-lg bg-red-500 dark:bg-red-600 text-white hover:bg-red-600 dark:hover:bg-red-700 transition">Delete</a>
                </div>
            </article>
        <?php endwhile; ?>
    </section>
</main>

<script>
const toggle = document.getElementById('dark-toggle');
function updateIcon() {
    if(document.documentElement.classList.contains('dark')){
        toggle.textContent = '☀️';
    } else {
        toggle.textContent = '🌙';
    }
}
toggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    updateIcon();
});
if(localStorage.getItem('theme') === 'dark'){ document.documentElement.classList.add('dark'); }
updateIcon();
</script>

</body>
</html>
