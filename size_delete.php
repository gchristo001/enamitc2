<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}


if(!isset($_GET['attributeid'])){
    header("Location: size_input.php?itemid=".$_GET['itemid']);
    return;
}
else{

$sql = "UPDATE item_attributes SET quantity = 0 WHERE attributeid = :attributeid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":attributeid" => $_GET['attributeid']));

header("Location: size_input.php?itemid=".$_GET['itemid']);
return;

}


?>