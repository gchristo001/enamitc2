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

        if (!$print){
            $_SESSION['error'] = "Orderid tidak ditemukan";
            header("Location: menu_print.php");
            return;
        }
}


if(isset($_POST['delete'])){
    $sql = "DELETE FROM orders WHERE orderid = :orderid";
    $stmt = $pdo -> prepare($sql);
    $stmt->execute(array(":orderid" => $_GET['orderid']));

    header("Location: menu_print.php");
    return;

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
    <a id = "back" href="menu_print.php"> Back </a>
    <button id = "printbtn" onClick="window.print()">Print</button>
    <input type="text" id = "nama_barang" size="25" value ="<?= $print['name'] ?> | Id:<?= $print['attributeid'] ?>">
    <input type="text" id = "orderid" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" id = "userid" size="15" value="Userid: <?= $print['userid'] ?>">
    <input type="text" id = "nama" size="16" value="<?= $print['username'] ?>">
    <input type="text" id = "tanggal" size="9" value="">
    <input type="text" id = "berat"  size="4" value="<?= $print['weight'] ?> ">
    <input type="text" id = "size" size="4" value="<?= $print['size'] ?>">
    <input type="text" id = "kode" onchange = "cekKadar()" size="3" value="">
    <input type="text" id = "kadar" size="3">
    <input type="text" id = "harga" onchange = "hargatotal()" size="8"  value="<?=$print['price']*1000?>">
    <input type="text" id = "harga1" onchange = "hargatotal()" onfocus=this.value=''  size="8"  value="0">
    <input type="text" id = "harga2" onchange = "hargatotal()" onfocus=this.value=''  size="8"  value="0">
    <input type="text" id = "harga3" onchange = "hargatotal()"  onfocus=this.value='' size="8"  value="0">
    <input type="text" id = "info1" onchange = "copyinfo()" size="5"  value="">
    <input type="text" id = "info2" onchange = "copyinfo()" size="5"  value="">
    <input type="text" id = "info3" onchange = "copyinfo()" size="5"  value="">
    <input type="text" id = "hargatotal" size="10"  value="">
    <input type="text" id = "etalase" value = "">
    <input type="text" id = "etalase_input" size="10"  value="Etls: ">
    <img id = "gambar" src = "item-image/<?= $print['image'] ?>">
    <form method = "post" id = "delete_bon">
        <input type = "submit" id = "del_btn" name = "delete" value="Delete">
    </form>

    <input type="text" class = "hidden" id = "cnama_barang" size="25" value ="<?= $print['name'] ?> | Id:<?= $print['attributeid'] ?>">
    <input type="text" class = "hidden" id = "corderid" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" class = "hidden" id = "cuserid" size="15" value="Userid: <?= $print['userid'] ?>">
    <input type="text" class = "hidden" id = "cnama" size="16" value="<?= $print['username'] ?>">
    <input type="text" class = "hidden" id = "ctanggal" size="9" value="">
    <input type="text" class = "hidden" id = "cberat"  size="4" value="<?= $print['weight'] ?> ">
    <input type="text" class = "hidden" id = "csize" size="4" value="<?= $print['size'] ?>">
    <input type="text" class = "hidden" id = "ckode" onchange = "cekKadar()" size="3" value="">
    <input type="text" class = "hidden" id = "ckadar" size="3">
    <input type="text" class = "hidden"  id = "charga" onchange = "hargatotal()" size="8"  value="<?=$print['price']*1000?>">
    <input type="text" class = "hidden" id = "charga1" onchange = "hargatotal()" onfocus=this.value=''  size="8"  value="0">
    <input type="text" class = "hidden" id = "charga2" onchange = "hargatotal()" onfocus=this.value=''  size="8"  value="0">
    <input type="text" class = "hidden" id = "charga3" onchange = "hargatotal()"  onfocus=this.value='' size="8"  value="0">
    <input type="text" class = "hidden" id = "cinfo1" size="5"  value="">
    <input type="text" class = "hidden" id = "cinfo2" size="5"  value="">
    <input type="text" class = "hidden" id = "cinfo3" size="5"  value="">
    <input type="text" class = "hidden" id = "chargatotal" size="10"  value="">
    <input type="text" class = "hidden" id = "cetalase_input" size="10"  value="Etls: ">
    <img id = "cgambar"class = "hidden" src = "item-image/<?= $print['image'] ?>">

    <input type="text" class = "hidden" id = "ccnama_barang" size="25" value ="<?= $print['name'] ?> | Id:<?= $print['attributeid'] ?>">
    <input type="text" class = "hidden" id = "ccorderid" size="15" value ="Orderid: <?= $print['orderid'] ?>">
    <input type="text" class = "hidden" id = "ccuserid" size="15" value="Userid: <?= $print['userid'] ?>">
    <input type="text" class = "hidden" id = "ccnama" size="16" value="<?= $print['username'] ?>">
    <input type="text" class = "hidden" id = "cctanggal" size="9" value="">
    <input type="text" class = "hidden" id = "ccberat"  size="4" value="<?= $print['weight'] ?> ">
    <input type="text" class = "hidden" id = "ccsize" size="4" value="<?= $print['size'] ?>">
    <input type="text" class = "hidden" id = "cckode" onchange = "cekKadar()" size="3" value="">
    <input type="text" class = "hidden" id = "cckadar" size="3">
    <input type="text" class = "hidden"  id = "ccharga" onchange = "hargatotal()" size="8"  value="<?=$print['price']*1000?>">
    <input type="text" class = "hidden" id = "ccharga1" onchange = "hargatotal()" onfocus=this.value=''  size="8"  value="0">
    <input type="text" class = "hidden" id = "ccharga2" onchange = "hargatotal()" onfocus=this.value=''  size="8"  value="0">
    <input type="text" class = "hidden" id = "ccharga3" onchange = "hargatotal()"  onfocus=this.value='' size="8"  value="0">
    <input type="text" class = "hidden" id = "ccinfo1" size="5"  value="">
    <input type="text" class = "hidden" id = "ccinfo2" size="5"  value="">
    <input type="text" class = "hidden" id = "ccinfo3" size="5"  value="">
    <input type="text" class = "hidden" id = "cchargatotal" size="10"  value="">
    <input type="text" class = "hidden" id = "ccetalase_input" size="10"  value="Etls: ">
    <img id = "ccgambar"class = "hidden" src = "item-image/<?= $print['image'] ?>">

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

        document.getElementById("chargatotal").value = numberWithCommas(hargatotal);
        document.getElementById("charga").value = numberWithCommas(harga);
        document.getElementById("charga1").value = numberWithCommas(harga1);
        document.getElementById("charga2").value = numberWithCommas(harga2);
        document.getElementById("charga3").value = numberWithCommas(harga3);

        document.getElementById("cchargatotal").value = numberWithCommas(hargatotal);
        document.getElementById("ccharga").value = numberWithCommas(harga);
        document.getElementById("ccharga1").value = numberWithCommas(harga1);
        document.getElementById("ccharga2").value = numberWithCommas(harga2);
        document.getElementById("ccharga3").value = numberWithCommas(harga3);

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

    function copyinfo(){
        document.getElementById("cinfo1").value = document.getElementById("info1").value
        document.getElementById("cinfo2").value = document.getElementById("info2").value
        document.getElementById("cinfo3").value = document.getElementById("info3").value

        document.getElementById("ccinfo1").value = document.getElementById("info1").value
        document.getElementById("ccinfo2").value = document.getElementById("info2").value
        document.getElementById("ccinfo3").value = document.getElementById("info3").value
    }

    function cekKadar(){
        var kode =  String(<?php echo($print["code"])?>);
        let letter = kode.charAt(0);
        if(letter == "7"){
            document.getElementById("kadar").value = "16k";
            document.getElementById("ckadar").value = "16k";
            document.getElementById("cckadar").value = "16k";
        }
        else{
            document.getElementById("kadar").value = "17k";
            document.getElementById("ckadar").value = "17k";
            document.getElementById("cckadar").value = "17k";
        }

        var ciliu_flag = <?php echo($print["event"])?>;
        
        if(ciliu_flag == 3){
            document.getElementById("kode").value = "c" + kode;
            document.getElementById("ckode").value = "c" + kode;
            document.getElementById("cckode").value = "c" + kode;
        }
        else {
            document.getElementById("kode").value = kode;
            document.getElementById("ckode").value = kode;
            document.getElementById("cckode").value = kode;
        }
    }

    function hide_admin_user(){
        var userid = <?php echo($print["userid"])?>;
        if (userid == 1){
            document.getElementById("nama").style.display = "none";
            document.getElementById("userid").style.display = "none";
            document.getElementById("cnama").style.display = "none";
            document.getElementById("cuserid").style.display = "none";
            document.getElementById("ccnama").style.display = "none";
            document.getElementById("ccuserid").style.display = "none";
        }
    }



    window.onload = function() {
        hargatotal();
        cekKadar();
        document.getElementById("tanggal").value = datestr;
        document.getElementById("ctanggal").value = datestr;
        document.getElementById("cctanggal").value = datestr;
        hide_admin_user();
    };
</script>


</body>


</html>