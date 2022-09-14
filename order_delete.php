<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}


if(isset($_GET['offline_order_id'])){
    $sql = "DELETE FROM offline_order WHERE offline_order_id = :offline_order_id";
    $stmt = $pdo -> prepare($sql);
    $stmt->execute(array(":offline_order_id" => $_GET['offline_order_id']));

    header("Location: order_input.php");
    return;
}

if(isset($_GET['orderid'])){
    $sql = "DELETE FROM orders WHERE orderid = :orderid";
    $stmt = $pdo -> prepare($sql);
    $stmt->execute(array(":orderid" => $_GET['orderid']));

    header("Location: menu_print.php");
    return;
}



?>