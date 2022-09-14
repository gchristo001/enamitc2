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
    <link rel="stylesheet" href="print_manual.css?<?=filemtime('print_manual.css');?>">

</head>

<body>

<div class = "printableArea">
    <button class = "info"  id = "printbtn" onClick="window.print()">Print</button>
    <!--<input class = "info" type="text" id = "nama_barang" size="25" value =" | Id:">
    <input class = "info"  type="text" id = "orderid" size="15" value ="Orderid: ">
    <input class = "info"  type="text" id = "userid" size="8" value="Userid: ">
    <input class = "info"  type="text" id = "nama" size="16" value="">
    <input class = "info"  type="text" id = "tanggal" size="9" value="">
    <input class = "info"  type="text" id = "berat"  size="3" value="">
    <input class = "info"  type="text" id = "size" size="2" value="">
    <input class = "info"  type="text" id = "kode" onchange = "cekKadar()" size="3" value="">
    <input class = "info"  type="text" id = "kadar" size="2">
    <input class = "info"  type="text" id = "harga" onchange = "hargatotal()" size="8"  value="">
    <input class = "info"  type="text" id = "harga1" onchange = "hargatotal()" size="8"  value="0">
    <input class = "info"  type="text" id = "harga2" onchange = "hargatotal()" size="8"  value="0">
    <input class = "info"  type="text" id = "harga3" onchange = "hargatotal()" size="8"  value="0">
    <input class = "info"  type="text" id = "info1" size="5"  value="">
    <input class = "info"  type="text" id = "info2" size="5"  value="">
    <input class = "info"  type="text" id = "info3" size="5"  value="">
    <input class = "info"  type="text" id = "hargatotal" size="10"  value="">
    <p class = "info"  id = "etalase">Etalase: <p>-->
    <input class = "info"  type="text" id = "etalase_input" size="5"  value="">
    <input class = "info"  type="file"  accept="image/*" name="image" id="file"  onchange="loadFile(event)" style="display: inline-block;">  
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
        var kode = document.getElementById("kadar").value ;
        let letter = kode.charAt(0);
        if(letter == "7"){
            document.getElementById("kadar").value = "16k";
        }
        else{
            document.getElementById("kadar").value = "17k";
        }

    }

    window.onload = function() {
        document.getElementById("tanggal").value = datestr;
    };
</script>


</body>


</html>