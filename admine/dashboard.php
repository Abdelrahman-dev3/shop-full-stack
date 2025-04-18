<?php
session_start();
$_SESSION['title'] ='Dashboard';
if (!isset($_SESSION['username'])) {
    header("location:admine_login.php");
    exit();
}
// get count
require 'row_count.php';
$statments = $dbcon->prepare("SELECT COUNT(*) FROM items ");
$statments->execute();
$count = $statments->fetchColumn();

$statments = $dbcon->prepare("SELECT COUNT(*) FROM comments ");
$statments->execute();
$count_comm = $statments->fetchColumn();
// end get count


require 'startnav.php';
?>
<div class="cards">
    <a style='text-decoration:none' href="members.php">
    <div class="card ceilblue">
    <i class="fas fa-users icon"></i>
        <div class="text">Total Members</div>
        <div class="number"><?php echo row_count('member') ;?></div>
    </div>
    </a>
    <a style='text-decoration:none' href="members.php?page=pending">
    <div class="card yellow">
    <i class="fas fa-user-plus icon"></i>
        <div class="text">Pending Members</div>
        <div class="number"><?php echo row_count('pending') ;?></div>
    </div>
    </a>
    <a style='text-decoration:none' href="items.php">
    <div class="card orange">
    <i class="fas fa-tags icon"></i>
        <div class="text">Total Items</div>
        <div class="number"><?php echo $count ;?></div>
    </div>
    </a>
    <div style='text-decoration:none'>
    <div class="card purple">
    <i class="fas fa-comments icon"></i>
        <div class="text">Total Comments</div>
        <div class="number"><?php echo $count_comm ?></div>
    </div>
</div>
</div>

<!-- Latest Registered -->
<div class="latest">
    <div class="box">
        <h3><span class="icon">👤</span> Latest Registered Users</h3>
        <form action='members.php?page=handle' method='post'>
        <ul>
        <?php
            $statments = $dbcon->prepare("SELECT user_id ,regsters , username FROM users  ORDER BY user_id DESC LIMIT 5");
            $statments->execute();
            $last_reg = $statments->fetchAll();
            echo '<div style="background-color: white; padding: 15px; width: 94%;">';
            foreach ($last_reg as $user) {
                echo '<div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #ddd;">';
                echo '<span>' . $user['username'] . '</span>';
                echo '</div>';
            }
            echo '</div>';
                    ?>
        </ul>
        </form>
    </div>

    <div class="box">
        <h3><span class="icon">📦</span> Latest Items</h3>
        <ul>
        <?php
            $statments = $dbcon->prepare("SELECT `name` FROM items  ORDER BY item_id DESC LIMIT 5");
            $statments->execute();
            $last_item = $statments->fetchAll();
            echo '<div style="background-color: white; padding: 15px; width: 94%;">';
            foreach ($last_item as $item) {
                echo '<div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #ddd;">';
                echo '<span>' . $item['name'] . '</span>';
                echo '</div>';
            }
            echo '</div>';
                    ?>
        </ul>
    </div>
</div>