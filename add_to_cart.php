<?php
session_start();
include "function.php";
include "includes/dbcon/pdo.php";

if (!isset($_SESSION['user_id'])) {
    header("location:login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id']; // item id for product
    $user_id = $_SESSION['user_id']; // user id for user

    // check if the product is in the cart
    $stmt = $dbcon->prepare("SELECT * FROM cart WHERE user_id = ? AND item_id = ?");
    $stmt->execute([$user_id, $item_id]);
    $existing_item = $stmt->fetch();

    if ($existing_item) {
        $_SESSION['success'] = "Product already in cart";
    } else {
        $stmt = $dbcon->prepare("INSERT INTO cart (user_id, item_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $item_id]);
        $_SESSION['success'] = "Product added to cart";
    }

    header("location:cart.php");
    exit();
} else {
    header("location:index.php");
    exit();
}
?> 