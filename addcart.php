<?php
session_start();

if (isset($_POST['itemid'])){
    if ( !in_array ($_POST['itemid'],$_SESSION['cart'])){
        $_SESSION['cart'][] = $_POST['itemid'];
    }  
}

print_r($_POST);
$badge = count($_SESSION['cart']);
?>