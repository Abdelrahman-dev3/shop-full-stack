<?php
session_start();
include "function.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['item_id']) || !isset($_POST['comment'])) {
    header("location: index.php");
    exit();
}

$item_id = intval($_POST['item_id']);
$user_id = $_SESSION['user_id'];
$comment = trim(strip_tags(htmlspecialchars($_POST['comment'])));

if (empty($comment)) {
    $_SESSION['error'] = "التعليق لا يمكن أن يكون فارغاً";
    header("location: item.php?id=" . $item_id);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO comments (item_id, user_id, comment, comment_date) VALUES (?, ?, ?, NOW())");
    if ($stmt->execute([$item_id, $user_id, $comment])) {
        $_SESSION['success'] = "تم إضافة تعليقك بنجاح";
    } else {
        $_SESSION['error'] = "حدث خطأ أثناء إضافة التعليق";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "خطأ في قاعدة البيانات: " . $e->getMessage();
}

header("location: item.php?id=" . $item_id);
exit(); 