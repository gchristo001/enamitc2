<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}



if (isset($_GET['category'])){
    $stmt = $pdo->prepare(
        " SELECT 
        items.itemid, 
        items.name,
        items.image,
        GROUP_CONCAT(item_attributes.size) as size, 
        FORMAT(max(item_attributes.weight),2) as weight, 
        max(item_attributes.price) as price,
        sum(item_attributes.quantity) as quantity
        FROM items
        LEFT JOIN item_attributes
        ON items.itemid = item_attributes.itemid
        WHERE item_attributes.quantity != 0 AND items.category = :category
        GROUP BY 1,2,3
        ");
    $stmt->execute(array(":category" => $_GET['category']));
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
  
if (isset($_GET['supplier'])){
    $stmt = $pdo->prepare(
        " SELECT 
        items.itemid, 
        items.name,
        items.image,
        GROUP_CONCAT(item_attributes.size) as size, 
        FORMAT(max(item_attributes.weight),2) as weight, 
        max(item_attributes.price) as price,
        sum(item_attributes.quantity) as quantity
        FROM items
        LEFT JOIN item_attributes
        ON items.itemid = item_attributes.itemid
        WHERE item_attributes.quantity != 0 AND items.supplier = :supplier
        GROUP BY 1,2,3
        ");
    $stmt->execute(array(":supplier" => $_GET['supplier']));
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['event'])){
    $stmt = $pdo->prepare(
        " SELECT 
        items.itemid, 
        items.name,
        items.image,
        GROUP_CONCAT(item_attributes.size) as size, 
        FORMAT(max(item_attributes.weight),2) as weight, 
        max(item_attributes.price) as price,
        sum(item_attributes.quantity) as quantity
        FROM items
        LEFT JOIN item_attributes
        ON items.itemid = item_attributes.itemid
        WHERE item_attributes.quantity != 0 AND items.event = :event
        GROUP BY 1,2,3
        ");
    $stmt->execute(array(":event" => $_GET['event']));
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['hot'])){
    $stmt = $pdo->prepare(
        " SELECT 
        items.itemid, 
        items.name,
        items.image,
        GROUP_CONCAT(item_attributes.size) as size, 
        FORMAT(max(item_attributes.weight),2) as weight, 
        max(item_attributes.price) as price,
        sum(item_attributes.quantity) as quantity
        FROM items
        LEFT JOIN item_attributes
        ON items.itemid = item_attributes.itemid
        WHERE item_attributes.quantity != 0 AND items.hot = :hot
        GROUP BY 1,2,3
        ");
    $stmt->execute(array(":hot" => $_GET['hot']));
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['new'])){
    $stmt = $pdo->prepare(
        " SELECT 
        items.itemid, 
        items.name,
        items.image,
        GROUP_CONCAT(item_attributes.size) as size, 
        FORMAT(max(item_attributes.weight),2) as weight, 
        max(item_attributes.price) as price,
        sum(item_attributes.quantity) as quantity
        FROM items
        LEFT JOIN item_attributes
        ON items.itemid = item_attributes.itemid
        WHERE item_attributes.quantity != 0 
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
        LIMIT 50
        ");
    $stmt->execute();
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['search'])){
    $stmt = $pdo->prepare(
        " SELECT 
        items.itemid, 
        items.name,
        items.image,
        GROUP_CONCAT(item_attributes.size) as size, 
        FORMAT(max(item_attributes.weight),2) as weight, 
        max(item_attributes.price) as price,
        sum(item_attributes.quantity) as quantity
        FROM items
        LEFT JOIN item_attributes
        ON items.itemid = item_attributes.itemid
        WHERE item_attributes.quantity != 0 AND lower(items.name) LIKE lower(:search)
        GROUP BY 1,2,3
        ");
    $stmt->execute(array(":search" => "%".$_GET['search']."%"));
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


if (isset($_POST['add'])){
    if ( !in_array ($_POST['itemid'],$_SESSION['cart'])){
        $_SESSION['cart'][] = $_POST['itemid'];
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
    <title>Product List</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">

    <script>
        function ajaxgo(j){
            var data = new FormData();
            data.append("itemid",j);
        

            var xhr = new XMLHttpRequest();
            xhr.open("POST","addcart.php");
            xhr.onload = function(){
                console.log(this.response);
            };

            xhr.send(data);

            

            var oReq = new XMLHttpRequest(); // New request object
            oReq.onload = function() {
            document.getElementById('notif').innerText = this.responseText;
            };
            oReq.open("get", "addcart.php", true);
    
            oReq.send();
            
            return false;
        }

        function reqListener () {
                 console.log(this.responseText);
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
                    <li><a href = "product_list.php?supplier=Bulgari">Bulgari</a></li>
                    <li><a href = "product_list.php?supplier=Ayu">Ayu</a></li>
                    <li><a href = "product_list.php?supplier=SDW">SDW</a></li>
                    <li><a href = "product_list.php?supplier=Hala">Hala</a></li>
                    <li><a href = "product_list.php?supplier=Amero">Amero</a></li>
                    <li><a href = "product_list.php?supplier=MT">MT</a></li>
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


<!-- menu section starts  -->

<section class="menu" id="menu">

    <?php
    if(isset($_GET['category'])){
        echo("<h1 class=\"heading\"> Category <span>". $_GET['category']."  </span> </h1>");
    }

    if(isset($_GET['supplier'])){
        echo("<h1 class=\"heading\"> Supplier <span>". $_GET['supplier']." </span> </h1>");
    }

    if(isset($_GET['new'])){
        echo("<h1 class=\"heading\"> Barang <span> Terbaru </span> </h1>");
    }

    if(isset($_GET['hot'])){
        echo("<h1 class=\"heading\"> Hot <span> Deal </span> </h1>");
    }

    if(isset($_GET['event'])){
        echo("<h1 class=\"heading\"> Event <span> Items </span> </h1>");
    }

    if(isset($_GET['search'])){
        echo("<h1 class=\"heading\"> Hasil <span> Pencarian </span> </h1>");
    }
    
    
    ?>
    <div class="box-container">

    <?php
    
        foreach ( $displayitems as $item ) {
            echo("<div class=\"box\">");
            echo("<img src=\"item-image/".($item['image'])." \">");
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"weight-size\">".$item['weight']." gr");
            if($item['size']>0){
              echo (" | size: ".$item['size']."</div>");
            }
            else{
              echo("</div>");
            }
            echo("<div class=\"weight-size\"> Total : ".$item['quantity']."</div>");
            echo("<div class=\"price\">".$item['price']." k </div>");
            echo("<form id=\"user-form\" onsubmit = \"return ajaxgo(".$item['itemid'].")\">");
            echo("<input type=\"hidden\" value=\"".$item['itemid']."\" id = \"itemid\">");
            echo("<input type=\"submit\" class=\"btn\" value = \"Beli\" name = \"add\">");
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