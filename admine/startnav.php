<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $_SESSION['title'] ;?></title>
        <link rel="stylesheet" href="../layout/css/members.css">
        <link rel="stylesheet" href="../layout/css/categories.css">
        <?php
        if (isset($_SESSION['manage.style'])) {
            echo '<link rel="stylesheet" href="../layout/css/manage.css">';
        }
        ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    </head>
    <body>
        <div class="sidebar">
            <h2><?php echo $_SESSION['title'] ;?></h2>
            <ul>
                <li><i class="fas fa-home"></i> <a href="dashboard.php">Home</a></li>
                <li><i class="fas fa-th-large"></i> <a href="categories.php">Categories</a></li>
                <li><i class="fas fa-box"></i> <a href="items.php">Items</a></li>
                <li><i class="fas fa-users"></i> <a href="members.php">Members</a></li>
                <li><i class="fas fa-chart-bar"></i> <a href="#">Statistics</a></li>
                <li><i class="fas fa-history"></i> <a href="../index.php">Go Shop</a></li>
                <li><i class="fas fa-user-edit"></i> <a href="members.php?page=edit">Edit Profile</a></li>
                <li><i class="fas fa-cog"></i> <a href="#">Settings</a></li>
                <li><i class="fas fa-sign-out-alt"></i> <a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">

        <!-- require 'startnav.php';
        $_SESSION['title'] ='Edit Member'; -->


