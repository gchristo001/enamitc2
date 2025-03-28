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

$stmt = $pdo->query(
    " SELECT * FROM advert WHERE slot != 0 "
);
$adverts = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    WHERE item_attributes.quantity != 0 AND items.view = 1
    GROUP BY 1,2,3
    ORDER BY items.itemid DESC
    LIMIT 8");
$newitem = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    WHERE item_attributes.quantity != 0
    GROUP BY 1,2,3
    ORDER BY Rand()
    LIMIT 8");
$randomitem = $stmt->fetchAll(PDO::FETCH_ASSOC);

$badge = count($_SESSION['cart']);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Mas Enam ITC 2</title>
    <script src="https://kit.fontawesome.com/67318e12e0.js" crossorigin="anonymous"></script>
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css?<?=filemtime('style.css');?>">


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

<!-- home section starts  -->

<section class="home">

    <div class="slide active" style="background: url(images/home-wallpaper.png) no-repeat;">
    </div>

    <div class="slide" style="background: url(images/banner5.png) no-repeat;">
        <div class="content">
            <!--<span>Harga Spesial</span>-->
            <h3>Best Seller Item</h3>
            <a href="product_list.php?event=3" class="btn">Lihat</a>
        </div>
    </div>

<!--
    <div class="slide" style="background: url(images/bst.jpeg) no-repeat;">
        <div class="content">
            <span style="color: #000">Celebrate Your Birth Month</span>
            <h3 style="color: #000">Birth Stone</h3>
            <a href="product_list.php?event=1" class="btn">Lihat</a>
        </div>
    </div>
-->
    <div id="next-slide" onclick="next()" class="fas fa-angle-right"></div>
    <div id="prev-slide" onclick="prev()" class="fas fa-angle-left"></div>

</section>

<!-- home section ends     -->

<!-- category section starts  -->


<section class="category">

    <a href="FaQ.php" class="btn">Info</a>

    <h1 class="heading"> Lihat <span>Produk</span> </h1>

    <div class="box-container">

        <div class="box">
            <img src="images/banner1.jpg" alt="">
            <div class="content">
                <span>New</span>
                <h3>Items</h3>
                <a href="product_list.php?new=1" class="btn">Lihat</a>
            </div>
        </div>

        <div class="box">
            <img src="images/walpaper.jpg" alt="">
            <div class="content">
                <span>Hottest</span>
                <h3>Deal</h3>
                <a href="product_list.php?hot=1" class="btn">Lihat</a>
            </div>
        </div>

        <div class="box">
            <img src="images/banner5.png" alt="">
            <div class="content">
                <span>BestSeller</span>
                <h3>Item</h3>
                <a href="product_list.php?event=3" class="btn">Lihat</a>
            </div>
        </div>
        
    </div>

</section>

<!-- category section ends -->

<!--<div class="video"> 
        <iframe  width="1080" height="720" src="https://www.youtube.com/embed/rcSxMPKSQuU" title="Lavani by Amero Jewellery x Purana" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>-->

<!-- deal section starts  -->


<?php
    if(!empty($adverts)){
        echo("<section class=\"deal\" id=\"deal\">");
        echo("<h1 class=\"heading\"> Special <span>Event</span> </h1>");
    } 
        foreach ( $adverts as $adv ) {
            echo("<div class=\"row\">");

            echo("<div class=\"content\">");
            echo("<span class=\"discount\" style=\"font-size: 3rem;\">".$adv["title"]."</span>");
            echo("<h3 class=\"desc\" style=\"font-size: 1.7rem;\">".$adv["description"]."</h3>");
            echo("</div>");

            echo("<div class=\"image\">");
            echo("<img src=\"iklan/".$adv["image"]."\">");
            echo("</div>");

            echo("</div>");
            echo("<br><br>");
        }
    if(!empty($adverts)){
        echo("</section>");
    } 
?>

<!-- deal section ends -->


<!-- custom js file link -->
<script src="script.js?<?=filemtime('script.js');?>"></script>

<!-- menu section starts  -->

