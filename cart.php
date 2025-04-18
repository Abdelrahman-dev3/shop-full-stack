<?php
session_start();
include "function.php";
include "includes/dbcon/pdo.php";

$stmt = $dbcon->prepare("SELECT
                            `cart`.`item_id`
                            ,`image`
                            ,`country`
                            , `price`
                            , `name`
                        FROM
                            `cart`
                        INNER JOIN
                            items ON cart.item_id = items.item_id
                        WHERE
                            cart.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();




if(isset($_GET['remove'])){
    remove_item($_SESSION['user_id'] , $_GET['remove']);
    header('Location: cart.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - ShopMaster</title>
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js.map">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="layout/css/cart.css">
</head>
<body>
<nav class="navbar">
        <div class="ms-4 logo">Shop<span>Master</span></div>
        <a style="color: white; text-decoration: none;margin-left: -185px;" href="cart.php" class="cart-icon">
            <i class="fas fa-shopping-cart"></i>
            <?php
            $stmt = $dbcon->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $cart_count = $stmt->fetch()['count'];
            if ($cart_count > 0):
            ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <?php
            foreach (getcat() as $cat) {
                echo "<li><a href='categories.php?id=" . $cat['id'] . "&catname=" . $cat['name'] . "'>{$cat['name']}</a></li>";
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
    <div class="cart-container">
        <div class="cart-card">
            <div class="cart-header">
                <h2>Cart</h2>
            </div>
            
            <div class="cart-content">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($cart_items)): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Cart is empty</h3>
                        <p>You haven't added any products to your cart</p>
                        <a href="index.php" class="btn btn-primary">Back to Shop</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <img src="uploudes/images/<?php echo $item['image']; ?>" 
                                 alt="Product Image" 
                                 class="item-image">
                            <div class="item-info">
                                <a class="item-title_link" style="text-decoration: none; color: black;" href="item.php?id=<?php echo $item['item_id']; ?>">
                                <h3 class="item-title"><?php echo $item['name']; ?></h3>
                                </a>
                                <div class="item-price">$<?php echo $item['price']; ?></div>
                            </div>
                            <a href="cart.php?remove=<?php echo $item['item_id']; ?>" class="remove-btn">
                            <i class="fas fa-trash"></i>
                            </a>
                            </div>
                    <?php endforeach; ?>
                    
                    <div class="cart-summary">
                        <h4>Cart Summary</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total:</span>
                            <span class="fw-bold">$<?php echo get_total_price($_SESSION['user_id']); ?></span>
                        </div>
                        <button onclick="show_payment()" class="btn btn-primary checkout-btn">
                            <i class="fas fa-shopping-bag"></i>
                            Complete the purchase
                        </button>
                        <div id="payment_form" style="display: none;">
                            <form class="d-flex justify-content-between mt-3" action="payment.php" method="post">
                                <div>
                                    <label for="cash">Cash</label>
                                    <input type="radio" name="payment_cash" value="cash" class="payment-option" id="cash">
                                </div>
                                <div>
                                    <label for="card">Card</label>
                                    <input type="radio" name="payment_card" value="card" class="payment-option" id="card">
                                </div>
                                <div>
                                    <label for="paypal">Paypal</label>
                                    <input type="radio" name="payment_paypal" value="paypal" class="payment-option" id="paypal">
                                </div>
                                <button class="btn btn-dark px-4 py-1 rounded-pill ms-5">Pay</button>
                            </form>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function show_payment(){
            document.getElementById('payment_form').style.display = 'block';
        }

    let radios = document.querySelectorAll('.payment-option');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            radios.forEach(other => {
                if (other !== radio) {
                    other.checked = false;
                }
            });
        });
    });
</script>
</body>
</html> 