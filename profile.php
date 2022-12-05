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

$stmt = $pdo->query("SELECT * FROM prizes WHERE quantity > 0 ORDER BY cost");
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
/*
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
*/
$sql ="
    SELECT 
        SUM(weight) as totalgram
    FROM 
        orders
        left join item_attributes on orders.attributeid = item_attributes.attributeid
    WHERE 
        status = 'Approved' 
        AND userid = :userid
        AND orderdate >= '2022-12-12 00:00'
        AND orderdate < '2023-01-01 00:00'
";

$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $_SESSION['userid']));
$online_order =  $stmt->fetch(PDO::FETCH_ASSOC);

$sql ="
    SELECT 
        SUM(weight) as totalgram
    from 
        offline_order
    WHERE 
        userid = :userid
        AND offline_order_date >= '2022-12-12 00:00'
        AND offline_order_date < '2023-01-01 00:00'
        AND nomor_bon != '5Gems'
        AND nomor_bon != '20Gems'
        AND nomor_bon != '70Gems'
";

$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $_SESSION['userid']));
$offline_order =  $stmt->fetch(PDO::FETCH_ASSOC);


$total_gram = $offline_order["totalgram"] + $online_order["totalgram"]; 
$progress = $total_gram*2;

if($progress >= 100){
    $progress = 100;
}

if($progress == 100){
    $sql =" SELECT * FROM offline_order WHERE 
        userid = :userid
        AND offline_order_date >= '2022-12-12 00:00'
        AND offline_order_date < '2023-01-01 00:00'
        AND nomor_bon = '70Gems'
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':userid' => $_SESSION['userid']));
    $prize3 =  $stmt->fetch(PDO::FETCH_ASSOC);
    if(empty($prize3)){
        date_default_timezone_set('Asia/Jakarta');
        $inputdate = date("Y-m-d H:i:s");
    
        $sql = "INSERT INTO offline_order (offline_order_date, userid, weight, price, nomor_bon)
                  VALUES (:offline_order_date, :userid, :weight, :price, :nomor_bon)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':offline_order_date' => $inputdate,
            ':userid' => $_SESSION['userid'],
            ':weight' => 70,
            ':price' => 0,
            ':nomor_bon' => '70Gems'));
    }
}
if($progress>= 30){
    $sql =" SELECT * FROM offline_order WHERE 
        userid = :userid
        AND offline_order_date >= '2022-12-12 00:00'
        AND offline_order_date < '2023-01-01 00:00'
        AND nomor_bon = '20Gems'
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':userid' => $_SESSION['userid']));
    $prize1 =  $stmt->fetch(PDO::FETCH_ASSOC);
    if(empty($prize1)){
        date_default_timezone_set('Asia/Jakarta');
        $inputdate = date("Y-m-d H:i:s");
    
        $sql = "INSERT INTO offline_order (offline_order_date, userid, weight, price, nomor_bon)
                  VALUES (:offline_order_date, :userid, :weight, :price, :nomor_bon)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':offline_order_date' => $inputdate,
            ':userid' => $_SESSION['userid'],
            ':weight' => 20,
            ':price' => 0,
            ':nomor_bon' => '20Gems'));
    }
}
if($progress>= 10){
    $sql =" SELECT * FROM offline_order WHERE 
        userid = :userid
        AND offline_order_date >= '2022-12-12 00:00'
        AND offline_order_date < '2023-01-01 00:00'
        AND nomor_bon = '5Gems'
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            ':userid' => $_SESSION['userid']));
    $prize1 =  $stmt->fetch(PDO::FETCH_ASSOC);
    if(empty($prize1)){
        date_default_timezone_set('Asia/Jakarta');
        $inputdate = date("Y-m-d H:i:s");
    
        $sql = "INSERT INTO offline_order (offline_order_date, userid, weight, price, nomor_bon)
                  VALUES (:offline_order_date, :userid, :weight, :price, :nomor_bon)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':offline_order_date' => $inputdate,
            ':userid' => $_SESSION['userid'],
            ':weight' => 5,
            ':price' => 0,
            ':nomor_bon' => '5Gems'));
    }
}


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
    <link rel="stylesheet" href="style.css?<?=filemtime('style.css');?>">
    <link rel="stylesheet" href="christmas.css?<?=filemtime('christmas.css');?>">

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

    
        /* Style the Image Used to Trigger the Modal */
