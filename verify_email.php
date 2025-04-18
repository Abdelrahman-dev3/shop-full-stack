<?php
session_start();
include "function.php";
include "includes/dbcon/pdo.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mailer/autoload.php';

$status = $_GET['status'] ?? "verify";
$email = $_GET['email'] ?? "";
$code = $_GET['code'] ?? "";

if ($status == "verify") {
    // التحقق من صحة الكود
    $stmt = $dbcon->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ? AND is_verified = 0");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if ($user) {
        // تحديث حالة التحقق
        $stmt = $dbcon->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmt->execute([$email]);
        
        $_SESSION['MSG_status'] = ["تم التحقق من البريد الإلكتروني بنجاح"];
        header("location:login.php");
        exit();
    } else {
        $_SESSION['MSG_status'] = ["كود التحقق غير صحيح أو تم استخدامه مسبقاً"];
        header("location:login.php");
        exit();
    }
} else {
    header("location:index.php");
    exit();
}
?> 