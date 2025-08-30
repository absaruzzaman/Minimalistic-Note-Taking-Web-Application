<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user info (optional, for removing old picture)
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Process text fields
$username  = $_POST['username'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email     = $_POST['email'] ?? '';
$bio       = $_POST['bio'] ?? '';

$profile_pic_name = $user['profile_pic']; // keep old if no new

// Check if remove picture button was clicked
if(isset($_POST['remove_pic'])){
    if($user['profile_pic'] && file_exists('uploads/'.$user['profile_pic'])){
        unlink('uploads/'.$user['profile_pic']);
    }
    $profile_pic_name = NULL; // reset variable
}

// Process profile picture upload
if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName    = $_FILES['profile_pic']['name'];
    $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg','jpeg','png','gif'];

    if(in_array($fileExt, $allowedExts)){
        $newFileName = 'user_'.$user_id.'_'.time().'.'.$fileExt;
        $destPath = 'uploads/'.$newFileName;
        if(move_uploaded_file($fileTmpPath, $destPath)){
            // Delete old picture
            if($user['profile_pic'] && file_exists('uploads/'.$user['profile_pic'])){
                unlink('uploads/'.$user['profile_pic']);
            }
            $profile_pic_name = $newFileName;
        }
    }
}

// Update user info in database
$stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, email=?, bio=?, profile_pic=? WHERE id=?");
$stmt->bind_param("sssssi", $username, $full_name, $email, $bio, $profile_pic_name, $user_id);
$stmt->execute();

header("Location: profile.php");
exit;
