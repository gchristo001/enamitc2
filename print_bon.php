<?php 
require_once "pdo.php";
session_start();

if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}



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
    <button id = "printbtn" onClick="window.print()">Print</button>
    <input type="text" id = "nama_barang" size="25" value ="<?= $print['name'] ?>">
    <input type="text" id = "orderid" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "userid" size="4" value="<?= $print['userid'] ?>">
    <input type="text" id = "nama" size="12" value="<?= $print['username'] ?>">
    <input type="text" id = "tanggal" size="9" value="<?= $print['orderdate'] ?>">
    <input type="text" id = "berat" onchange = "copy_berat()" size="3" value="<?= $print['weight'] ?> ">
    <input type="text" id = "size" size="2" value="<?= $print['size'] ?>">
    <input type="text" id = "kode" onchange = "cekKadar()" size="3" value="<?= $print['code'] ?>">
    <input type="text" id = "kadar" size="2">
    <input type="text" id = "harga" onchange = "hargatotal()" size="8"  value="<?=$print['price']*1000?>">
    <input type="text" id = "harga1" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "harga2" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "harga3" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "info1" size="5"  value="">
    <input type="text" id = "info2" size="5"  value="">
    <input type="text" id = "info3" size="5"  value="">
    <input type="text" id = "nama_barang1" size="25" value ="<?= $print['name'] ?>">
    <input type="text" id = "nama_barang2" size="25" value ="<?= $print['name'] ?>">
    <input type="text" id = "orderid1" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "orderid2" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "nama1" size="12" value="<?= $print['username'] ?>">
    <input type="text" id = "nama2" size="12" value="<?= $print['username'] ?>">
    <input type="text" id = "hargatotal" size="10"> 
    <input type="text" id = "berat1" size="4" value="<?= $print['weight'] ?>">
    <input type="text" id = "karton1" onchange = "copyKarton()" size="3">
    <input type="text" id = "berat2" size="4" value="<?= $print['weight'] ?>">
    <input type="text" id = "karton2" size="3" >
    <input type="text" id = "kode1" size="3" value="<?= $print['code'] ?>">
    <input type="text" id = "kode2" size="3" value="<?= $print['code'] ?>">
    <input type="text" id = "hargatotal1" size="10" >  
    <input type="text" id = "hargatotal2" size="10" >
    <input type="text" id = "etalase1" onchange = "copyEtalase()" size="2" >  
    <input type="text" id = "etalase2" size="2" >    
    <img id = "gambar" src = "item-image/<?= $print['image'] ?>">
    
    <input type="text" id = "cnama_barang" size="25" value ="<?= $print['name'] ?>">
    <input type="text" id = "corderid" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "cuserid" size="4" value="<?= $print['userid'] ?>">
    <input type="text" id = "cnama" size="12" value="<?= $print['username'] ?>">
    <input type="text" id = "ctanggal" size="9" value="<?= $print['orderdate'] ?>">
    <input type="text" id = "cberat" onchange = "copy_berat()" size="3" value="<?= $print['weight'] ?> ">
    <input type="text" id = "csize" size="2" value="<?= $print['size'] ?>">
    <input type="text" id = "ckode" onchange = "cekKadar()" size="3" value="<?= $print['code'] ?>">
    <input type="text" id = "ckadar" size="2">
    <input type="text" id = "charga" onchange = "hargatotal()" size="8"  value="<?=$print['price']*1000?>">
    <input type="text" id = "charga1" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "charga2" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "charga3" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "cinfo1" size="5"  value="">
    <input type="text" id = "cinfo2" size="5"  value="">
    <input type="text" id = "cinfo3" size="5"  value="">
    <input type="text" id = "cnama_barang1" size="25" value ="<?= $print['name'] ?>">
    <input type="text" id = "cnama_barang2" size="25" value ="<?= $print['name'] ?>">
    <input type="text" id = "corderid1" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "corderid2" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "cnama1" size="12" value="<?= $print['username'] ?>">
    <input type="text" id = "cnama2" size="12" value="<?= $print['username'] ?>">
    <input type="text" id = "chargatotal" size="10"> 
    <input type="text" id = "cberat1" size="4" value="<?= $print['weight'] ?>">
    <input type="text" id = "ckarton1" onchange = "copyKarton()" size="3">
    <input type="text" id = "cberat2" size="4" value="<?= $print['weight'] ?>">
    <input type="text" id = "ckarton2" size="3" >
    <input type="text" id = "ckode1" size="3" value="<?= $print['code'] ?>">
    <input type="text" id = "ckode2" size="3" value="<?= $print['code'] ?>">
    <input type="text" id = "chargatotal1" size="10" >  
    <input type="text" id = "chargatotal2" size="10" >
    <input type="text" id = "cetalase1" onchange = "copyEtalase()" size="2" >  
    <input type="text" id = "cetalase2" size="2" >    
    <img id = "cgambar" src = "item-image/<?= $print['image'] ?>"> 



