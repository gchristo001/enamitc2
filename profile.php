<?php
require_once "pdo.php";
session_start();

if(isset($_SESSION['userid'])){
    $stmt = $pdo->prepare("SELECT * FROM users where userid = :xyz");
    $stmt->execute(array(":xyz" => $_SESSION['userid']));
    $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);
  
    $stmt = $pdo->prepare("
    SELECT
      items.name,
      items.weight,
      items.price,
      orders.orderdate
     FROM items
     LEFT JOIN orders ON items.orderid = orders.orderid
     where userid = :xyz
     ORDER BY orders.orderid");
      $stmt->execute(array(":xyz" => $_SESSION['userid']));
    $userorder = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
else{
    header('Location : index.php');
    return;
}
  
foreach ($userorder as $row){
       $totalweight = $totalweight + $row['weight'];
       $totalprice = $totalprice + $row['price'];
}

$stmt = $pdo->query("SELECT * FROM prizes");
$prizeitem = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">


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
                    <li><a href = "product_list.php?supplier=SJW">SJW</a></li>
                    <li><a href = "product_list.php?supplier=Hala">Hala</a></li>
                    <li><a href = "product_list.php?supplier=Amero">Amero</a></li>
                    <li><a href = "product_list.php?supplier=MT">MT</a></li>
                </ul>
            </li>
            <li><a href="#footer">ABOUT US</a></li>
        </ul>
    </nav>

    <div class="icons">
        <div id="menu-btn" class="fas fa-bars"></div>
        <div id="search-btn" class="fas fa-search"></div>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
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
            <h3><?= $userinfo['name'] ?></h3>
            <p>Member since: 2002</p>
            <div class="profile-link">
                <a href="update_account.php">edit     <i class="fas fa-edit fa-1x"></i></a>
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
                <a href="#" class="btn">View Past Order</a>
            </div>
        </div>

        <div class="box">
            <img src="images/gems.jpg" alt="">
            <div class="content">
                <span>My Gems</span>
                <h3><i class="fas fa-gem"></i> 125</h3>
                <a href="#" class="btn">View Redeemed Prize</a>
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
            echo('<img src="data:image/jpeg;base64,'.base64_encode( $item['image'] ).'"/>');
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"price\">".$item['cost']." <i class=\"fas fa-gem\"></i> </div>");
            echo("<a href=\"#\" class=\"btn\">redeem</a>");
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
                <a href = "product_list.php?supplier=SJW"><i class="fas fa-angle-right"></i>SJW</a>
                <a href = "product_list.php?supplier=Hala"><i class="fas fa-angle-right"></i>Hala</a>
                <a href = "product_list.php?supplier=Amero"><i class="fas fa-angle-right"></i>Amero</a>
                <a href = "product_list.php?supplier=MT"><i class="fas fa-angle-right"></i>MT</a>
                </div>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="https://en-gb.facebook.com/tokomasenamitc2/?ref=page_internal"> <i class="fab fa-facebook-f"></i> facebook </a>
            <a href="https://www.instagram.com/tokomas_enamitc2/"> <i class="fab fa-instagram"></i> instagram </a>
            <a href="https://wa.me/62818188266"> <i class="fab fa-whatsapp"></i> whatsapp 1 </a>
            <a href="http://wa.me/6281882888266"> <i class="fab fa-whatsapp"></i> whatsapp 2 </a>
            <a href="http://wa.me/6283844088866"> <i class="fab fa-whatsapp"></i> whatsapp 3 </a>
            <a href="http://wa.me/628970702600"> <i class="fab fa-whatsapp"></i> whatsapp 4 </a>
            <a href="http://wa.me/628970703600"> <i class="fab fa-whatsapp"></i> whatsapp 5 </a>
            <a href="http://wa.me/62818202963"> <i class="fas fa-phone"></i> Customer Service </a>
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