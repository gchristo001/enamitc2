<?php 
require_once "pdo.php";
session_start();




if(isset($_GET['orderid'])){
    $sql = 
        " SELECT 
        orders.orderid as orderid,
        orders.orderdate,
        orders.userid,
        orders.admin,
        users.username,
        users.phone,
        users.address,
        item_attributes.attributeid as attributeid,
        item_attributes.size as size,
        item_attributes.weight as weight,
        item_attributes.price as price,
        items.code as code,
        items.name,
        items.image
        FROM orders
        LEFT JOIN users
        ON orders.userid = users.userid 
        LEFT JOIN item_attributes
        ON orders.attributeid = item_attributes.attributeid
        LEFT JOIN items
        ON item_attributes.itemid = items.itemid
        WHERE orderid = :orderid 
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ":orderid" => $_GET['orderid']  ));
        $print = $stmt->fetch(PDO::FETCH_ASSOC);
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bon</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="print.css?<?=filemtime('print.css');?>">

</head>

<body>

<div class = "printableArea">
    <textarea id = "nama_barang" rows="10"> <?= $print['name'] ?> </textarea>
    <textarea id = "userid" rows="4"> <?= $print['userid'] ?> </textarea>
    <textarea id = "nama" rows="10"> <?= $print['username'] ?> </textarea>
    <textarea id = "tanggal" rows="10"> <?= $print['orderdate'] ?> </textarea>
    <textarea id = "berat" rows="4"> <?= $print['weight'] ?> </textarea>
    <textarea id = "size" rows="2"> <?= $print['size'] ?> </textarea>
    <textarea id = "kode" rows="3"> <?= $print['code'] ?> </textarea>
    
    <img id = "gambar" src = "item-image/<?= $print['image'] ?>"> 
</div>

</body>


</html>