</div>


<script>
   
    function hargatotal() {
        var harga = document.getElementById("harga").value;
        var harga1 = document.getElementById("harga1").value;
        var harga2 = document.getElementById("harga2").value;
        var harga3 = document.getElementById("harga3").value;

        harga = harga.replace(/,/g, '');
        harga1 = harga1.replace(/,/g, '');
        harga2 = harga2.replace(/,/g, '');
        harga3 = harga3.replace(/,/g, '');

        var hargatotal = parseInt(harga) + parseInt(harga1) + parseInt(harga2) + parseInt(harga3);
        document.getElementById("hargatotal").value = "Rp. " + numberWithCommas(hargatotal);
        document.getElementById("hargatotal1").value = "Rp. " + numberWithCommas(hargatotal);
        document.getElementById("hargatotal2").value = "Rp. " + numberWithCommas(hargatotal);

        document.getElementById("chargatotal").value = "Rp. " + numberWithCommas(hargatotal);
        document.getElementById("chargatotal1").value = "Rp. " + numberWithCommas(hargatotal);
        document.getElementById("chargatotal2").value = "Rp. " + numberWithCommas(hargatotal);

        document.getElementById("harga").value = numberWithCommas(harga);
        document.getElementById("harga1").value = numberWithCommas(harga1);
        document.getElementById("harga2").value = numberWithCommas(harga2);
        document.getElementById("harga3").value = numberWithCommas(harga3);

        document.getElementById("charga").value = numberWithCommas(harga);
        document.getElementById("charga1").value = numberWithCommas(harga1);
        document.getElementById("charga2").value = numberWithCommas(harga2);
        document.getElementById("charga3").value = numberWithCommas(harga3);
    }

    function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function editDate(){
        var str = document.getElementById("tanggal").value;
        var first = str.split(" ")[0];
        document.getElementById("tanggal").value = first; 
        document.getElementById("ctanggal").value = first; 
    }

    function cekKadar(){
        var kode =  document.getElementById("kode").value;
        let letter = kode.charAt(0);
        if(letter == "7"){
            document.getElementById("kadar").value = "16k";
            document.getElementById("ckadar").value = "16k";
        }
        else{
            document.getElementById("kadar").value = "17k";
            document.getElementById("ckadar").value = "17k";
        }
    }

    function copyEtalase(){
        document.getElementById("etalase2").value =  document.getElementById("etalase1").value;
        document.getElementById("cetalase1").value =  document.getElementById("etalase1").value;
        document.getElementById("cetalase2").value =  document.getElementById("cetalase1").value;
    }
    function copyKarton(){
        document.getElementById("karton2").value =  document.getElementById("karton1").value;
        document.getElementById("ckarton1").value =  document.getElementById("karton1").value;
        document.getElementById("ckarton2").value =  document.getElementById("ckarton1").value;
    }
    function copy_berat(){
        document.getElementById("berat1").value =  document.getElementById("berat").value;
        document.getElementById("berat2").value =  document.getElementById("berat").value;
        document.getElementById("cberat1").value =  document.getElementById("berat").value;
        document.getElementById("cberat2").value =  document.getElementById("berat").value;
    }


    window.onload = function() {
        hargatotal();
        cekKadar();
        editDate();
    };
</script>


</body>


</html>