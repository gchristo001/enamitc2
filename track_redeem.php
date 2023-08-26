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


$sql = "SELECT
prizes.image,
prizes.prizeid,
prizes.name,
prizes.cost,
redeem.redeemid,
redeem.redeemdate,
redeem.status
FROM redeem
LEFT JOIN prizes
ON prizes.prizeid = redeem.prizeid
WHERE redeem.userid = :userid AND (redeem.status = 'approved' OR redeem.status = 'pending')
ORDER BY redeemdate DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':userid' => $_SESSION['userid']));
$redeems = $stmt->fetchALL(PDO::FETCH_ASSOC);


if(isset($_POST['action'])){
    if($_POST['action'] == 'cancel'){
        $stmt = $pdo->prepare("UPDATE redeem SET status = 'Canceled' WHERE redeemid = :redeemid");
        $stmt->execute(array(":redeemid" => $_POST['redeemid']));
        $stmt = $pdo->prepare("UPDATE prizes SET quantity = quantity + 1  WHERE prizeid = :prizeid");
        $stmt->execute(array(":prizeid" => $_POST['prizeid']));
    }
    header("Location: track_redeem.php");
    return;
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penukaran Hadiah</title>

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

        @media (max-width: 600px) {

            .itm-img{
                border-radius: 5px;
                width: 9rem;
                height: 9rem;
            }
          
            table, thead, tbody, th, td, tr { 
                display: block;
            }
            .redeemid {
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
            .gems {
                grid-area: gems;
                text-align: left;
                font-size: 1.8rem;
                font-weight: bold;
                color: #ae9238;
            }
            .nama {
                grid-area: nama;
                text-align: left;
                font-size: 1.9rem;
                font-weight: bold;
                padding-bottom: 0px;
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
                    grid-template-columns: repeat(6, 1fr);
                    grid-auto-rows: minmax(auto, auto);
                    grid-template-areas:
                    "id  id id  st   st   st"
                    "ft ft nama nama nama nama "
                    "ft ft gems gems gems gems"
                    "tgl tgl tgl tgl btn2 btn2";
                    margin: 7px;
                    border-radius: .5rem;
                    border: 0.2rem solid #D8D5CA;
                    background: #FCF8ED;
                }
            .wrapper td:nth-of-type(1):before { content: "Redeem ID : "; }
                
            th{display: none;}
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

<section class="past-redemption">
    
    
    <h1 class="heading"> Penukaran <span>Hadiah</span> </h1>

    <?php
    if (!empty($redeems)){
    echo '<div class="box">
        <div class="table">
        <table>
            <tr>
              <th>RedeemId</th>
              <th>Date</th>
              <th>Nama Hadiah</th>
              <th>Gambar</th>
              <th>Cost</th>
              <th>Status</th> 
              <th>Cancel?</th>   
            </tr>';
            
            
            foreach ($redeems as $row) {
                echo ("<form method=\"post\"onSubmit=\"return confirm('Cancel Redemption?') \">");
                echo ("<input type=\"hidden\" name=\"redeemid\" value=\"".$row['redeemid']."\">");
                echo ("<input type=\"hidden\" name=\"prizeid\" value=\"".$row['prizeid']."\">");
                echo ("<tr class = \"wrapper\">");
                echo ("<td class = \"redeemid\">".$row['redeemid']."</td>");
                echo ("<td class = \"tanggal\">".$row['redeemdate']."</td>");
                echo ("<td class = \"nama\">".$row['name']."</td>");
                echo ("<td class = \"foto\"><img class=\"itm-img\" src=\"prize-image/".$row['image']."\"</td>");
                echo ("<td class = \"gems\">".$row['cost']."   <i class=\"fas fa-gem\"></i> </td>");
                echo ("<td class = \"status\">".$row['status']."</td>");
                if ($row['status'] == 'pending'){
                echo ("<td class = \"btn2\"><input type=\"submit\"name=\"action\" class=\"cancel\"value=\"cancel\"></td>");
                }
                else{
                echo ("<td></td>");
                }
                echo ("</tr>");
                echo ("</form>");
            }
    echo '        
    </table>
    </div>
    
    </div>';
    }
    else{
        echo ('<div class="cart-empty">') ;
        echo ("<h1 style=\"color: white\"> Belum ada penukaran hadiah </h1>");
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
            <a href="https://www.instagram.com/tokomas_6itc2/"> <i class="fab fa-instagram"></i>Main Instagram </a>
            <a href="https://www.instagram.com/tokomas_enamitc2.new/"> <i class="fab fa-instagram"></i>Second Instagram </a>
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