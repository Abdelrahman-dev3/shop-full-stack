<?php
$dsn = "mysql:host=localhost;dbname=shop"; 
$username = "root"; 
$password = ""; 

try{

    $dbcon = new PDO($dsn,$username,$password); 

}catch (PDOException $e) {

    echo "Database error: " . $e->getMessage();

}
?>