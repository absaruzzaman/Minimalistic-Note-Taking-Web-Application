<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT username, full_name, email, created_at FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle update
$message = "";
if(isset($_POST['update_profile'])){
    $new_username = $_POST['username'];
    $new_full_name = $_POST['full_name'];
    $stmt = $conn->prepare("UPDATE users SET username=?, full_name=? WHERE id=?");
    $stmt->bind_param("ssi", $new_username, $new_full_name, $user_id);
    if($stmt->execute()){
        $message = "Profile updated successfully!";
        $user['username'] = $new_username;
        $user['full_name'] = $new_full_name;
        $_SESSION['username'] = $new_username;
    } else {
        $message = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' }</script>
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
            <h1 class="text-lg sm:text-xl font-bold">Profile</h1>
        </div>
        <nav class="flex items-center gap-3">
            <a href="index.php" class="hover:text-indigo-500">Dashboard</a>
            <button id="dark-toggle" class="px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">🌙</button>
            <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white">Logout</a>
        </nav>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 py-6">
    <?php if($message): ?>
        <p class="mb-4 p-3 rounded-lg bg-green-100 dark:bg-green-700 text-green-800 dark:text-green-100"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="bg-white/90 dark:bg-gray-800/80 p-6 rounded-2xl shadow-md">
        <h2 class="text-xl font-semibold mb-4">Personal Info</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 dark:text-gray-200 font-medium mb-1">Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-200 font-medium mb-1">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-400 outline-none transition">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-200 font-medium mb-1">Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-200 font-medium mb-1">Member Since</label>
                <input type="text" value="<?= htmlspecialchars(date('M d, Y', strtotime($user['created_at']))) ?>" disabled
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 cursor-not-allowed">
            </div>
            <button type="submit" name="update_profile" class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-md transition">Save Changes</button>
        </form>
    </div>
</main>

<script>
const toggle = document.getElementById('dark-toggle');
function updateIcon() {
    toggle.textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
}
toggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    updateIcon();
});
// Load saved theme
if(localStorage.getItem('theme')==='dark'){ document.documentElement.classList.add('dark'); }
updateIcon();
</script>
</body>
</html>