<section class="menu" id="menu">

    <h1 class="heading"> New <span>Items</span> </h1>

    <div class="box-container">

    <?php
        foreach ( $newitem as $item ) {
            echo("<div class=\"box\">");
            
            $filestr = explode("." ,$item['image']);
            $filepath = "../image-data/" . $filestr[0] . ".txt";

            if(file_exists($filepath)){
                $file = file_get_contents($filepath, true);
                echo ("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"".$file." \"loading=\"lazy\">");
            }
            else{
                echo("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"../item-image/".($item['image'])." \"loading=\"lazy\">");
            }
            
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"weight-size\">".$item['weight']." gr");
            if($item['size']>0){
              echo (" | size: ".$item['size']."</div>");
            }
            else{
              echo("</div>");
            }
            echo("<div class=\"weight-size\"> Stok : ".$item['quantity']);
            echo(" | <button style = \"background: transparent; color: orange;\" class = \"share\" id = \"".$item['itemid']."+".$item['name']."+".$item['weight']."+".$item['price']."+".$item['size']."+".$item['image']."\"> Share <i class=\"fa-solid fa-arrow-up-from-bracket\"></i> </button> </div>");
            echo("<div class=\"price\">".$item['price']." k </div>");
            echo("<form id=\"user-form\" onsubmit = \"return ajaxgo(".$item['itemid'].")\">");
            echo("<input type=\"hidden\" value=\"".$item['itemid']."\" id = \"itemid\">");
            echo("<input type=\"submit\" class=\"btn\" value = \"Beli\" name = \"add\">");
            echo("</form>");
            echo("</div>");
        }    
    ?>
    </div>
    <div style= "display: flex; flex-direction: column; justify-content: center; margin-top: 12px; padding: 2px;">
    <a style= "margin: auto; text-align: center; width: auto;" href="product_list.php?new=1" class="btn">Lihat Lebih</a>
    </div>

</section>

<!-- menu section ends -->


<!-- menu section starts  -->

<section class="menu" id="menu">

    <h1 class="heading"> Featured <span>Items</span> </h1>

    <div class="box-container">

    <?php
        foreach ( $randomitem as $item ) {
            echo("<div class=\"box\">");
            $filestr = explode("." ,$item['image']);
            $filepath = "../image-data/" . $filestr[0] . ".txt";

            if(file_exists($filepath)){
                $file = file_get_contents($filepath, true);
                echo ("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"".$file." \"loading=\"lazy\">");
            }
            else{
                echo("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"../item-image/".($item['image'])." \" loading=\"lazy\">");
            }
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"weight-size\">".$item['weight']." gr");
            if($item['size']>0){
              echo (" | size: ".$item['size']."</div>");
            }
            else{
              echo("</div>");
            }
            echo("<div class=\"weight-size\"> Stok : ".$item['quantity']);
            echo(" | <button style = \"background: transparent; color: orange;\" class = \"share\" id = \"".$item['itemid']."+".$item['name']."+".$item['weight']."+".$item['price']."+".$item['size']."+".$item['image']."\"> Share <i class=\"fa-solid fa-arrow-up-from-bracket\"></i> </button> </div>");
            echo("<div class=\"price\">".$item['price']." k </div>");
            echo("<form id=\"user-form\" onsubmit = \"return ajaxgo(".$item['itemid'].")\">");
            echo("<input type=\"hidden\" value=\"".$item['itemid']."\" name = \"itemid\"id = \"itemid\">");
            echo("<input type=\"submit\" class=\"btn\" value = \"Beli\" name = \"add\">");
            echo("</form>");
            echo("</div>");
        }    
    ?>

    </div>

</section>

<!-- menu section ends -->

<script>

    function getDataUrl(img) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = img.width;
    canvas.height = img.height;
    ctx.drawImage(img, 0, 0);
    return canvas.toDataURL('image/jpeg');
    }
    function dataURLtoBlob(dataurl) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
    }
    
  var share = document.getElementsByClassName('share');
  for (var i = 0; i < share.length; i++) {
  const btn = share[i];
  btn.addEventListener('click', async () => {
  var info = btn.id;
  const myArray = info.split("+");
  const id = myArray[0];
  const nama = myArray[1];
  const berat = myArray[2];
  const harga = myArray[3];
  const size = myArray[4];
  const gambar = myArray[5];
  
  const imgsrc = document.getElementById(id).src;
  var img = document.createElement("img");
  img.src = imgsrc

  const dataUrl = getDataUrl(img);
  
  var blob = dataURLtoBlob(dataUrl);
  var file = new File([blob], "picture.jpg", {type: 'image/jpeg'});
  var filesArray = [file];
  
  const shareData = {
    title: nama + " | " + berat + "gr | sz:" + size + " | "+ harga + "k" ,
    text: 'Coba cek ini, deh: ' + nama + " | " + berat + "gr | sz:" + size + " | "+ harga + "k" + ' di website toko mas enam itc 2',
    url: 'https://www.enamitc2.com/product_list.php?id=' + id,
    files: filesArray
  }
   navigator.share(shareData) });
  }
</script>







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



</body>
</html>