<?php
require '../includes/dbcon/pdo.php'; // connect with database
function row_count($value){
    global $dbcon; // استخدم المتغير من ملف pdo.php
    $sql = '';
    if ($value == 'member') {
        $sql = 'group_id != 1';
    }
    if ($value == 'pending') {
        $sql = 'regsters != 1';
    }
    $statments = $dbcon->prepare("SELECT COUNT(*) FROM users WHERE $sql");
    $statments->execute();
    $count = $statments->fetchColumn();
    return $count;
}
?>