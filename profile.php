<?php
require_once "pdo.php";
session_start();
$totalweight = '0';
$totalprice = '0';
$totalredeem = '0';


if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

if (isset($_POST['search'])){
    header("Location: product_list.php?search=".$_POST['search']);
    return;
}


if(isset($_SESSION['userid'])){
    $stmt = $pdo->prepare("SELECT * FROM users where userid = :xyz");
    $stmt->execute(array(":xyz" => $_SESSION['userid']));
    $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);  
}
else{
    header('Location : login.php');
    return;
}

$sql ="SELECT 
item_attributes.weight as weight,
item_attributes.price as price
FROM orders
LEFT JOIN item_attributes 
ON orders.attributeid = item_attributes.attributeid
WHERE orders.status = 'Approved' AND orders.userid = :userid
UNION ALL 
SELECT
weight as weight,
price as price
from
offline_order
where
offline_order.userid = :userid
";

$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $userinfo['userid']));
$userorder =  $stmt->fetchAll(PDO::FETCH_ASSOC);

  if(!empty($userorder)){
    foreach ($userorder as $row){
        $totalweight = $totalweight + $row['weight'];
        $totalprice = $totalprice + $row['price'];
    }
  }
  else{
        $totalweight = '0';
        $totalprice = '0';
  }

$stmt = $pdo->query("SELECT * FROM prizes WHERE quantity > 0 ORDER BY prizeid DESC");
$prizeitem = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql ="SELECT 
sum(prizes.cost) as cost
FROM redeem
LEFT JOIN prizes 
ON redeem.prizeid = prizes.prizeid
WHERE (redeem.status = 'approved' OR redeem.status = 'pending') AND redeem.userid = :userid
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':userid' => $_SESSION['userid'] ));
$cost = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!empty($cost)){
    foreach ($cost as $row){
        $totalredeem = $totalredeem + $row['cost'];
    }
  }
  else{
        $totalredeem = '0';
  }

  $totalgems = floor($totalweight)-$totalredeem;


  if(isset($_POST['redeem'])){

    if($_POST['cost'] > $totalgems){
        header("Location: profile.php");
        return ;
    }
    else{
        date_default_timezone_set('Asia/Jakarta');
        $redeemdate = date("Y-m-d H:i:s");
        $sql = "INSERT INTO redeem (userid, redeemdate, prizeid, status)
        VALUES (:userid, :redeemdate, :prizeid, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':userid' => $_SESSION['userid'],
            ':redeemdate' => $redeemdate,
            ':prizeid' => $_POST['prizeid'],
            ':status' => "pending"));

        $sql = "UPDATE prizes SET quantity = quantity-1 WHERE prizeid=:prizeid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':prizeid' => $_POST['prizeid']));
        header("Location: track_redeem.php");
        return ;
    }
      
  }

  
$badge = count($_SESSION['cart']);

 //  Ticket

 $sql ="SELECT 
COUNT(orderid) as totalorder
FROM orders 
WHERE status = 'Approved' 
AND userid = :userid
AND orderdate > '2022-01-31 00:00'
AND orderdate < '2022-02-06 00:00'
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $_SESSION['userid']));
$totalorder =  $stmt->fetch(PDO::FETCH_ASSOC);

$sql ="SELECT 
COUNT(id) as totalredeem
FROM ticket_redeem
WHERE userid = :userid
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $_SESSION['userid']));
$totalredeem =  $stmt->fetch(PDO::FETCH_ASSOC);

$ticket = 2*$totalorder['totalorder'] - $totalredeem['totalredeem'];



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">

    <style>
        @media (max-width: 450px) {
            .header .navbar {
                display: none!important;
                visibility: none;
            }
            html {
                font-size: 50%;
            }
            .home .slide .content h3 {
                font-size: 4rem;
            }
            .shopping-cart .box-container .box {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                    -ms-flex-flow: row;
                        flex-flow: row;
            }
            .menu .box-container{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr));
            gap:1.5rem;
            }
            .menu .box-container .box img{
            height: 15rem;
            width: 15rem;
            }
        }
    </style>

    <script>
        function chkredeem(cost){
            var gems = document.getElementById('gems').value;
             if(cost > gems){
                 alert("Gems tidak cukup");
                 return false;
             }
             else{
                 return confirm ("Redeem hadiah seharga " + cost +" gems?");
             }
        }
    </script>



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

<!-- profile section starts  -->

