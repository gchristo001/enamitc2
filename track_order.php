<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

$badge = count($_SESSION['cart']);

if (isset($_POST['search'])){
    header("Location: product_list.php?search=".$_POST['search']);
    return;
}

if (!isset($_SESSION['userid'])){
    header('Location : index.php');
    return;
}

$sql = "SELECT username FROM users WHERE userid = :userid";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':userid' => $_SESSION['userid']));
$nama_user = $stmt->fetch(PDO::FETCH_ASSOC);



$sql = "SELECT
items.image,
items.name,
item_attributes.size,
item_attributes.attributeid,
item_attributes. weight,
item_attributes. price,
orders.orderid,
orders.orderdate,
orders.status,
orders.admin
FROM orders
LEFT JOIN item_attributes
ON orders.attributeid = item_attributes.attributeid
LEFT JOIN items
ON item_attributes.itemid = items.itemid
WHERE orders.userid = :userid AND (orders.status = 'approved' OR orders.status = 'pending')
ORDER BY orderdate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':userid' => $_SESSION['userid']));
$orders = $stmt->fetchALL(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM offline_order WHERE userid = :userid ORDER BY offline_order_date DESC");
$stmt->execute(array(
    ':userid' => $_SESSION['userid']));
$offline_orders = $stmt->fetchALL(PDO::FETCH_ASSOC);

if(isset($_POST['action'])){
    if($_POST['action'] == 'cancel'){
        $stmt = $pdo->prepare("UPDATE orders SET status = 'Canceled' WHERE orderid = :orderid");
        $stmt->execute(array(":orderid" => $_POST['orderid']));
        $stmt = $pdo->prepare("UPDATE item_attributes SET quantity = quantity + 1  WHERE attributeid = :attributeid");
        $stmt->execute(array(":attributeid" => $_POST['att_id']));
    }
    header("Location: track_order.php");
    return;
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Order</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">

    <style>
        h1{
            color: white;
        }
        .btn{
            width: auto ;
        }
        .itm-img{
        height: 35px;
        width: 35px;
        }

        .tbl .table{
        overflow-x: auto;
        }

        th{
        font-size: 1.5rem;
        padding: 5px;
        color: white;
        background-color: #AE9238;
        }

        tr{
        background-color: rgb(255, 255, 255);
        }


        td{
        padding: 5px 15px;
        font-size: 1.3rem;
        text-align: center;
        margin: none;
        }

        section.past-redemption{
        padding-top: 100px;
        }

        section.past-order{
        padding-top: 100px;
        }

        .past-order .box{
        padding-top: 30px;
        }

        .tab-row{
        display: grid;
        grid-template-columns: 1fr 1fr;
        background-color: #D8D5CA;
        }

        .tab-content{
        text-align: center;
      
        }

        .tab-content a {
        display:inline-block;
        color: black;
        font-size: 2rem;
        width: 100%;
        padding: auto;
        }

        .active{
        background-color: #AE9238;
        }


        @media (max-width: 600px) {
            html {
                font-size: 50%;
               
            }
            .home .slide .content h3 {
                font-size: 4rem;
            }
            label{
                font-size: 12rem;
            }
            
            .itm-img{
                border-radius: 5px;
                width: 10rem;
                height: 10rem;
            }
          
            table, thead, tbody, th, td, tr { 
                display: block;
            }
            .orderid {
                grid-area: id;
                text-align: left;
            }
            .tanggal {
                grid-area: tgl;
                text-align: left;
                font-style: italic;
            }
            .status {
                grid-area: st;
                text-align: right;
                color: blue;
                font-size: 1.5rem;
            }
            .foto {
                grid-area: ft;
            }
            .nama {
                grid-area: nama;
                text-align: left;
                font-size: 1.9rem;
                font-weight: bold;
                padding-bottom: 0px;
            }
            .admin {
                grid-area: adm;
                text-align: left;
                padding-right: 0px;
                padding-top: 0px;
            }
            .size {
                grid-area: sz;
                padding-top: 0px;
                padding-right: 0px;
                padding-left: 0px;
            }
            .berat {
                grid-area: berat;
                text-align: right;
                padding-top: 0px;
            }
            .harga {
                grid-area: harga;
                text-align: left;
                font-size: 1.7rem;
                font-weight: bold;
            }
            .btn1 {
                grid-area: btn1;
                display: flex;
                justify-content: flex-end;
            }
            .btn1 a{
                background-color: #d3ad7f;
                border-radius: 5px;
                text-decoration: none;
                color: black;
                font-size: 1.2rem;
                padding: 5px;
            }
            .btn2 {
                grid-area: btn2;
                display: flex;
                justify-content: flex-end;
            }
            .btn2 input{
                grid-area: btn2;
                background-color: #d3ad7f;
                border-radius: 5px;
                font-size: 1.4rem;
                text-decoration: none;
                color: black;
                padding: 5px;
            }
            
       
            
       
            .wrapper { 
                display: grid;
                grid-template-columns: repeat(9, 1fr);
                grid-auto-rows: minmax(auto, auto);
                grid-template-areas:
                "id  id id  id  id  id  st   st   st"
                "ft ft ft nama nama nama nama nama nama"
                "ft ft ft adm adm sz sz berat berat"
                "ft ft ft harga harga harga harga harga harga"
                "tgl tgl tgl tgl btn1 btn1 btn1 btn2 btn2";
                margin: 7px;
                border-radius: .5rem;
                border: 0.2rem solid #D8D5CA;
                background: #FCF8ED;
            }

            .wrapper td:nth-of-type(1):before { content: "Order ID : "; }
            .wrapper td:nth-of-type(3):before { content: "Admin: "; }
            .wrapper td:nth-of-type(6):before { content: "Sz: "; }
            .wrapper td:nth-of-type(6):before { content: "Sz: "; }
            .wrapper td:nth-of-type(7):after { content: " gr"; }
            .wrapper td:nth-of-type(8):after { content: " K"; }
                
            th{display: none;}

            .wrapper-offline{
            margin: 7px;
            border-radius: .5rem;
            border: 0.2rem solid #D8D5CA;
            background: #FCF8ED;
            }

            .wrapper-offline td { 
            border: none;
            border-bottom: 1px solid #eee; 
            position: relative;
            padding-left: 30%;
            width: auto;
            text-align: left;  
            }

            th{display: none;}
            
            .wrapper-offline td:before { 
            position: absolute;
            top: 6px;
            left: 6px;
            width: 35%; 
            padding-right: 10px; 
            white-space: nowrap;
            font-weight: bold;
            text-align: left;
            }

            .wrapper-offline td:nth-of-type(1):before { content: "Order ID : "; }
            .wrapper-offline td:nth-of-type(2):before { content: "Tanggal : "; }
            .wrapper-offline td:nth-of-type(3):before { content: "Berat : "; }
            .wrapper-offline td:nth-of-type(4):before { content: "Harga: "; }
            .wrapper-offline td:nth-of-type(5):before { content: "No Bon: "; }
            
        }
      




    </style>

</head>
<body>

<!-- header section starts  -->

<header class="header">

    <a href="index.php" class="logo"> <img src="images/Logo.png"> </a>

    <nav class="navbar">
        <ul>
            <li><a href="index.php">HOME</a></li>
            <li><a>BELANJA</a>                
                <ul>
                    <li><a href = "product_list.php?category=Necklace">Kalung</a></li>
                    <li><a href = "product_list.php?category=Bangle">Gelondong</a></li>
                    <li><a href = "product_list.php?category=Bracelet">Gelang</a></li>
                    <li><a href = "product_list.php?category=Ring">Cincin</a></li>
                    <li><a href = "product_list.php?category=Earings">Anting</a></li>
                    <li><a href = "product_list.php?category=Pendant">Liontin</a></li>
                    <li><a href = "product_list.php?category=Kids">Anak</a></li>
                    <li><a href = "product_list.php?category=Dubai gold">Dubai</a></li>
                    <li><a href = "product_list.php?category=Gold bar">Emas Batang</a></li>
                    <li><a href = "product_list.php?category=Sold out">Sold Out</a></li>
                </ul>
            </li>
            <li><a>KOLEKSI</a>
                <ul>
                    <li><a href = "product_list.php?supplier=DeGold">DeGold</a></li>
                    <li><a href = "product_list.php?supplier=UBS">UBS</a></li>
                    <li><a href = "product_list.php?supplier=Citra">Citra</a></li>
                    <li><a href = "product_list.php?supplier=Lotus">Lotus</a></li>
                    <li><a href = "product_list.php?supplier=YT">YT</a></li>
                    <li><a href = "product_list.php?supplier=Kinghalim">Kinghalim</a></li>
                    <li><a href = "product_list.php?supplier=HWT">HWT</a></li>
                    <li><a href = "product_list.php?supplier=BG">BG</a></li>
                    <li><a href = "product_list.php?supplier=Ayu">Ayu</a></li>
                    <li><a href = "product_list.php?supplier=SDW">SDW</a></li>
                    <li><a href = "product_list.php?supplier=Hala">Hala</a></li>
                    <li><a href = "product_list.php?supplier=Hartadinata">Hartadinata</a></li>
                    <li><a href = "product_list.php?supplier=MT">MT</a></li>
                    <li><a href = "product_list.php?supplier=KAP">KAP</a></li>
                    <li><a href = "product_list.php?supplier=Other">Other</a></li>
                </ul>
            </li>
            <li><a href="#footer">TENTANG KAMI</a></li>
        </ul>
    </nav>


    <div class="icons">
        <a href="product_list.php" id="shop-btn" class="fas fa-store"></a>
        <div id="search-btn" class="fas fa-search"></div>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
        <span class="badge" id="notif"><?=$badge?></span>
        <a href="
                <?php
                if (isset($_SESSION['userid'])){
                  echo 'profile.php';
                }
                else {
                  echo 'login.php';
                }
                ?>" class="fas fa-user"></a>
    </div>

   

    <form method = "post" class="search-form">
        <input type="search" name="search" placeholder="search here..." id="search-box">
        <label for="search-box" class="fas fa-search"></label>
    </form>

</header>
<!-- header section ends -->


<!-- order section starts  -->

<section class="past-order">
    
    
    <h1 class="heading"> Riwayat <span>Order</span> </h1>

    <div class="tab-row">
        <div class="tab-content active">
            <a href="javascript:void(0)" onclick="openOrder(event, 'web');">
            Web Order
            </a>
        </div>
        <div class="tab-content">
            <a href="javascript:void(0)" onclick="openOrder(event, 'toko');">
            Onsite Order
            </a>
        </div>
    </div>

    <script>
    function openOrder(evt, orderType) {
        var i, x, tablinks;
        x = document.getElementsByClassName("tab");
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab-content");
        for (i = 0; i < x.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(orderType).style.display = "block";
        evt.currentTarget.parentElement.className += " active";
       
    }
    </script>


    <?php 
        if(!empty($offline_orders)){
            echo '<div class="box tab" id="toko" style="display:none">' ;
            echo '<div class="table">' ;
            echo '  <table>
                    <tr>
                    <th>Id Pembelian di Toko</th>
                    <th>Tanggal</th>
                    <th>Gram</th>
                    <th>Harga</th>
                    <th>Nomor Bon</th>
                    </tr>';
                    foreach ($offline_orders as $row) {
                        echo ("<tr class = \"wrapper-offline\">");
                        echo ("<td>".$row['offline_order_id']."</td>");
                        echo ("<td>".$row['offline_order_date']."</td>");
                        echo ("<td>".$row['weight']."</td>");
                        echo ("<td>".$row['price']."</td>");
                        echo ("<td>".$row['nomor_bon']."</td>");
                        echo ("</tr>");
                    }
            echo '</table>
            </div>
            </div>';
        }
        else{
            echo '<div class="box tab" id="toko" style="display:none" >' ;
            echo ("<h1 style:\"color: white\"> Belum ada order offline</h1>");
            echo ("<a href=\"index.php\" class=\"btn\" style:\"width: auto\">Lanjutkan Belanja</a>");
            echo ('</div>');
        }
   
        if(!empty($orders)){
            echo'<div class="box tab" id="web">
                <div class="table">
                <table>
                <tr>
                <th>OrderId</th>
                <th>Tanggal</th>
                <th>Admin</th>
                <th>Nama Barang</th>
                <th>Gambar</th>
                <th>Size</th>
                <th>Gram</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Cancel?</th>
                <th>Kontak Admin</th>
                </tr>';
                
                
                foreach ($orders as $row) {
                    echo ("<form method=\"post\"onSubmit=\"return confirm('Cancel Order?') \">");
                    echo ("<input type=\"hidden\" name=\"att_id\" value=\"".$row['attributeid']."\">");
                    echo ("<input type=\"hidden\" name=\"orderid\" value=\"".$row['orderid']."\">");
                    echo ("<tr class = \"wrapper\">");
                    echo ("<td class = \"orderid\">".$row['orderid']."</td>");
                    echo ("<td class = \"tanggal\">".$row['orderdate']."</td>");
                    echo ("<td class = \"admin\">".$row['admin']."</td>");
                    echo ("<td class = \"nama\">".$row['name']."</td>");
                    echo ("<td class = \"foto\"><img class=\"itm-img\" src=\"item-image/".$row['image']."\"</td>");
                    echo ("<td class = \"size\">".$row['size']."</td>");
                    echo ("<td class = \"berat\">".$row['weight']."</td>");
                    echo ("<td class = \"harga\">".$row['price']."</td>");
                    echo ("<td class = \"status\">".$row['status']."</td>");
                    if ($row['status'] == 'pending'){
                        switch ($row['admin']) {
                            case 1:
                                $no_admin = "62818188266";
                                break;
                            case 2:
                                $no_admin = "6281882888266";
                                break;
                            case 3:
                                $no_admin = "6283844088866";
                                break;
                            case 4:
                                $no_admin = "628970702600";
                                break;
                            case 5:
                                $no_admin = "628970703600";
                                break;
                            case "tokped":
                                $no_admin = "6281542386048";
                                break;
                            case "shopee":
                                $no_admin = "6289521580866";
                                break;
                            case "IG":
                                $no_admin = "62895429276500";
                                break;
                            default:
                                $no_admin = "";
                          }    

                        $textmessage = "Hi Admin ".$row['admin'].",\nOrder atas nama ".$nama_user["username"]."\n".$row['name']." | ". $row['weight']. "gr | sz:".$row['size']."\nhttps://www.enamitc2.com/login.php?orderid=".$row['orderid'];
                        echo ("<td class = \"btn2\"><input type=\"submit\"name=\"action\" class=\"cancel\"value=\"cancel\"></td>");
                        echo ("<td class = \"btn1\"><a href = \"https://wa.me/".$no_admin."?text=".urlencode($textmessage)."\">Kontak Admin</a></td>");
                    }
                    else{
                        echo("<td></td> <td></td>");
                    }
                    /*
                    if ($row['status'] == 'Approved'&& $row['orderdate']>'2022-01-31 00:00'){
                        echo ("<td>2 <i class=\"fas fa-ticket-alt\"></i></td>");
                    }
                    */
                    echo ("</tr>");
                    echo ("</form>");
                }
                
        echo'</table>
            </div>
            </div>';
    }
    else{
        echo ('<div class="box tab" id="web">') ;
        echo ("<h1 style:\"color: white\"> Belum ada order online </h1>");
        echo ("<a href=\"index.php\" class=\"btn\" style:\"width: auto\">Lanjutkan Belanja</a>");
        echo ('</div>');
    }
?>


</section>

<!-- cart section end -->














<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">

        <div class="box">
            <h3>Kategori</h3>
            <a href = "product_list.php?category=Necklace"><i class="fas fa-angle-right"></i>Kalung</a>
            <a href = "product_list.php?category=Bangle"><i class="fas fa-angle-right"></i>Gelondong</a>
            <a href = "product_list.php?category=Bracelet"><i class="fas fa-angle-right"></i>Gelang</a>
            <a href = "product_list.php?category=Ring"><i class="fas fa-angle-right"></i>Cincin</a>
            <a href = "product_list.php?category=Earings"><i class="fas fa-angle-right"></i>Anting</a>
            <a href = "product_list.php?category=Pendant"><i class="fas fa-angle-right"></i>Liontin</a>
            <a href = "product_list.php?category=Kids"><i class="fas fa-angle-right"></i>Anak</a>
            <a href = "product_list.php?category=Dubai gold"><i class="fas fa-angle-right"></i>Dubai</a>
            <a href = "product_list.php?category=Gold bar"><i class="fas fa-angle-right"></i>Emas Batang</a>
            <a href = "product_list.php?Sold_Out=Sold out"><i class="fas fa-angle-right"></i>Sold Out</a>
        </div>

        <div class="box">
            <h3>Koleksi</h3>
                <div class="footer-link">
                <a href = "product_list.php?supplier=DeGold"><i class="fas fa-angle-right"></i>DeGold</a>
                <a href = "product_list.php?supplier=UBS"><i class="fas fa-angle-right"></i>UBS</a>
                <a href = "product_list.php?supplier=Citra"><i class="fas fa-angle-right"></i>Citra</a>
                <a href = "product_list.php?supplier=Lotus"><i class="fas fa-angle-right"></i>Lotus</a>
                <a href = "product_list.php?supplier=YT"><i class="fas fa-angle-right"></i>YT</a>
                <a href = "product_list.php?supplier=Kinghalim"><i class="fas fa-angle-right"></i>Kinghalim</a>
                <a href = "product_list.php?supplier=HWT"><i class="fas fa-angle-right"></i>HWT</a>
                <a href = "product_list.php?supplier=BG"><i class="fas fa-angle-right"></i>BG</a>
                <a href = "product_list.php?supplier=Ayu"><i class="fas fa-angle-right"></i>Ayu</a>
                <a href = "product_list.php?supplier=SDW"><i class="fas fa-angle-right"></i>SDW</a>
                <a href = "product_list.php?supplier=Hala"><i class="fas fa-angle-right"></i>Hala</a>
                <a href = "product_list.php?supplier=Hartadinata"><i class="fas fa-angle-right"></i>Hartadinata</a>
                <a href = "product_list.php?supplier=MT"><i class="fas fa-angle-right"></i>MT</a>
                <a href = "product_list.php?supplier=KAP"><i class="fas fa-angle-right"></i>KAP</a>
                <a href = "product_list.php?supplier=Other"><i class="fas fa-angle-right"></i>Other</a>
                </div>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="https://shopee.co.id/tokomasenamitc2"> <i class="fab fa-shopify"></i> Shopee </a>
            <a href="https://tokopedia.link/ZPcW84MOcib"> <i class="fas fa-shopping-bag"></i> Tokopedia </a>
            <a href="https://www.instagram.com/tokomas_enamitc2/"> <i class="fab fa-instagram"></i> Instagram </a>
            <a href="https://wa.me/62818188266"> <i class="fab fa-whatsapp"></i> Whatsapp</a>
        </div>

        <div class="box" id="footer">
            <h3>Tentang Kami</h3>
            <p>Berdiri sejak 2004,
            Toko Mas 6 ITC 2 bagian dari toko mas 6 group.
            Menyediakan perhiasan model terbaru dengan kadar 70 - 100 % (mas tua kadar internasional)
            Kami terus menyediakan layanan terbaik bagi pelanggan kami dengan harga yang bersaing, tanpa ongkos.
            Perhiasan dapat dijual kembali dengan potongan super ekonomis.
            Kami juga menerima layanan servis perhiasan seperti cuci, patri dan pesanan perhiasan dengan kustomisasi khusus.
            Kami percaya anda dapat tampil modis selagi berinvestasi<br><br></p>
           <p><i class="fas fa-map-marker-alt"></i>  ITC Kebun Kelapa Lantai SB 01-03, Jl. Moh. Toha, Pungkur, Regol, Bandung City, West Java 40252, Indonesia </p>
           <p><i class="far fa-clock"></i>  Senin - Sabtu 09:00 - 16:00 </p>
        </div>

    </div>

    <div class="credit"> created by <span>GC</span> | all rights reserved </div>

</section>
<!-- footer section ends -->



<!-- custom js file link -->
<script src="script.js"></script>

</body>
</html>