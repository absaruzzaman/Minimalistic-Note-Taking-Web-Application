<?php
session_start();
include('db.php');

if(isset($_POST['signup'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if($stmt->execute()){
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<h2>Signup</h2>
<form method="POST">
    Username:<br><input type="text" name="username" required><br>
    Email:<br><input type="email" name="email" required><br>
    Password:<br><input type="password" name="password" required><br>
    <button type="submit" name="signup">Signup</button>
</form>
<a href="login.php">Already have an account? Login</a>
