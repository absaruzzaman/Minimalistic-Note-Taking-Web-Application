<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['signup'])) {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (strlen($username) < 3) {
    $error = "Username must be at least 3 characters.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Enter a valid email.";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters.";
  } else {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed);
    if ($stmt->execute()) {
      $_SESSION['user_id'] = $stmt->insert_id;
      $_SESSION['username'] = $username;
      header("Location: index.php");
      exit;
    } else {
      $error = (strpos($stmt->error, 'Duplicate') !== false) ? "Email or username already exists." : "Signup failed.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .gradient-bg{background:linear-gradient(180deg,#00b7ffff 0%,#5c3079ff 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
    @media (max-width:640px){.gradient-bg{padding:40px}}
  </style>
</head>
<body class="gradient-bg">
  <div class="w-full max-w-md bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 transform transition-transform duration-300 hover:scale-[1.01]">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create Account</h2>

    <?php if (!empty($error)): ?>
      <p class="text-red-500 text-sm text-center mb-4 bg-red-50 py-2 px-4 rounded-lg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-medium mb-1">Username</label>
        <input type="text" name="username" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200 focus:outline-none">
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" name="email" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200 focus:outline-none">
      </div>
      <div>
        <label class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" name="password" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200 focus:outline-none">
      </div>
      <button type="submit" name="signup"
        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
        Sign Up
      </button>
    </form>

    <p class="text-center text-gray-600 mt-6 text-sm">
      Already have an account?
      <a href="login.php" class="text-blue-600 hover:text-indigo-700 font-medium transition duration-200 hover:underline">Log in</a>
    </p>
  </div>
</body>
</html>
