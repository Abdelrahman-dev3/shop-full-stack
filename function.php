<?php
include "includes/dbcon/pdo.php";

// get category v1.0 

function getcat()
{
    global $dbcon;

    $stmt = $dbcon->prepare("SELECT * FROM `categories` WHERE parent = 0 ORDER BY id ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    return $rows;
}


 // get items v1.0 condition

function getitems(int $ID , $approved = 1)
{

    global $dbcon;

    $stmt_ = $dbcon->prepare("SELECT * FROM `items` WHERE cat_id = ? AND aprove = ? ORDER BY item_id DESC");
    $stmt_->execute([$ID , $approved]);
    $rows_ = $stmt_->fetchAll();
    return $rows_;
}

// get pass v1.0 condition

function getpass(int $ID)
{

    global $dbcon;

    $stmt_ = $dbcon->prepare("SELECT `password` FROM `users` WHERE user_id = ?");
    $stmt_->execute([$ID]);
    $rows_ = $stmt_->fetch();
    return $rows_['password'];
}

 // get member v1.0 condition


function getmember(int $ID)
{
    global $dbcon;
    $stmt_ = $dbcon->prepare("SELECT * FROM `users` WHERE user_id = ?");
    $stmt_->execute([$ID]);
    $rows_ = $stmt_->fetch();
    return $rows_;
}



// get item v2.0 

function get_item($id) {
    global $dbcon;
    $stmt = $dbcon->prepare("SELECT * FROM items WHERE item_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}


 // get member v2.0 


function get_member($id) {
    global $dbcon;
    $stmt = $dbcon->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}


 // Advanced search v1.0

function search_about($search) {
    global $dbcon;
    
    // تقسيم النص إلى كلمات مفردة
    $words = explode(" ", $search);
    
    // إنشاء جزء الـ WHERE لكل كلمة في `name` و `description`
    $conditions = $params = [];
    
    foreach ($words as $word) {
        $conditions[] = "(`name` LIKE ? OR `description` LIKE ? )";
        $params[] = "%$word%";
        $params[] = "%$word%";
    }
    
    // دمج الشروط باستخدام OR
    $whereClause = implode(" OR ", $conditions);
    
    // تجهيز الاستعلام النهائي
    $stmt = $dbcon->prepare("SELECT * FROM items WHERE $whereClause AND aprove = 1 ORDER BY item_id DESC");
    
    // تنفيذ الاستعلام مع القيم
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}


function get_comments($item_id) {
    global $dbcon;
    $stmt = $dbcon->prepare("SELECT comments.comment , comments.comment_date , users.username AS username FROM comments INNER JOIN users ON comments.user_id = users.user_id WHERE item_id = ? ORDER BY comment_date DESC");
    $stmt->execute([$item_id]);
    return $stmt->fetchAll();
}

    // get time ago v1.0

function timeAgo($timestamp) {
    $now = time(); # time now
    $diff = $now - strtotime($timestamp); // diff between now and commint time

    if ($diff < 60) {
        return "Just now"; 
    } elseif ($diff < 3600) {
        return floor($diff / 60) . " minutes ago"; // من دقيقة لـ 59 دقيقة
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . " hours ago"; // من ساعة لـ 23 ساعة
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . " days ago"; // من يوم لـ 6 أيام
    } else {
        return date("Y-m-d", strtotime($timestamp)); // غير كده يعرض التاريخ
    }
}

function checkIDExists($id) {
    global $dbcon;
    $stmt = $dbcon->prepare("SELECT COUNT(*) FROM items WHERE item_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}

function get_All_items() {
    global $dbcon;
    $stmt = $dbcon->prepare("SELECT * FROM items WHERE aprove = 1 ORDER BY item_id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}


function get_total_price(int $ID) {
    global $dbcon;
    $stmt = $dbcon->prepare("SELECT
                                SUM(items.price) AS total
                            FROM
                                cart
                            INNER JOIN
                                items ON cart.item_id = items.item_id
                            WHERE
                                cart.user_id = ?");
    $stmt->execute([$ID]);
    return $stmt->fetchColumn();
}

function remove_item(int $ID , int $item_id) {
    global $dbcon;
    $stmt = $dbcon->prepare("DELETE FROM cart WHERE user_id = ? AND item_id = ?");
    $stmt->execute([$ID , $item_id]);
}




