<?php
session_start();
include "function.php";
$status = $_GET['status'] ?? "login";

if ($status == "login") {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - ShopMaster</title>
        <link rel="stylesheet" href="layout/css/nav.css">
        <link rel="stylesheet" href="layout/css/login.css">
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
        <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js">
        <link rel="stylesheet" href="includes/bootstrap/js/bootstrap.bundle.min.js.map">
        <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css.map">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

        <div class="login-container">
            <?php if (isset($_SESSION['check'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <li><?php echo $_SESSION['check']; ?></li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['check']);  ?>
            <?php endif; ?>
            <form action="?status=process_login" method="POST">
                <h1>Login</h1>
                <div class="form-group">
                    <input type="text" id="username" name="username" value="<?php echo $_SESSION['un'] ?? ""; ?>" required>
                    <?php unset($_SESSION['un']); ?>
                    <label for="username">Username</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder=" " required>
                    <label for="password">Password</label>
                </div>
                <button type="submit">Login</button>
            </form>
            <div class="signup-link">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </div>
        </div>
    </body>

    </html>
<?php
} elseif ($status == "process_login") {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? "";
        $password = $_POST['password'] ?? "";

        $stmt = $dbcon->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                $_SESSION['check'] = "يرجى التحقق من بريدك الإلكتروني قبل تسجيل الدخول";
                header("location:login.php");
                exit();
            }
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("location:profile.php");
            exit();
        } else {
            $_SESSION['check'] = "Invalid Username or Password";
            header("location:login.php");
            exit();
        }
    } else {
        header("location:login.php");
        exit();
    }
} else {
    header("location:login.php");
    exit();
}
?>