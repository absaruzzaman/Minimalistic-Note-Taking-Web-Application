<?php
session_start();
include('db.php');

$error = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .gradient-bg {
      background: linear-gradient(180deg, #00b7ffff 0%, #5c3079ff 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    @media (max-width: 640px) {
      .gradient-bg {
        padding: 40px;
      }
    }
  </style>
</head>
<body class="gradient-bg">
  <div class="w-full max-w-md bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 transform transition-transform duration-300 hover:scale-[1.01]">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Welcome Back</h2>
    
    <?php if (!empty($error)): ?>
      <p class="text-red-500 text-sm text-center mb-4 bg-red-50 py-2 px-4 rounded-lg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
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
      <button type="submit" name="login"
        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-2 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
        Login
      </button>
    </form>

    <p class="text-center text-gray-600 mt-6 text-sm">
      Don't have an account?
      <a href="signup.php" class="text-blue-600 hover:text-indigo-700 font-medium transition duration-200 hover:underline">Sign up</a>
    </p>
  </div>
</body>
</html>