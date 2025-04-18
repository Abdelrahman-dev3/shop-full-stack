<?php
include "function.php";

if(empty($_GET['q'])){
    header('location:index.php');
    exit();
}

$results = search_about($_GET['q']);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="layout/css/search.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js.map">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
</head>
<body>
    <nav class="navbar">
        <div class="  ms-4 logo">Shop<span>Master</span></div>
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
<?php
if($results != null){
?>
    <div class="content">
    <section class="electronics">
        <div class="products">
            <?php
            foreach ($results as $item) {
                echo "<a style='text-decoration: none; color: black;' href='item.php?id=" . $item['item_id'] . "'>";
                echo "<div class='product'>";
                echo "<img src='https://picsum.photos/seed/custom-seed/300/200' alt='Random Image'>";
                echo "<h3>" . $item['name'] . "</h3>";
                echo "<p class='prt'>" . '$' . $item['price'] . "</p>";
                echo "<p><strong>Publisher:</strong> ".  getmember($item['member_id'])['full_name'] ."</p>";
                echo "<button>Add to Cart</button>";
                echo "</div>";
                echo "</a>";
            }
            ?>
            
        </div>
    </section>        
    </div>

<?php
}else{
?>
<div class="search-container">
    <div class="search-header">
        <h1>Search Results</h1>
        <p>nothing found</p>
        </div>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <p>No results found</p>
        </div>
</div>
    <?php
}
?>
</body>
</html>