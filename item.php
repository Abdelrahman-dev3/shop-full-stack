<?php
session_start();
 // default timezone
date_default_timezone_set('Africa/Cairo');

include "function.php";
 // check if id is set in url
if(!isset($_GET['id'])){
    header("location:index.php");
    exit();
}

$item_id = $_GET['id'] ?? 0;

if (!checkIDExists($item_id)) {
    header("location:index.php");
    exit();
}

$comment = $error = '';

// get item data
$item = get_item($item_id);
// get member data
$member = get_member($item['member_id']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])){
    $bad_words = ["زفت", "خرا", "قحبة", "عرص", "كلب", "ديوث", "شرموطة", "عرصة", "زب", "نياك", "كسمك","مخنث", "لوطي", "قواد", "حيوان", "خنزير", "قليل الأدب", "قذر", "حقير", "واطي","الدين","fuck", "shit", "bitch", "bastard", "asshole", "cunt", "dick", "cock","motherfucker", "slut", "whore", "faggot", "wanker", "twat", "prick",  "FUCK", "SHIT", "BITCH", "BASTARD", "ASSHOLE", "CUNT", "DICK", "COCK", "MOTHERFUCKER", 
    "SLUT", "WHORE", "FAGGOT", "WANKER", "TWAT", "PRICK"];
    
    $comment = trim(strip_tags(htmlspecialchars($_POST['comment'])));

    if(empty($comment)){
        $error = "You must write a comment to add it";
    }elseif(in_array($comment, $bad_words)){
        $error = "We do not allow such comments to be posted";
    }else{

if (isset($_SESSION['username'])) {
        $comment_user = $_SESSION['user_id'];
        $comment_item = $item_id;
        $stmt = $dbcon->prepare("INSERT INTO comments (comment, user_id, item_id) VALUES (?, ?, ?)");
        $stmt->execute([$comment, $comment_user, $comment_item]);
        header("Location:item.php?id=" . $item_id . "#comments-title" );
        exit();
    }else{
        $error = "You must be logged in to add a comment";
    }
  } // end if in_array
} // end request method

// get comments
$comments = get_comments($item_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $item['name'] ?> - ShopMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="layout/css/item.css">
    <link rel="stylesheet" href="layout/css/profile.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
</head>
<body>
<nav class="navbar">
    <div class="ms-4 logo">Shop<span>Master</span></div>
    <a style="color: white; text-decoration: none;margin-left: -185px;" href="cart.php" class="cart-icon">
            <?php
            if (isset($_SESSION['user_id'])) {
                ?>
            <i class="fas fa-shopping-cart"></i>
            <?php
            $stmt = $dbcon->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
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
        <!-- item container -->
<div class="item-container">
    <div class="item-details">
        <div class="item-images">
            <div class="main-image">
                <img src="uploudes/images/<?php echo $item['image'] ?>">
            </div>
        </div>
        <!-- item info -->
        <div class="item-info">
            <h1><?php echo $item['name'] ?></h1>
            <div style="color: green;" class="price"><?php echo '$' . $item['price'] ?></div>

            <div class="item-meta">
                <span class="status">
                    <i class="fas fa-tag"></i> <?php echo $item['status'] ?>
                </span>
                <span class="country">
                    <i class="fas fa-globe"></i> <?php echo ucfirst($item['country']) ?>
                </span>
                <span class="date">
                    <i class="fas fa-calendar"></i> <?php echo $item['add_date'] ?>
                </span>
            </div>

            <div class="description">
                <h2>Description</h2>
                <p><?php echo $item['description'] ?></p>
            </div>
            <!-- seller info -->
            <div class="seller-info">
                <h2>Seller Info</h2>
                <div class="seller-card">
                    <div class="seller-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="seller-details">
                        <h3><?php echo $member['full_name'] ?></h3>
                        <p>Member since <?php echo $member['date'] ?></p>
                        <p>Sold 0 Products</p>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                    <button type="submit" class="btn btn-primary add-to-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary add-to-cart-btn">
                    <i class="fas fa-sign-in-alt"></i>
                     Login to buy
                </a>
            <?php endif; ?>
        </div>
    </div>

        <!-- comments section -->
    <div class="comments-section">
        <h2 id="comments-title">Comments</h2>
        <?php if( $error != ''): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
            <form action="" name="add_comment" method="POST" class="comment-form">
                <textarea name="comment" placeholder="Write your comment here..." required></textarea>
                <input class="btn btn-primary" style="margin-top: 10px; padding: 10px 20px;" type="submit" name="add_comment" value="Send Comment">
            </form>
        

    <div class="comments-list">
        <?php if (empty($comments)): ?>
            <p class="no-comments">No comments yet</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <div class="comment-user">
                            <i class="fas fa-user"></i>
                            <span><?php echo $comment['username']; ?></span>
                        </div>
                        <span class="comment-date">
                            <?php echo timeAgo($comment['comment_date']); ?>
                        </span>
                    </div>
                    <p class="comment-content"><?php echo nl2br($comment['comment']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

    <!-- similar items -->
    <div class="similar-items">
        <h2>Similar Items</h2>
        <?php
        $products_about = search_about($item['name']);
        if(empty($products_about)){
    ?>
        <div class="no-products">
            <i class="fas fa-box-open"></i>
            <p>There are no similar items</p>
        </div>
        <?php
        }else{
    ?>
        <div class="products-grid">
            <?php
            foreach($products_about as $product_about){
            ?>
            <a style="text-decoration: none; color: black;" href="item.php?id=<?php echo $product_about['item_id'] ?>">
                <div class="product-card">
                    <div class="product-image">
                        <img src="uploudes/images/<?php echo $product_about['image'] ?>">
                    </div>
                    <div class="product-info">
                        <h4><?php echo $product_about['name'] ?></h4>
                        <p class="description"><?php echo $product_about['description'] ?></p>
                        <div class="product-details">
                            <span class="price"><?php echo '$' . $product_about['price'] ?></span>
                            <span class="country"><i class="fas fa-globe"></i><?php echo $product_about['country'] ?></span>
                            <span class="date"><i class="fas fa-calendar"></i><?php echo $product_about['add_date'] ?></span>
                        </div>
                    </div>
                </div>
            </a>
            <?php
            }
            ?>
        </div>
        <div class="add-product-btn text-center">
            <a href="add_product.php" class="btn mt-3 btn-primary">Add Product</a>
        </div>
        <?php
        }
    ?>
</div>
</div>
</body>
</html> 