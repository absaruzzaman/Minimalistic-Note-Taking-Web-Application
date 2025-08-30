<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, full_name, email, bio, profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
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

<header class="w-full bg-white/90 dark:bg-gray-800 backdrop-blur-sm shadow-md">
  <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
    <h1 class="text-lg sm:text-xl font-bold">Profile</h1>
    <nav class="flex items-center gap-3">
      <a href="index.php" class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
          Dashboard
      </a>
      <button id="dark-toggle" class="px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">🌙</button>
      <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white">Logout</a>
    </nav>
  </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-6">
  <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-6">

    <!-- Profile Picture -->
    <div class="flex flex-col items-center">
      <label class="block font-medium mb-1">Profile Picture</label>
      <div class="relative w-32 h-32 mb-2">
        <?php if($user['profile_pic'] && file_exists('uploads/'.$user['profile_pic'])): ?>
            <img id="profile-preview" src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" 
                 alt="Profile Picture" 
                 class="w-full h-full rounded-full object-cover border-2 border-gray-300 dark:border-gray-600 shadow-md transition-transform duration-300 hover:scale-110 cursor-pointer">
            <button type="submit" name="remove_pic"
    class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 rounded-full hover:bg-red-600 text-xs">
    ✕
</button>
        <?php else: ?>
            <div class="w-full h-full rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500">
              No Image
            </div>
        <?php endif; ?>
      </div>
      <input type="file" name="profile_pic" accept="image/*" id="profile-input" class="block text-gray-700 dark:text-gray-200">
      <p class="text-xs text-gray-500 dark:text-gray-400">Click image to zoom or select a new one</p>
    </div>

    <!-- Personal Info -->
    <div class="flex flex-col gap-4">
      <div>
        <label class="block font-medium mb-1">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 outline-none">
      </div>
      <div>
        <label class="block font-medium mb-1">Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 outline-none">
      </div>
      <div>
        <label class="block font-medium mb-1">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 outline-none">
      </div>
      <div>
        <label class="block font-medium mb-1">Bio</label>
        <textarea name="bio" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 outline-none"><?= htmlspecialchars($user['bio']) ?></textarea>
      </div>

      <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition shadow-md">Save Changes</button>
    </div>

  </form>
</main>

<script>
const toggle = document.getElementById('dark-toggle');
const preview = document.getElementById('profile-preview');
const input = document.getElementById('profile-input');

function updateIcon() {
    toggle.textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
}

toggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    updateIcon();
});

// Apply saved theme and update icon on page load
if(localStorage.getItem('theme') === 'dark'){ document.documentElement.classList.add('dark'); }
updateIcon();

// Click image to zoom
if(preview){
    preview.addEventListener('click', () => {
        const imgWindow = window.open('', '_blank');
        imgWindow.document.write(`<img src="${preview.src}" style="width:100%; height:auto;">`);
    });
}

// Live preview on upload
if(input){
    input.addEventListener('change', function(){
        if(this.files && this.files[0]){
            const reader = new FileReader();
            reader.onload = function(e){
                if(preview){ preview.src = e.target.result; }
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
}
</script>

</body>
</html>
