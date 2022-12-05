<?php
require_once "pdo.php";
session_start();
error_reporting(0);


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
     die("ACCESS DENIED");
}

    $sql = " SELECT * FROM items
    LEFT JOIN item_attributes
    ON items.itemid = item_attributes.itemid
    where
    attributeid in (select t.attributeid from (select itemid, min(attributeid) as attributeid from item_attributes group by 1) as t) and quantity > 0
    ORDER BY items.itemid DESC
    LIMIT 50";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Barang</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css?<?=filemtime('admin.css');?>">

    <!-- custom js file link  -->
    <script src="admin_script.js"defer></script>

    <style>

        .banner{
            display: grid;
            grid-template-columns:  repeat(auto-fill, minmax(400px, 1fr));
            padding-top: 100px;
        }


        @media (max-width: 500px) {
            html {
                font-size: 60%;
                overflow: scroll;
            }
            .home .slide .content h3 {
                font-size: 4rem;
            }
            label{
                font-size: 12rem;
            }
            .input {
                width: 14rem;
                font-size: 1.5rem;
                color: black;
                padding: .5rem 1rem;
                border-radius: .5rem;
                background: #eee;
            }
            .button{
                color: #fff;
                width: 14rem;
                height: 34px;
                background: black;
                border-radius: 5px;
            }

            .banner .box{
                display: flex;
                flex-direction: column;
                padding-top: 20px;
                margin-left: 10px;;
            }

            table, thead, tbody, th, td, tr { 
            display: flex;
            flex-direction: column;
            width: 30rem;
            padding: 5px; 
            }
            
            
            tr { border: 1px solid #ccc; }
            
            td { 
            border: none;
            border-bottom: 1px solid #eee; 
            position: relative;
            padding-left: 30%;
            width: auto;
            text-align: left;  
            }

            th{display: none;}
            
            td:before { 
            position: absolute;
            top: 6px;
            left: 6px;
            width: 35%; 
            padding-right: 10px; 
            white-space: nowrap;
            font-weight: bold;
            text-align: left;
            }
            
        td:nth-of-type(1):before { content: "Id"; }
        td:nth-of-type(2):before { content: "Tampil"; }
        td:nth-of-type(3):before { content: "Nama"; }
        td:nth-of-type(4):before { content: "Hot Deal"; }
        td:nth-of-type(5):before { content: "Event"; }
        td:nth-of-type(6):before { content: "Supplier"; }
        td:nth-of-type(7):before { content: "Kategori"; }
        td:nth-of-type(8):before { content: "Warna"; }
        td:nth-of-type(9):before { content: "Kode"; }
        td:nth-of-type(10):before { content: "Size"; }
        td:nth-of-type(11):before { content: "Berat"; }
        td:nth-of-type(12):before { content: "Harga"; }
        td:nth-of-type(13):before { content: "Gambar"; }
        td:nth-of-type(14):before { content: "Aksi"; }
  }
        
    </style>

</head>
<body>

<!-- header section starts  -->

<header class="header">

    <a href="admin_page.php"> <img class="logo" src="images/Logo.png"> </a>

    <nav class="navbar">
        <ul>
            <li><a href="#">Barang +</a>
                <ul>
                    <li><a href="item_input.php">Input</a></li>
                    <li><a href="size_input2.php">Tambah Size</a></li>
                    <li><a href="item_edit.php">Edit</a></li>
                </ul>
            </li>
            <li><a href="#">Hadiah +</a>
                <ul>
                    <li><a href="prize_input.php">Input</a></li>
                    <li><a href="prize_confirm.php">Konfirmasi</a></li>
                    <li><a href="prize_edit.php">Edit</a></li>
                </ul>
            </li>
            <li><a href="#">Order +</a>
                <ul>
                    <li><a href="order_input.php">Input</a></li>
                    <li><a href="order_confirm.php">Konfirmasi</a></li>
                    <li><a href="order_edit.php">Edit</a></li>
                    <li><a href="menu_print.php">Print Bon</a></li>
                </ul>
            </li>
            <li><a href="#">Event +</a>
                <ul>
                    <li><a href="event1.php">Event 1</a></li>
                    <li><a href="event2.php">Event 2</a></li>
                </ul>
            </li>
            <?php
                if($_SESSION['userid'] == 4){
                    echo '<li><a href="#">Admin Access +</a>
                          <ul>';
                    echo '<li><a href="admin_access.php">Cek Akun</a> </li>';
                    echo '<li><a href="show_order.php">Order online</a> </li>';
                    echo '<li><a href="show_offline_order.php">Order fisik</a> </li>';
                    echo '<li><a href="show_redeem.php">Penukaran Hadiah</a> </li>';
                    echo '<li><a href="price_change.php">Ganti Harga</a> </li>';
                    echo '</ul>
                          </li>';
                }
            ?>
        </ul>
    </nav>

    <div class="icons">
        <div id="menu-btn" class="fas fa-bars"></div> 
    </div>


</header>

<!-- header section ends -->

<!-- banner section starts  -->

<section class="banner">

    <div class="box">
    <h1>Lihat Data Barang</h1>
    
    <div class="box-table">
        <table>
            <tr>
              <th>Id</th>
              <th>Tampil</th>
              <th>Nama</th>
              <th>Hot Deal</th>
              <th>Event</th>
              <th>Supplier</th>
              <th>Kategori</th>
              <th>Warna</th>
              <th>Kode</th>
              <th>Size</th>
              <th>Berat</th>
              <th>Harga</th>
              <th>Gambar</th>
              <th>Aksi</th>
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['attributeid']."</td>");
                if($row['view'] == 1){
                    echo ("<td>Ya</td>");
                }
                else{
                    echo ("<td>Tidak</td>");
                }
                echo ("<td>".$row['name']."</td>");
                echo ("<td>".$row['hot']."</td>");
                echo ("<td>".$row['event']."</td>");
                echo ("<td>".$row['supplier']."</td>");
                echo ("<td>".$row['category']."</td>");
                echo ("<td>".$row['color']."</td>");
                echo ("<td>".$row['code']."</td>");
                echo ("<td>".$row['size']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                
                $filestr = explode("." ,$row['image']);
                $filepath = "./image-data/" . $filestr[0] . ".txt";

                if(file_exists($filepath)){
                    $file = file_get_contents($filepath, true);
                    echo ("<td><img class=\"logo\" src=\"".$file."\"loading=\"lazy\"></td>");
                }
                else{
                    echo ("<td><img class=\"logo\" src=\"item-image/".$row['image']."\"loading=\"lazy\"></td>");
                }
                
                echo("<td>");
                echo('<a style = "background: black; color: white; border-radius: 5px; text-align: center; margin-top: 4px;" href="size_input.php?itemid='.$row['itemid'].'">Tambah Size</a>');
                echo('<a style = "background: black; color: white; border-radius: 5px; text-align: center; margin-top: 4px;" href="item_edit1.php?itemid='.$row['itemid'].'">Edit</a>');
                echo('<a style = "background: black; color: white; border-radius: 5px; text-align: center; margin-top: 4px;" href="item_delete.php?itemid='.$row['itemid'].'">Delete</a>');
                echo ('<button class="share" style = "background: black; color: white; border-radius: 5px; margin-top: 4px; font-size:1.3rem;" id ="'.$row['itemid'].'">Copy Link</button>');
                echo ("</td></tr>");

    
                
                
               }
            ?>
        </table>
    </div>
    </div>

</section>

<!-- banner section ends -->


<script>

var share = document.getElementsByClassName('share');
  for (var i = 0; i < share.length; i++) {
  const btn = share[i];
  btn.addEventListener('click', async () => {
  var info = btn.id;
  const shareData = {
    title: "",
    text: '',
    url: 'https://www.enamitc2.com/product_list.php?id=' + info,
  }
   navigator.share(shareData) });
  }

</script>





</body>
</html>