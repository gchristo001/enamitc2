<?php
require_once "pdo.php";
session_start();

$stmt = $pdo->query("SELECT itemid,name,weight,size,price,image FROM items where orderid IS NULL ORDER BY itemid desc limit 4");
$newitem = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT itemid,name,weight,size,price,image FROM items where orderid IS NULL ORDER BY RAND() limit 4 ");
$randomitem = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Mas Enam ITC 2</title>

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

            <?php $badge = count($_SESSION['cart']); ?>

            return false;
        }
   </script>



</head>
<body>

<!-- header section starts  -->

<header class="header">

    <a href="index.php" class="logo"> <img src="images/Logo.png"> </a>

</header>
<!-- header section ends -->

<!-- home section starts      -->

<section class="home">

    <div class="slide active" style="background: url(images/banner1.jpg) no-repeat;">
        <div class="content">
            <span>Ditunggu ya ...</span>
            <span>Bentar lagi ada yang seru!! ~~</span>
            <h3>SEDANG DALAM KONSTRUKSI</h3>
        </div>
    </div>

</section>


<!-- menu section ends -->
<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">
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