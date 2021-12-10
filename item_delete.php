<?php
require_once "pdo.php";
session_start();


if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}

if(!isset($_GET['itemid'])){
    header("Location: item_input.php");
    return;
}
else{
$sql = "DELETE FROM items WHERE itemid = :itemid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":itemid" => $_GET['itemid']));

$sql = "DELETE FROM item_attributes WHERE itemid = :itemid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":itemid" => $_GET['itemid']));

header("Location: item_input.php");
return;

}


?>