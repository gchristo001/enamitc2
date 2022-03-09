<?php 
require_once "pdo.php";
session_start();

if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
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
    <input type="text" id = "nama_barang" onchange = "copy_nama_barang()" size="25" value = "">
    <input type="text" id = "orderid" size="15" value ="">
    <input type="text" id = "userid" size="4" value="">
    <input type="text" id = "nama"  onchange = "copy_nama()"  size="12" value="">
    <input type="text" id = "tanggal" size="9" value="">
    <input type="text" id = "berat" onchange = "copy_berat()" size="3" value="">
    <input type="text" id = "size" size="2" value="">
    <input type="text" id = "kode" onchange = "cekKadar(); copy_kode()" size="3" value="">
    <input type="text" id = "kadar" size="2">
    <input type="text" id = "harga" onchange = "hargatotal()" size="8"  value="">
    <input type="text" id = "harga1" onchange = "hargatotal()" size="8"  value="">
    <input type="text" id = "harga2" onchange = "hargatotal()" size="8"  value="">
    <input type="text" id = "harga3" onchange = "hargatotal()" size="8"  value="">
    <input type="text" id = "info1" size="5"  value="">
    <input type="text" id = "info2" size="5"  value="">
    <input type="text" id = "info3" size="5"  value="">
    <input type="text" id = "nama_barang1" size="25" value ="">
    <input type="text" id = "nama_barang2" size="25" value ="">
    <input type="text" id = "orderid1" size="15" value ="">
    <input type="text" id = "orderid2" size="15" value ="">
    <input type="text" id = "nama1" size="12" value="">
    <input type="text" id = "nama2" size="12" value="">
    <input type="text" id = "hargatotal" size="10"> 
    <input type="text" id = "berat1" size="4" value="">
    <input type="text" id = "karton1" onchange = "copyKarton()" size="3">
    <input type="text" id = "berat2" size="4" value="">
    <input type="text" id = "karton2" size="3" >
    <input type="text" id = "kode1" size="3" value="">
    <input type="text" id = "kode2" size="3" value="">
    <input type="text" id = "hargatotal1" size="10" >  
    <input type="text" id = "hargatotal2" size="10" >
    <input type="text" id = "etalase1" onchange = "copyEtalase()" size="2" >  
    <input type="text" id = "etalase2" size="2" >  
    <input type="file"  accept="image/*" name="image" id="file"  onchange="loadFile(event)" style="display: inline-block;">  
    <img id = "gambar" > 
</div>

<script>
var loadFile = function(event) {
	var image = document.getElementById('gambar');
	image.src = URL.createObjectURL(event.target.files[0]);
};
</script>



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

        document.getElementById("harga").value = numberWithCommas(harga);
        document.getElementById("harga1").value = numberWithCommas(harga1);
        document.getElementById("harga2").value = numberWithCommas(harga2);
        document.getElementById("harga3").value = numberWithCommas(harga3);
    }

    function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function editDate(){
        var str = document.getElementById("tanggal").value;
        var first = str.split(" ")[0];
        document.getElementById("tanggal").value = first; 
    }

    function cekKadar(){
        var kode =  document.getElementById("kode").value;
        let letter = kode.charAt(0);
        if(letter == "7"){
            document.getElementById("kadar").value = "16k";
        }
        else{
            document.getElementById("kadar").value = "17k";
        }
    }

    function copyEtalase(){
        document.getElementById("etalase2").value =  document.getElementById("etalase1").value;
    }
    function copyKarton(){
        document.getElementById("karton2").value =  document.getElementById("karton1").value;
    }
    function copy_berat(){
        document.getElementById("berat1").value =  document.getElementById("berat").value;
        document.getElementById("berat2").value =  document.getElementById("berat").value;
    }
    function copy_nama_barang(){
        document.getElementById("nama_barang1").value =  document.getElementById("nama_barang").value;
        document.getElementById("nama_barang2").value =  document.getElementById("nama_barang").value;
    }
    function copy_nama(){
        document.getElementById("nama1").value =  document.getElementById("nama").value;
        document.getElementById("nama2").value =  document.getElementById("nama").value;
    }
    function copy_kode(){
        document.getElementById("kode1").value =  document.getElementById("kode").value;
        document.getElementById("kode2").value =  document.getElementById("kode").value;
    }

    window.onload = function() {
        hargatotal();
        editDate();
    };
</script>


</body>


</html>