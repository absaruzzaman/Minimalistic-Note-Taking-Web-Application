<?php
session_start();
include('db.php');

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password);
    $stmt->fetch();

    if(password_verify($password, $hashed_password)){
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
    } else {
        echo "Invalid credentials.";
    }
}
?>
<h2>Login</h2>
<form method="POST">
    Email:<br><input type="email" name="email" required><br>
    Password:<br><input type="password" name="password" required><br>
    <button type="submit" name="login">Login</button>
</form>
<a href="signup.php">Don't have an account? Signup</a>
