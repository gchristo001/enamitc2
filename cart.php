<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}


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
                    <li><a href = "product_list.php?category=Necklace">Kalung</a></li>
                    <li><a href = "product_list.php?category=Bangle">Gelondong</a></li>
                    <li><a href = "product_list.php?category=Bracelet">Gelang</a></li>
                    <li><a href = "product_list.php?category=Ring">Cincin</a></li>
                    <li><a href = "product_list.php?category=Earings">Anting</a></li>
                    <li><a href = "product_list.php?category=Pendant">Liontin</a></li>
                    <li><a href = "product_list.php?category=Kids">Anak</a></li>
                    <li><a href = "product_list.php?category=Dubai gold">Dubai</a></li>
                    <li><a href = "product_list.php?category=Gold bar">Emas Batang</a></li>
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

    <?php
    if (isset($_POST['search'])){
        header("Location: product_list.php?search=".$_POST['search']);
        return;
    }
    ?>

    <form method = "post" class="search-form">
        <input type="search" name="search" placeholder="search here..." id="search-box">
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
                echo ("<h3> Cart Kosong </h3>");
                echo ("<a href=\"index.php\" class=\"btn\">Lanjutkan Belanja</a>");
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
           <p><i class="fas fa-map-marker-alt"></i>  itc kebon kalapa lt. dasar blok a2 no 7,8,9,16 </p>
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