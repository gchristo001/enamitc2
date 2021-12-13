<?php
require_once "pdo.php";
session_start();


if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}

if(!isset($_GET['prizeid'])){
    header("Location: prize_input.php");
    return;
}
else{

$sql = "DELETE FROM prizes WHERE prizeid = :prizeid";
$stmt = $pdo -> prepare($sql);
$stmt->execute(array(":prizeid" => $_GET['prizeid']));

header("Location: prize_input.php");
return;
}


?>