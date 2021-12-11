<?php
require_once "pdo.php";
session_start();
$totalweight = '0';
$totalprice = '0';
$totalredeem = '0';


if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}


if(isset($_SESSION['userid'])){
    $stmt = $pdo->prepare("SELECT * FROM users where userid = :xyz");
    $stmt->execute(array(":xyz" => $_SESSION['userid']));
    $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);  
}
else{
    header('Location : index.php');
    return;
}

$sql ="SELECT 
item_attributes.weight as weight1,
item_attributes.price as price1,
offline_order.weight as weight2,
offline_order.price as price2
FROM orders
LEFT JOIN item_attributes 
ON orders.attributeid = item_attributes.attributeid
LEFT JOIN offline_order
ON orders.userid = offline_order.userid
WHERE orders.status = 'Approved' AND orders.userid = :userid";

$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $userinfo['userid']));
$userorder =  $stmt->fetchAll(PDO::FETCH_ASSOC);

  if(!empty($userorder)){
    foreach ($userorder as $row){
        $totalweight = $totalweight + $row['weight1']+ $row['weight2'];
        $totalprice = $totalprice + $row['price1']+ $row['price2'];
    }
  }
  else{
        $totalweight = '0';
        $totalprice = '0';
  }

$stmt = $pdo->query("SELECT * FROM prizes WHERE quantity > 0");
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">


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
            <li><a>SHOP CATEGORIES</a>                
                <ul>
                    <li><a href = "product_list.php?category=Necklace">Necklace</a></li>
                    <li><a href = "product_list.php?category=Bangle">Bangle</a></li>
                    <li><a href = "product_list.php?category=Bracelet">Bracelet</a></li>
                    <li><a href = "product_list.php?category=Ring">Ring</a></li>
                    <li><a href = "product_list.php?category=Earings">Earings</a></li>
                    <li><a href = "product_list.php?category=Pendant">Pendant</a></li>
                    <li><a href = "product_list.php?category=Kids">Kids</a></li>
                    <li><a href = "product_list.php?category=Dubai gold">Dubai</a></li>
                    <li><a href = "product_list.php?category=Gold bar">Gold bar</a></li>
                </ul>
            </li>
            <li><a>COLLECTION</a>
                <ul>
                    <li><a href = "product_list.php?supplier=DeGold">DeGold</a></li>
                    <li><a href = "product_list.php?supplier=UBS">UBS</a></li>
                    <li><a href = "product_list.php?supplier=Citra">Citra</a></li>
                    <li><a href = "product_list.php?supplier=Lotus">Lotus</a></li>
                    <li><a href = "product_list.php?supplier=YT">YT</a></li>
                    <li><a href = "product_list.php?supplier=Kinghalim">Kinghalim</a></li>
                    <li><a href = "product_list.php?supplier=HWT">HWT</a></li>
                    <li><a href = "product_list.php?supplier=Bulgari">Bulgari</a></li>
                    <li><a href = "product_list.php?supplier=Ayu">Ayu</a></li>
                    <li><a href = "product_list.php?supplier=SDW">SDW</a></li>
                    <li><a href = "product_list.php?supplier=Hala">Hala</a></li>
                    <li><a href = "product_list.php?supplier=Amero">Amero</a></li>
                    <li><a href = "product_list.php?supplier=MT">MT</a></li>
                </ul>
            </li>
            <li><a href="#footer">ABOUT US</a></li>
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

    <form action="" class="search-form">
        <input type="search" name="" placeholder="search here..." id="search-box">
        <label for="search-box" class="fas fa-search"></label>
    </form>

</header>
<!-- header section ends -->

<!-- profile section starts  -->

<section class="profile" id="profile">

    <h1 class="heading"> <span>My</span> Profile </h1>

    <div class="row">
        <i class="fas fa-user-circle fa-10x" id ="account-img"></i>
        <div class="account-details">
            <h3><?= $userinfo['username']  ?></h3>
            <p>Id: <?= $userinfo['userid'] ?>  |
                
            Member since: <?php 
            $myDateTime = new DateTime($userinfo['member_since']);
            echo  ($myDateTime->format('j M Y')); ?></p>
            <div class="profile-link">
                <!--<a href="update_account.php">edit     <i class="fas fa-edit fa-1x"></i></a> -->
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
                <a href="track_order.php" class="btn">View Past Order</a>
            </div>
        </div>

        <div class="box">
            <img src="images/gems.jpg" alt="">
            <div class="content">
                <span>My Gems</span>
                <?php echo("<input type=\"hidden\" value=\"".(string)($totalgems)."\" id = \"gems\">"); ?>
                <h3><i class="fas fa-gem"></i> <?php echo ($totalgems)   ?>  </h3>
                <a href="track_redeem.php" class="btn">View Redeemed Prize</a>
            </div>
        </div>    
    </div>
