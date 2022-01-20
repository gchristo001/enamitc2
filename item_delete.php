<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}


if(!isset($_GET['itemid'])){
    header("Location: item_input.php");
    return;
}
else{
$sql = "UPDATE item_attributes SET quantity = 0  WHERE itemid = :itemid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":itemid" => $_GET['itemid']));

header("Location: item_input.php");
return;

}


?>