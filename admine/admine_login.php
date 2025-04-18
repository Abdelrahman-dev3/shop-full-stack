<?php
session_start();
include "../includes/dbcon/pdo.php";
$error_msg = $id = $username = $password = '';
if (isset($_SESSION['username'])) {
        header("location:dashboard.php");
        exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['username'])) {
        $error_msg = "Not Allowed Send Empty Username" . "<br>";
    }else{
        $username = trim($_POST['username']) ;
    }
    if (empty($_POST['password'])) {
        $error_msg = "Not Allowed Send Empty password" . "<br>";
    }else{
        $password = trim($_POST['password']);
    }

    if(isset($_POST['username']) && isset($_POST['password']) ){

        if (empty($error_msg)) {
            $stmt = $dbcon->prepare("SELECT `user_id` , `username` , `password` FROM `users` WHERE `username` = ? AND `group_id` = 1");
            $stmt->execute([$username]);
            $row = $stmt->fetch();
            if(!$row){
                $error_msg = "This User Is Not Found";
            }else{
                if (password_verify($password, $row['password'])) {
                    $_SESSION['username'] = $username ; 
                    $_SESSION['id'] = $row['user_id']; 
                    header("location:dashboard.php");
                    exit();
                } else {
                    $error_msg = "Incorrect password";
                }
            }
        }

    }

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../layout/css/styles.css">
</head>
<body>
    <div class="login-container">
        <form class="login-form" action="" method="POST">
            <h2>Admin Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" require>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" require>
            </div>
            <button type="submit" class="login-btn">Login</button>
            <p class="error_msg"><?php echo $error_msg; ?></p>
        </form>
    </div>
</body>
</html>