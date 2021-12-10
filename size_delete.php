<?php
require_once "pdo.php";
session_start();


if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}

if(!isset($_GET['attributeid'])){
    header("Location: size_input.php?itemid=".$_GET['itemid']);
    return;
}
else{

$sql = "DELETE FROM item_attributes WHERE attributeid = :attributeid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":attributeid" => $_GET['attributeid']));

header("Location: size_input.php?itemid=".$_GET['itemid']);
return;

}


?>