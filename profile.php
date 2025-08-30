<?php
session_start();
include('db.php');
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$stmtU = $conn->prepare("SELECT username, email, created_at FROM users WHERE id=?");
$stmtU->bind_param("i",$user_id);
$stmtU->execute();
$user = $stmtU->get_result()->fetch_assoc();

$stmtC = $conn->prepare("SELECT COUNT(*) AS total FROM notes WHERE user_id=?");
$stmtC->bind_param("i",$user_id);
$stmtC->execute();
$total = $stmtC->get_result()->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>.gradient-bg{background:linear-gradient(180deg,#00b7ffff 0%,#5c3079ff 100%);min-height:100vh}</style>
</head>
<body class="gradient-bg">
  <div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8">
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Your Profile</h2>
        <div class="flex gap-2">
          <a href="index.php" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white hover:bg-black">Back</a>
          <a href="logout.php" class="px-3 py-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600">Logout</a>
        </div>
      </div>

      <div class="mt-6 grid sm:grid-cols-2 gap-4">
        <div class="p-4 rounded-xl border bg-white">
          <div class="text-sm text-gray-500">Username</div>
          <div class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['username']) ?></div>
        </div>
        <div class="p-4 rounded-xl border bg-white">
          <div class="text-sm text-gray-500">Email</div>
          <div class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        <div class="p-4 rounded-xl border bg-white">
          <div class="text-sm text-gray-500">Joined</div>
          <div class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($user['created_at']) ?></div>
        </div>
        <div class="p-4 rounded-xl border bg-white">
          <div class="text-sm text-gray-500">Total Notes</div>
          <div class="text-lg font-semibold text-gray-800"><?= (int)$total ?></div>
        </div>
      </div>

      <div class="mt-6 text-sm text-gray-600">
        Tip: keep categories consistent (e.g., <em>Work</em>, <em>Study</em>, <em>Personal</em>) to filter faster.
      </div>
    </div>
  </div>
</body>
</html>
