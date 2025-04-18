<?php
session_start();
include "function.php";
include "includes/dbcon/pdo.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

$mail = new PHPMailer;

$status = $_GET['status'] ?? "signup";
if ($status == 'signup') {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up - ShopMaster</title>
        <link rel="stylesheet" href="layout/css/nav.css">
        <link rel="stylesheet" href="layout/css/signup.css">
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
        <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js">
        <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js.map">
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    </head>

    <body>
        <nav class="navbar">
            <div class="  ms-4 logo">Shop<span>Master</span></div>
                    <a style="color: white; text-decoration: none;margin-left: -185px;" href="cart.php" class="cart-icon">
                        <?php
            if (isset($_SESSION['user_id'])) {
                ?>
            <i class="fas fa-shopping-cart"></i>
            <?php
            $stmt = $dbcon->prepare("SELECT COUNT(*) as count FROM users WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $cart_count = $stmt->fetch()['count'];
            if ($cart_count > 0):
            ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif;
            }
            ?>
        </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <?php
                foreach (getcat() as $cat) {
                    echo " <li><a href='categories.php?id=" . $cat['id'] . "&catname=" .  $cat['name'] . "'>{$cat['name']}</a></li>";
                }
                ?>

            </ul>
            <div class="icons">
            <?php if (isset($_SESSION['user_id'])): ?>
            <button type="button" class="btn btn-outline-info" onclick="window.location.href='profile.php'">Profile</button>
            <button type="button" class="btn btn-outline-danger me-5 ms-2" onclick="window.location.href='logout2.php'">Logout</button>
        <?php else: ?>
            <button type="button" class="btn btn-outline-info" onclick="window.location.href='login.php'">Login</button>
            <button type="button" class="btn btn-outline-primary me-5 ms-2" onclick="window.location.href='signup.php'">Sign</button>
        <?php endif; ?>            </div>
        </nav>
        <div class="signup-container">
            <?php if (!empty($_SESSION['MSG_status'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['MSG_status'] as $msg): ?>
                            <li><?php echo $msg; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
            unset($_SESSION['MSG_status']);
            endif; ?>
            <form action="?status=process_signup" method="POST">
                <h1>Sign Up</h1>
                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder=" " pattern=".{2,}" title="Username Must Be Larger Than  2 Chars" required>
                    <label for="username">Username</label>
                </div>
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder=" " required>
                    <label for="email">Email Address</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Password</label>
                </div>
                <div class="form-group">
                    <input type="text" id="fullName" name="fullName" placeholder=" " required>
                    <label for="fullName">Full Name</label>
                </div>
                <button type="submit">Create Account</button>
            </form>
            <div class="login-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </body>

    </html>
<?php
} elseif ($status == "process_signup") {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim(htmlspecialchars(strip_tags($_POST['username']))) ?? "";
            $_SESSION['un'] = $username;
            if (empty($username)) {
                $_SESSION['MSG_status'][] = "Not Allow Send Empty Username";
            }
            if (strlen($username) <= 2 ) {
                $_SESSION['MSG_status'][] = "Username Must Be Larger Than 2 Chars";
            }
            if(isset($_POST['password'])){
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT) ?? "";
                if (empty($password)) {
                    $_SESSION['MSG_status'][] = "Not Allow Send Empty Password";
                }
            }
            # email
        $valid_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($valid_email) {
            $email = $valid_email;
        } else {
            $_SESSION['MSG_status'][] = "Invalid Email, Please Write Correct Email";
        }
        
        $fullname = trim(htmlspecialchars($_POST['fullName'])) ?? "";
        if (empty($fullname)) {
            $_SESSION['MSG_status'][] = "Not Allow Send Empty Full Name";
        }
        
        if (empty($_SESSION['MSG_status'])) {
            $stmt_check = $dbcon->prepare("SELECT `user_id` FROM `users` WHERE `username` = ?");
            $stmt_check->execute([$username]);        
            if ($stmt_check->rowCount() > 0) {
                $_SESSION['MSG_status'][] = "This Username Already Exists";
            }
            
            $stmt_check = $dbcon->prepare("SELECT `user_id` FROM `users` WHERE `email` = ?");
            $stmt_check->execute([$email]);        
            if ($stmt_check->rowCount() > 0) {
                $_SESSION['MSG_status'][] = "This email Already Exists";
            }
        }
        
        if (empty($_SESSION['MSG_status'])) {
            $verification_code = rand(100000, 999999);
            
            $stmt = $dbcon->prepare("INSERT INTO `users`(`username`, `password`, `email`, `full_name`, `verification_code`, `is_verified`) VALUES (?, ?, ?, ?, ?, 0)");
            $Add = $stmt->execute([$username, $password, $email, $fullname, $verification_code]);
            
            if ($Add) {
                echo $email;
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'abdoahmed2010201020@gmail.com';
                    $mail->Password = "xjlsuxotxdduohic";
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';

                    $mail->setFrom('abdoahmed2010201020@gmail.com', 'ShopMaster');
                    $mail->addAddress('abdoahmed2010201020@gmail.com');// $email
                    $mail->isHTML(true);
                    $mail->Subject = 'التحقق من البريد الإلكتروني';
                    $mail->Body = "مرحباً $fullname,<br>";
                    $mail->Body .= "شكراً لتسجيلك في ShopMaster. يرجى استخدام الكود التالي للتحقق من بريدك الإلكتروني:<br>";
                    $mail->Body .= "كود التحقق: $verification_code<br>";
                    $mail->Body .= "أو انقر على الرابط التالي:<br>";
                    $mail->Body .= "http://localhost/e-commerce/verify_email.php?status=verify&email=" . urlencode($email) . "&code=$verification_code<br>";
                    $mail->Body .= "مع تحيات فريق ShopMaster";

                    $mail->send();
                    
                    $_SESSION['MSG_status'] = ["تم إرسال كود التحقق إلى بريدك الإلكتروني"];
                    header("location:login.php");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['MSG_status'] = ["حدث خطأ أثناء إرسال البريد الإلكتروني: " . $e->getMessage()];
                    $_SESSION['MSG_status'] = ["حدث خطأ أثناء إرسال البريد الإلكتروني"];
                    header("location:signup.php?status=signup");
                    exit();
                }
            }
        }
        header("location:signup.php?status=signup");
        exit();
    } else {
        header("location:signup.php");
        exit();
    }
} else {
    header("location:signup.php");
    exit();
}
?>