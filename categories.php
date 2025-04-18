<?php
session_start();
include "includes/dbcon/pdo.php";
include "function.php";

$id = $_GET['id'] ?? 0;

$catname = $_GET['catname'] ?? "Home";

$category_id = $category_name = [];

foreach (getcat() as $cat) {
    $category_id[] = $cat['id'];
    $category_name[] = $cat['name'];
}

$category = array_combine($category_name, $category_id);

if(!in_array($catname, $category_name)){
    header("location: index.php");
    exit();
}


if($category[$catname] != $id){
    header("location: index.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="layout/css/categories.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js.map">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
    <title>categories</title>
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
        <?php endif; ?>
        </div>
    </nav>
    <section class="electronics">
        <h2><?php echo $_GET['catname'] ?? "Home" ;?></h2>
        <div class="products">
            <?php
            $rand = 0;
            foreach (getitems($id) as $item) {
                $rand++;
                echo "<a style='text-decoration: none; color: black;' href='item.php?id=" . $item['item_id'] . "'>";
                echo "<div class='product'>";
                echo "<img src='https://picsum.photos/300/200?random=" . $rand . "' alt='Random Image'>";
                echo "<h3>" . $item['name'] . "</h3>";
                echo "<p class='prt'>" . '$' . $item['price'] . "</p>";
                echo "<p><strong>Publisher:</strong> ".  getmember($item['member_id'])['full_name'] ."</p>";
                echo "<button>show more details</button>";
                echo "</div>";
                echo "</a>";
            }
            ?>
            
        </div>
    </section>
</body>
</html>





