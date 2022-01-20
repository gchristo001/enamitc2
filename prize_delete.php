<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}


if(!isset($_GET['prizeid'])){
    header("Location: prize_input.php");
    return;
}
else{

$sql = "UPDATE prizes SET quantity = 0 WHERE prizeid = :prizeid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":prizeid" => $_GET['prizeid']));

header("Location: prize_input.php");
return;
}


?>