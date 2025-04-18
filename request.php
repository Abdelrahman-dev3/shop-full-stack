<?php
session_start();
include "function.php";
include "includes/dbcon/pdo.php";

if (!isset($_SESSION['user_id'])) {
    header("location:login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - ShopMaster</title>
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .success-icon {
            color: #28a745;
            font-size: 80px;
            margin-bottom: 20px;
        }
        .order-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-left: 15px;
        }
        .order-item-info {
            flex-grow: 1;
            text-align: right;
        }
        .total-amount {
            font-size: 1.2em;
            font-weight: bold;
            color: #28a745;
            margin-top: 20px;
        }
        .continue-shopping {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="ms-4 logo">Shop<span>Master</span></div>
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
                <button type="button" class="btn btn-outline-primary me-5 ms-2" onclick="window.location.href='signup.php'">إنشاء حساب</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="success-container">
        <i class="fas fa-check-circle success-icon"></i>
        <h2>Payment Success!</h2>
        <p>Thank you for choosing ShopMaster. Your order details will be sent to your email.</p>

        <div class="order-details">
            <h3>Order Details</h3>
            <p>Order ID: #<?php echo $order['order_id']; ?></p>
            <p>Order Date: <?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></p>
            
            <div class="order-items">
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item">
                        <img src="uploudes/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <div class="order-item-info">
                            <h4>Iphone 15 Pro Max</h4>
                            <p>Price: $1000</p>
                            <p>Quantity: 1</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-amount">
                Total Amount: $1000
            </div>
        </div>

        <div class="continue-shopping">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i>
                Back to Shopping
            </a>
        </div>
    </div>
</body>
</html>
