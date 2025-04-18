<?php
session_start();
include "function.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST['payment_cash'])){
        header('Location:request.php');
        exit();    
    }

    if(isset($_POST['payment_card'])){
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment by Card - ShopMaster</title>
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .payment-form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-preview {
            background: linear-gradient(45deg, #1a1a1a, #4a4a4a);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            position: relative;
            height: 200px;
        }
        .card-chip {
            width: 50px;
            height: 40px;
            background: linear-gradient(135deg, #ffd700, #ffa500);
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card-number {
            font-size: 24px;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        .card-details {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }
        .card-type-icons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .card-type-icon {
            width: 60px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .card-type-icon.active {
            border: 2px solid #007bff;
            background: #e7f1ff;
        }
        .card-type-icon img {
            max-width: 40px;
            max-height: 25px;
        }
        .payment-button {
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .payment-button:hover {
            background: #218838;
        }
        .security-info {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .security-info i {
            color: #28a745;
            margin-left: 5px;
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
                <button type="button" class="btn btn-outline-info" onclick="window.location.href='login.php'">تسجيل الدخول</button>
                <button type="button" class="btn btn-outline-primary me-5 ms-2" onclick="window.location.href='signup.php'">إنشاء حساب</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="payment-container">
        <div class="payment-form">
            <div class="card-preview">
                <div class="card-chip"></div>
                <div class="card-number" id="cardNumber">•••• •••• •••• ••••</div>
                <div class="card-details">
                    <div>
                        <small>Card Holder</small>
                        <div id="cardHolder">Card Holder</div>
                    </div>
                    <div>
                        <small>Card Expiry</small>
                        <div id="cardExpiry">MM/YY</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" class="form-control" id="cardNumberInput" placeholder="XXXX XXXX XXXX XXXX" required>
                </div>

                <div class="form-group">
                    <label>Card Holder</label>
                    <input type="text" class="form-control" id="cardHolderInput" placeholder="Card Holder" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Card Expiry</label>
                            <input type="text" class="form-control" id="cardExpiryInput" placeholder="MM/YY" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" class="form-control" placeholder="XXX" required>
                        </div>
                    </div>
                </div>

              
                <button type="submit" class="payment-button">
                    <i class="fas fa-lock"></i>
                    Pay
                </button>
            </form>

            <div class="security-info">
                <i class="fas fa-shield-alt"></i>
                Secure Payment
            </div>
        </div>
    </div>

    <script>
        // تحديث معاينة البطاقة
        document.getElementById('cardNumberInput').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value;
            document.getElementById('cardNumber').textContent = value || '•••• •••• •••• ••••';
        });

        document.getElementById('cardHolderInput').addEventListener('input', function(e) {
            document.getElementById('cardHolder').textContent = e.target.value || 'Card Holder';
        });

        document.getElementById('cardExpiryInput').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
            document.getElementById('cardExpiry').textContent = value || 'MM/YY';
        });

        // تغيير نوع البطاقة
        document.querySelectorAll('.card-type-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                document.querySelectorAll('.card-type-icon').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html> 
    <?php

    }


    if(isset($_POST['payment_paypal'])){
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>payment- ShopMaster</title>
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .payment-form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .paypal-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .paypal-logo img {
            max-width: 150px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
        }
        .card-icons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .card-icon {
            width: 40px;
            height: 25px;
            background: #eee;
            border-radius: 3px;
        }
        .payment-button {
            background: #0070ba;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .payment-button:hover {
            background: #005ea6;
        }
        .security-info {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .security-info i {
            color: #28a745;
            margin-left: 5px;
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
                <button type="button" class="btn btn-outline-primary me-5 ms-2" onclick="window.location.href='signup.php'">Sign</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="payment-container">
        <div class="payment-form">
            <div class="paypal-logo">
                <img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-large.png" alt="PayPal">
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" class="form-control" placeholder="XXXX XXXX XXXX XXXX" required>
                    <div class="card-icons">
                        <div class="card-icon"></div>
                        <div class="card-icon"></div>
                        <div class="card-icon"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expiration Date</label>
                            <input type="text" class="form-control" placeholder="MM/YY" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" class="form-control" placeholder="XXX" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="payment-button">
                    <i class="fas fa-lock"></i>
                    Pay
                </button>
            </form>

            <div class="security-info">
                <i class="fas fa-shield-alt"></i>
                Secure Payment
            </div>
        </div>
    </div>

    <script>
        // التحقق من صحة رقم البطاقة
        document.querySelector('input[type="text"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value;
        });
    </script>
</body>
</html> 
    <?php

    }

}
?>