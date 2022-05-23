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
        item_attributes.attributeid as attributeid,
        item_attributes.size as size,
        item_attributes.weight as weight,
        item_attributes.price as price,
        items.code as code,
        items.name,
        items.event,
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
    <input type="text" id = "nama_barang" size="25" value ="<?= $print['name'] ?> | Id:<?= $print['attributeid'] ?>">
    <input type="text" id = "orderid" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "userid" size="8" value="Userid: <?= $print['userid'] ?>">
    <input type="text" id = "nama" size="16" value="<?= $print['username'] ?>">
    <input type="text" id = "tanggal" size="9" value="">
    <input type="text" id = "berat"  size="3" value="<?= $print['weight'] ?> ">
    <input type="text" id = "size" size="2" value="<?= $print['size'] ?>">
    <input type="text" id = "kode" onchange = "cekKadar()" size="3" value="">
    <input type="text" id = "kadar" size="2">
    <input type="text" id = "harga" onchange = "hargatotal()" size="8"  value="<?=$print['price']*1000?>">
    <input type="text" id = "harga1" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "harga2" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "harga3" onchange = "hargatotal()" size="8"  value="0">
    <input type="text" id = "info1" size="5"  value="">
    <input type="text" id = "info2" size="5"  value="">
    <input type="text" id = "info3" size="5"  value="">
    <input type="text" id = "hargatotal" size="10"  value="">
    <input type="text" id = "etalase" value = "etalase">
    <input type="text" id = "etalase_input" size="5"  value="">
    <img id = "gambar" src = "item-image/<?= $print['image'] ?>">
    


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
        document.getElementById("hargatotal").value = numberWithCommas(hargatotal);
        document.getElementById("harga").value = numberWithCommas(harga);
        document.getElementById("harga1").value = numberWithCommas(harga1);
        document.getElementById("harga2").value = numberWithCommas(harga2);
        document.getElementById("harga3").value = numberWithCommas(harga3);
    }

    function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    const d = new Date();
    var datestr = convertDate(d);

    function convertDate(inputFormat) {
    function pad(s) { return (s < 10) ? '0' + s : s; }
    var d = new Date(inputFormat)
    return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/')
    }

    function cekKadar(){
        var kode =  String(<?php echo($print["code"])?>);
        let letter = kode.charAt(0);
        if(letter == "7"){
            document.getElementById("kadar").value = "16k";
        }
        else{
            document.getElementById("kadar").value = "17k";
        }

        var ciliu_flag = <?php echo($print["event"])?>;
        
        if(ciliu_flag == 3){
            document.getElementById("kode").value = "c" + kode;
        }
        else {
            document.getElementById("kode").value = kode;
        }
    }



    window.onload = function() {
        hargatotal();
        cekKadar();
        document.getElementById("tanggal").value = datestr;
    };
</script>


</body>


</html>