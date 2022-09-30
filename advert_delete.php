<?php
require_once "pdo.php";
session_start();
error_reporting(0);


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}

if (isset($_GET['adid'])){
    $sql = "DELETE FROM advert WHERE adid = :adid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":adid" => $_GET['adid']));
    $adinfo = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['success'] = 'Iklan berhasil dihapus';
    header("Location: advert_input.php");
    return;
}

else{
    $_SESSION['error'] = 'Id iklan tidak ditemukan';
    header("Location: advert_input.php");
    return;
}



?>