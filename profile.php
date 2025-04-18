<?php
session_start();
include "function.php";

    //  اما المستخدم عمل login عملنا session user_id فهمه لو مش مسجل هيرجعوا لصفحة login.php

if (!isset($_SESSION['user_id'])) {
    header("location:login.php");
    exit();
}

if(isset($_POST['change_password'])){
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    
    if ($new_password !== $confirm_password) {
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    New password and confirm password do not match
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }else{

        if(password_verify($old_password, getpass($_SESSION['user_id']))){
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $dbcon->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hash, $_SESSION['user_id']]);
            $success = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Password changed successfully
                        </div>';
        }else{
        $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Old password is incorrect
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }

    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["add_image"])) {

    if(empty($_FILES['image']['name'])){
        $errors_image = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Image is required
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    $file = $_FILES['image'];
    $file_name = $file['name'];
    $file_size = $file['size'];

    $file_name_parts = explode('.', $file_name);
    $file_ext = strtolower(end($file_name_parts));

    $imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp", "svg", "tiff", "tif", "ico", "jfif", "avif"];

    if(in_array($file_ext, $imageExtensions)){
        if($file_size > 5000000){
            $errors_image = "Image size must be less than 5MB";
        }else{
            $new_name = uniqid('', true) . '.' . $file_ext;
            move_uploaded_file($file['tmp_name'], "uploudes/images/" . $new_name);
            $stmt = $dbcon->prepare("UPDATE users SET `image` = ? WHERE user_id = ?");
            $stmt->execute([$new_name, $_SESSION['user_id']]);
        }
    }
    

    if(!in_array($file_ext, $imageExtensions) && !empty($_FILES['image']['name'])){
        $errors_image = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Invalid image Extensions
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }
}



    // جلب بيانات المستخدم

$user_id = $_SESSION['user_id'];
$stmt = $dbcon->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

    // جلب منتجات المستخدم

$products_stmt = $dbcon->prepare("SELECT * FROM `items` WHERE member_id = ? ORDER BY add_date DESC");
$products_stmt->execute([$user_id]);
$products = $products_stmt->fetchAll();
?>
    <!-- قسم بيانات المستخدم -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ShopMaster</title>
    <link rel="stylesheet" href="layout/css/nav.css">
    <link rel="stylesheet" href="layout/css/profile.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <div class="profile-container">
        <?php echo isset($errors_image) ? $errors_image : ''; ?>
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php
                    if($user['image'] != 0){
                        echo "<img style='width: 100%;height: 100%;border-radius: 50%;' src='uploudes/images/{$user['image']}' alt='User Image'>";
                    }else{
                        echo "<i id='i' class='fas fa-user-circle'></i>";
                    }
                    ?>
                    <div class="upload-button">
                        <form action="" method="post" id="add_image" enctype="multipart/form-data">
                            <input type="file" name="image" id="image">
                            <input type="hidden" name="add_image" value="1">
                        </form>
                        <i class="fa-solid fa-plus"></i>
                    </div>
                
                </div>

                <h2><?php echo $user['username'] ?? "username"; ?></h2>
                <?php
                echo '<p class="email"> ' . $user["email"] . '</p>';
                ?>
            </div>
            
            <div class="profile-info">
                <div class="info-group">
                    <i class="fas fa-user"></i>
                    <div class="info-content">
                        <label>Full Name</label>
                        <p><?php echo $user['full_name'] ?? "full_name"; ?></p>
                    </div>
                </div>
                
                <div class="info-group">
                    <i class="fas fa-key"></i>
                    <div class="info-content">
                        <label>Member Since</label>
                        <p><?php echo date('F Y', strtotime($user['date'])); ?></p>
                    </div>
                </div>
            </div>
            <?php echo isset($error) ? $error : ''; ?>
            <?php echo isset($success) ? $success : ''; ?>
            <div class="profile-actions">
                <button id="change-password-btn" class="change-password-btn"><i class="fas fa-lock"></i> Change Password</button>
                <form id="change-password-form" action="" method="post">
                        <input type="password" name="old_password" id="old_password" placeholder="Old Password">
                        <input type="password" name="new_password" id="new_password" placeholder="New Password">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
        <!-- قسم منتجات المستخدم -->
        <div class="user-products">
            <h3 class="section-title">Products</h3>
            <?php
                if(empty($products)){
            ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <p>You haven't published any products yet.</p>
                    <a href="add_product.php" class="btn btn-primary">Add Product</a>
                </div>
            <?php
                }else{
            ?>
                <div class="products-grid">
                    <?php
                    foreach($products as $product){
                        if($product['aprove'] != 0){ 
                    ?>
                    <a style="text-decoration: none; color: black;" href="item.php?id=<?php echo $product['item_id'] ?>">
                    <?php
                        }
                        ?>
                        <div class="product-card">
                        <?php
                        if($product['aprove'] == 0){ 
                            echo "<div class='not-approved'>Pending Approval</div>";
                        }
                        ?>
                            <div class="product-image">
                                <img src="uploudes/images/<?php echo $product['image'] ?>">
                            </div>
                            <div class="product-info">
                                <h4><?php echo $product['name'] ?></h4>
                                <p class="description"><?php echo $product['description'] ?></p>
                                <div class="product-details">
                                    <span class="price"><?php echo '$' . $product['price'] ?></span>
                                    <span class="country"><i class="fas fa-globe"></i><?php echo $product['country'] ?></span>
                                    <span class="date"><i class="fas fa-calendar"></i><?php echo $product['add_date'] ?></span>
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
    <script>
        document.getElementById('change-password-btn').addEventListener('click', () => {
            let form = document.getElementById('change-password-form');
            form.classList.toggle('show'); 
        });
        document.getElementById("image").addEventListener("change", function() {
            let form = document.getElementById("add_image");
            form.submit();
        });
    </script>
</body>
</html> 