</section>

<!-- order-prize section ends  -->

<!-- menu section starts  -->

<section class="menu" id="menu">

    <h1 class="heading"> Prize <span>Catalog</span> </h1>

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
            <h3>Shop Categories</h3>
            <a href = "product_list.php?category=Necklace"><i class="fas fa-angle-right"></i>Necklace</a>
            <a href = "product_list.php?category=Bangle"><i class="fas fa-angle-right"></i>Bangle</a>
            <a href = "product_list.php?category=Bracelet"><i class="fas fa-angle-right"></i>Bracelet</a>
            <a href = "product_list.php?category=Ring"><i class="fas fa-angle-right"></i>Ring</a>
            <a href = "product_list.php?category=Earings"><i class="fas fa-angle-right"></i>Earings</a>
            <a href = "product_list.php?category=Pendant"><i class="fas fa-angle-right"></i>Pendant</a>
            <a href = "product_list.php?category=Kids"><i class="fas fa-angle-right"></i>Kids</a>
            <a href = "product_list.php?category=Dubai gold"><i class="fas fa-angle-right"></i>Dubai</a>
            <a href = "product_list.php?category=Gold bar"><i class="fas fa-angle-right"></i>Gold bar</a>
        </div>

        <div class="box">
            <h3>Collection</h3>
                <div class="footer-link">
                <a href = "product_list.php?supplier=DeGold"><i class="fas fa-angle-right"></i>DeGold</a>
                <a href = "product_list.php?supplier=UBS"><i class="fas fa-angle-right"></i>UBS</a>
                <a href = "product_list.php?supplier=Citra"><i class="fas fa-angle-right"></i>Citra</a>
                <a href = "product_list.php?supplier=Lotus"><i class="fas fa-angle-right"></i>Lotus</a>
                <a href = "product_list.php?supplier=YT"><i class="fas fa-angle-right"></i>YT</a>
                <a href = "product_list.php?supplier=Kinghalim"><i class="fas fa-angle-right"></i>Kinghalim</a>
                <a href = "product_list.php?supplier=HWT"><i class="fas fa-angle-right"></i>HWT</a>
                <a href = "product_list.php?supplier=Bulgari"><i class="fas fa-angle-right"></i>Bulgari</a>
                <a href = "product_list.php?supplier=Ayu"><i class="fas fa-angle-right"></i>Ayu</a>
                <a href = "product_list.php?supplier=SDW"><i class="fas fa-angle-right"></i>SDW</a>
                <a href = "product_list.php?supplier=Hala"><i class="fas fa-angle-right"></i>Hala</a>
                <a href = "product_list.php?supplier=Amero"><i class="fas fa-angle-right"></i>Amero</a>
                <a href = "product_list.php?supplier=MT"><i class="fas fa-angle-right"></i>MT</a>
                </div>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="https://shopee.co.id/tokomasenamitc2"> <i class="fab fa-shopify"></i> Shopee </a>
            <a href="https://tokopedia.link/ZPcW84MOcib"> <i class="fas fa-shopping-bag"></i> Tokopedia </a>
            <a href="https://www.instagram.com/tokomas_enamitc2/"> <i class="fab fa-instagram"></i> instagram </a>
            <a href="https://wa.me/62818188266"> <i class="fab fa-whatsapp"></i> whatsapp 1 </a>
        </div>

        <div class="box">
            <h3>About Us</h3>
            <p>Established since 2004,
           Providing the latest model of jewelry with 70-100% grade (international grade old gold).
           We continue to provide the best service for our customers at competitive prices, no fees.
           We also accept jewelry services such as washing, soldering and custom jewelry orders.
           Jewelry can be resold at a super economical cut.
           We believe you can look fashionable while investing.
           Let's beautify while saving.<br><br></p>
           <p><i class="fas fa-map-marker-alt"></i>  itc kebon kalapa lt. dasar blok a2 no 7,8,9,16 </p>
           <p><i class="far fa-clock"></i>  Monday - Saturday 09:00 - 16:00 </p>
        </div>

    </div>

    <div class="credit"> created by <span>GC</span> | all rights reserved </div>

</section>
<!-- footer section ends -->



<!-- custom js file link -->
<script src="script.js"></script>

</body>
</html>