#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 200px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (Image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image (Image Text) - Same Width as the Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation - Zoom in the Modal */
.modal-content, #caption {
  animation-name: zoom;
  animation-duration: 0.6s;
}

@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 100px;
  right: 20px;
  color: #f1f1f1;
  font-size: 80px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
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
                <?php
                if ($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) {
                    echo '<a href="admin_page.php">Admin Page     <i class="fas fa-user fa-1x"></i></a>';
                }
                ?>
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

<!-- deal section starts  


<section class="deal" id="deal">

    <h1 class="heading"> Imlek <span>Bonanza</span> </h1>

    <div class="row">

        <div class="content">
            <span class="discount" style="font-size: 5rem;">Lucky Draw</span>
            <h3 class="text">Dodol, Cokelat, Permen</h3>
            
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
            
            <p style="font-size: 1.6rem; text-transform: none;">Selamat kepada Shanti Kurniawan sudah memenangkan LM edisi imlek sebagai pemutar terbanyak di event kali ini!</p>
            <br>
            <p style="font-size: 1.6rem; text-transform: none;">Event Imlek Bonanza tela selesai. Terimakasih atas partisipasinya dan ditunggu di event selanjunya :) </p>
            <br>
            
        </div>
        
        <div class="image">
            <img src="images/imlek.jpeg" alt="">
        </div>

    </div>

</section>


deal section ends -->




<div class="christmas-card">

<div class="checkpoints">
	<div id="checkpoint1">
		<div class="checkpoint_content">
			<p> 5 gr </p>
			<img id="chk1" class="chkpt_img" src="christmas/chk1.png">
			<p> +5 <i class="fas fa-gem"></i></p>
		</div>
	</div>
	<div id="checkpoint2">
		<div class="checkpoint_content">
			<p> 15 gr </p>
			<img id="chk2" class="chkpt_img" src="christmas/chk1.png">
			<p> +20 <i class="fas fa-gem"></i></p>
		</div>
	</div>
	<div id="checkpoint3">
		<div class="checkpoint_content">
			<p> 50 gr </p>
			<img id="chk3" class="chkpt_img" src="christmas/chk1.png">
			<p> +70 <i class="fas fa-gem"></i></p>
		</div>
	</div>
</div>


<div class="progress">
	<div class="progress-done" data-done=<?= $progress?>>
		<img src="christmas/sleigh.png" class="progress_icon">
	</div>
</div>


<div id="indicator">
    <p>Progress : <span style="color: #AE9238; font-weight: bold;"><?= round($total_gram,2);?> gr</span></p>
</div>


</div>

<script>
const progress = document.querySelector('.progress-done');

progress.style.width = progress.getAttribute('data-done') + '%';
progress.style.opacity = 1;


if (progress.getAttribute('data-done')>= 10){
	document.getElementById('chk1').src = "christmas/open.png"
}
if (progress.getAttribute('data-done')>= 30){
	document.getElementById('chk2').src = "christmas/open.png"
}
if (progress.getAttribute('data-done')>= 100){
	document.getElementById('chk3').src = "christmas/open.png"
}

</script>
<!-- menu section starts  -->

<section class="menu" id="menu">

    <h1 class="heading"> List <span>Hadiah</span> </h1>

    <div class="box-container">

    <?php
        foreach ( $prizeitem as $item ) {
            echo("<div class=\"box\">");
            echo("<img class=\"myImages\" id=\"myImg\" src=\"prize-image/".($item['image'])." \">");
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

<!-- The Modal -->
<div id="myModal" class="modal">

<!-- The Close Button -->
<span class="close">&times;</span>

<!-- Modal Content (The Image) -->
<img class="modal-content" id="img01">

<!-- Modal Caption (Image Text) -->
<div id="caption"></div>

</div>

<script>
   // create references to the modal...
    var modal = document.getElementById('myModal');
    // to all images -- note I'm using a class!
    var images = document.getElementsByClassName('myImages');
    // the image in the modal
    var modalImg = document.getElementById("img01");
    // and the caption in the modal
    var captionText = document.getElementById("caption");

// Go through all of the images with our custom class
for (var i = 0; i < images.length; i++) {
  var img = images[i];
  // and attach our click listener for this image.
  img.onclick = function(evt) {
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
  }
}

var span = document.getElementsByClassName("close")[0];

span.onclick = function() {
  modal.style.display = "none";
}
</script>


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
<script src="script.js?<?=filemtime('script.js');?>"></script>

</body>
</html>