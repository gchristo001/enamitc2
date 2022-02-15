<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

if (isset($_POST['search'])){
    header("Location: product_list.php?search=".$_POST['search']);
    return;
}

if (isset($_GET['category'])){
    if($_GET['category'] == "Sold out"){
        $stmt = $pdo->query(
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
            WHERE item_attributes.quantity = 0
            GROUP BY 1,2,3
            ORDER BY items.itemid DESC
            LIMIT 60
            ");
        $soldout = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



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
        WHERE item_attributes.quantity != 0 AND items.category = :category AND items.view = 1
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
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
        WHERE item_attributes.quantity != 0 AND items.supplier = :supplier AND items.view = 1
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
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
        WHERE item_attributes.quantity != 0 AND items.event = :event AND items.view = 1
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
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
        WHERE item_attributes.quantity != 0 AND items.hot = :hot AND items.view = 1
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
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
        WHERE item_attributes.quantity != 0  AND items.view = 1
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
        LIMIT 60
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
        WHERE item_attributes.quantity != 0 AND lower(items.name) LIKE lower(:search) AND items.view = 1
        GROUP BY 1,2,3
        ORDER BY items.itemid DESC
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
            echo("<img class=\"myImages\" id=\"myImg\" src=\"item-image/".($item['image'])." \">");
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"weight-size\">".$item['weight']." gr");
            if($item['size']>0){
              echo (" | size: ".$item['size']."</div>");
            }
            else{
              echo("</div>");
            }
            echo("<div class=\"weight-size\"> Id: ".$item['itemid']);
            echo(" | Stok : ".$item['quantity']."</div>");
            echo("<div class=\"price\">".$item['price']." k </div>");
            echo("<form id=\"user-form\" onsubmit = \"return ajaxgo(".$item['itemid'].")\">");
            echo("<input type=\"hidden\" value=\"".$item['itemid']."\" id = \"itemid\">");
            echo("<input type=\"submit\" class=\"btn\" value = \"Beli\" name = \"add\">");
            echo("</form>");
            echo("</div>");
        }  
        
        if($_GET['category'] == "Sold out"){
            foreach ( $soldout as $item ) {
                echo("<div class=\"box\">");
                echo("<img class=\"myImages\" id=\"myImg\" src=\"item-image/".($item['image'])." \" style=\"opacity: 0.6;
                filter: alpha(opacity=60);\">");
                echo("<h3>".$item['name']."</h3>");
                echo("<div class=\"weight-size\">".$item['weight']." gr");
                if($item['size']>0){
                  echo (" | size: ".$item['size']."</div>");
                }
                else{
                  echo("</div>");
                }
                echo("<div class=\"weight-size\"> Id: ".$item['itemid']);
                echo("</div>");
                echo("<div class=\"price\">".$item['price']." k </div>");
                echo("</div>");
            }  
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
<script src="script.js"></script>

</body>
</html>