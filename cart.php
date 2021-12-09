<?php
require_once "pdo.php";
session_start();

if(isset($_GET['del'])){
    $index = array_search ($_GET['del'],$_SESSION['cart']);
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php");
    return;
}

$badge = count($_SESSION['cart']);

if(isset($_POST['checkout'])){
    if(isset($_SESSION['userid'])){
        foreach($_POST['attid'] as $attid){
            date_default_timezone_set('Asia/Jakarta');
            $orderdate = date("Y-m-d H:i:s");
            $sql = "INSERT INTO orders (userid, orderdate, attributeid, status)
            VALUES (:userid, :orderdate, :attributeid, :status)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':userid' => $_SESSION['userid'],
                ':orderdate' => $orderdate,
                ':attributeid' => $attid,
                ':status' => "pending"));

            $sql = "UPDATE item_attributes SET quantity = quantity-1 WHERE attributeid=:attributeid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array( ':attributeid' => $attid));
        }
        $_SESSION['cart'] = array();
        header("Location: track_order.php");
        return;
    }
    else{
        header("Location: login.php");
        return;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>

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


<!-- cart section starts  -->

<section class="shopping-cart">

    <h1 class="heading"> My <span>Cart</span> </h1>

    <form method="post">

        <div class="box-container">

        <?php
            if(!empty($_SESSION['cart'])){
                $total_price = 0;
                for ($i = 0; $i < count($_SESSION['cart']); $i++){
                    
                    
                    $sql = 
                    " SELECT 
                    items.itemid, 
                    items.name,
                    items.image,
                    item_attributes.attributeid,
                    item_attributes.size, 
                    item_attributes.weight, 
                    item_attributes.price,
                    item_attributes.quantity
                    FROM items
                    LEFT JOIN item_attributes
                    ON items.itemid = item_attributes.itemid
                    WHERE item_attributes.itemid = :itemid AND item_attributes.quantity != 0
                    ";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(":itemid" => $_SESSION['cart'][$i]));
                    $cart = $stmt->fetchALL(PDO::FETCH_ASSOC);
                if (isset($cart[0])){
                    echo ('<div class="box">') ;
                    echo ("<a href=\"cart.php?del=".$cart[0]['itemid']." \">");
                    echo ("<i class=\"fas fa-times\"></i>");
                    echo ('</a>');
                    echo ("<img src=\"item-image/".($cart[0]['image'])."\">");
                    echo ('<div class = "content">');
                    echo ("<h3>".$cart[0]['name']."</h3>");
                    echo ("<div class=\"weight-size\">".$cart[0]['weight']." gr | size: ");
                    echo ("<select name=\"attid[]\" id=\"select_size\">");                  

                    for($j = 0 ; $j < count($cart); $j++){
                        echo ("<option value =\"".$cart[$j]['attributeid']."\" >".$cart[$j]['size']."</option>");
                    } 
                    echo ("</select>");
                    
                    echo("</div>");
                    echo ("<div class=\"price\">".$cart[0]['price']." k </div>");
                    echo ('</div>');
                    echo ('</div>');
                    $checkout = "visible";

                    $total_price = $total_price + $cart[0]['price'] ;
                }
                else{
                    unset($_SESSION['cart'][$i]);
                    sort($_SESSION['cart']);
                }
                }  
            }
            else{
                echo ('<div class="cart-empty">') ;
                echo ("<h3> Your cart is empty </h3>");
                echo ("<a href=\"index.php\" class=\"btn\">Continue Shopping</a>");
                echo ('</div>');
                $checkout = "hidden";
            }
        ?>      

        </div>

        <div class="cart-total" style="visibility:<?=$checkout?>">
            <h3>total : <span id="price"><?= $total_price?></span> K</h3>
            <input type="submit" class="btn" name="checkout" value="checkout">
        </div>

    </form>
</section>

<!-- cart section end -->





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