<section class="profile" id="profile">

    <h1 class="heading"> <span>Profil</span> Saya </h1>

    <div class="row">
        <i class="fas fa-user-circle fa-10x" id ="account-img"></i>
        <div class="account-details">
            <h3><?= $userinfo['username']  ?></h3>
            <p>Id: <?= $userinfo['userid'] ?>  |
                
            Member since: <?php 
            $myDateTime = new DateTime($userinfo['member_since']);
            echo  ($myDateTime->format('j M Y')); ?></p>
            <div class="profile-link">
                <a href="profile_edit.php">edit     <i class="fas fa-edit fa-1x"></i></a> 
                <a href="logout.php">logout     <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
        
    </div>
</section>

<!-- profile section ends -->

<!-- order-prize section starts  -->

<section class="order-prize">
    <div class="box-container">
        <div class="box">
            <img src="images/order.jpg" alt="">
            <div class="content">
                <span>Total Order</span>
                <h3>Rp <?= $totalprice?> k</h3>
                <a href="track_order.php" class="btn"style="width:auto;">Lihat Riwayat Order</a>
            </div>
        </div>

        <div class="box">
            <img src="images/gems.jpg" alt="">
            <div class="content">
                <span>My Gems</span>
                <?php echo("<input type=\"hidden\" value=\"".(string)($totalgems)."\" id = \"gems\">"); ?>
                <h3><i class="fas fa-gem"></i> <?php echo ($totalgems)   ?>  </h3>
                <a href="track_redeem.php" class="btn" style="width:auto;">Lihat Penukaran Hadiah Sebelumnya</a>
            </div>
        </div>    
    </div>
</section>

<!-- order-prize section ends  -->

<!-- deal section starts  -->


<section class="deal" id="deal">

    <h1 class="heading"> Imlek <span>Bonanza</span> </h1>

    <div class="row">

        <div class="content">
            <span class="discount" style="font-size: 5rem;">Lucky Draw</span>
            <h3 class="text">Dodol, Cokelat, Permen</h3>
            <!--
            <div class="count-down">
                <div class="box">
                    <h3 id="days">00</h3>
                    <span>days</span>
                </div>
                <div class="box">
                    <h3 id="hours">00</h3>
                    <span>hours</span>
                </div>
                <div class="box">
                    <h3 id="minutes">00</h3>
                    <span>minutes</span>
                </div>
                <div class="box">
                    <h3 id="seconds">00</h3>
                    <span>seconds</span>
                </div>
            </div>
            -->
            <p style="font-size: 1.6rem; text-transform: none;">Setiap pembelian/checkout di web (setelah order status: approved) dari tanggal 31 Januari 2022 <span style="color: #F00">sampai 5 Februari 2022 </span>akan mendapatkan 2 tiket yang bisa digunakan untuk memutar lucky draw*. <br> Raih kesempatan untuk memenangkan logam mulia spesial edisi imlek (0.1gr) bagi pemutar terbanyak* </p>
            <br>

            <p style="font-size: 1rem; text-transform: none;"> *syarat dan ketentuan berlaku dan selama persediaan masih ada </p>
            <br>
            <span style="font-size: 2rem;">Jumlah Ticket</span>
            <h3 style="font-size: 2rem;"><i class="fas fa-ticket-alt"></i> <?php echo($ticket) ;  ?>  </h3>
            <br>
            <a href="roullete.php" class="btn" style="text-align: center;">Lucky Draw</a>
            
        </div>
        
        <div class="image">
            <img src="images/imlek.jpeg" alt="">
        </div>

    </div>

</section>
-->

<!-- deal section ends -->




<!-- menu section starts  -->

<section class="menu" id="menu">

    <h1 class="heading"> List <span>Hadiah</span> </h1>

    <div class="box-container">

    <?php
        foreach ( $prizeitem as $item ) {
            echo("<div class=\"box\">");
            echo("<img src=\"prize-image/".($item['image'])." \">");
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"price\">".$item['cost']." <i class=\"fas fa-gem\"></i> </div>");
            echo("<form id=\"prize-form\" method = \"post\"onsubmit = \"return chkredeem(".$item['cost'].")\">");
            echo("<input type=\"hidden\" value=\"".$item['prizeid']."\" name = \"prizeid\">");
            echo("<input type=\"hidden\" value=\"".$item['cost']."\" name = \"cost\">");
            echo("<input type=\"submit\" class=\"btn\" value = \"redeem\" name = \"redeem\">");
            echo("</form>");
            echo("</div>");
        }

    ?>

    </div>

</section>

<!-- menu section ends -->



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
            <a href = "product_list.php?category=Sold out"><i class="fas fa-angle-right"></i>Sold Out</a>
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
<script src="script.js?<?=filemtime('script.js');?>"></script>

</body>
</html>