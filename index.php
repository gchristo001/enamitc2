
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
            <a href="https://shopee.co.id/tokomasenamitc2"> <i class="fab fa-shopify"></i> Shopee </a>
            <a href="https://tokopedia.link/ZPcW84MOcib"> <i class="fas fa-shopping-bag"></i> Tokopedia </a>
            <a href="https://www.instagram.com/tokomas_enamitc2/"> <i class="fab fa-instagram"></i> Instagram </a>
            <a href="https://wa.me/62818188266"> <i class="fab fa-whatsapp"></i> Whatsapp</a>
        </div>

        <div class="box">
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