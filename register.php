<?php
require_once "pdo.php";
session_start();

if (isset($_POST['register'])){

    $sql = "SELECT userid FROM users WHERE phone=:phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':phone' => $_POST['phone']));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($user)){
            $_SESSION['error'] = "Nomor WA sudah terdaftar";
            $_SESSION["username"] =$_POST['username'];
            $_SESSION["email"] =$_POST['email'];
            $_SESSION["password_1"] =$_POST['password_1'];
            $_SESSION["password_2"] =$_POST['password_2'];
            $_SESSION["address"] =$_POST['address'];
            $_SESSION["birthday"] =$_POST['birthday']; 
            header('Location: register.php');
            return;
        }
        else if ($_POST['password_1'] != $_POST['password_2']){
            $_SESSION['error'] = "Konfirmasi password tidak sama";
            $_SESSION["username"] =$_POST['username'];
            $_SESSION["email"] =$_POST['email'];
            $_SESSION["phone"] =$_POST['phone'];
            $_SESSION["address"] =$_POST['address'];
            $_SESSION["birthday"] =$_POST['birthday'];
            header('Location: register.php');
            return;
        }
        else {
            $password = md5($_POST['password_2']);
            date_default_timezone_set('Asia/Jakarta');
            $registerdate = date("Y-m-d H:i:s");
            $sql = "INSERT INTO users (username, email, password, address, phone, birthday, member_since) 
                    VALUES (:username, :email, :password, :address, :phone, :birthday, :member_since)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':username' => $_POST['username'],
                ':email' => $_POST['email'],
                ':password' => $password,
                ':phone' => $_POST['phone'],
                ':address' => $_POST['address'],
                ':birthday' => $_POST['birthday'],
                ':member_since' => $registerdate));
            
            $sql = "SELECT userid FROM users WHERE phone=:phone";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':phone' => $_POST['phone']));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);


            $_SESSION['userid']= $user['userid'];
            unset($_SESSION['username']);
            unset($_SESSION['email']);
            unset($_SESSION['password_1']);
            unset($_SESSION['password_2']);
            unset($_SESSION['address']);
            unset($_SESSION['phone']);
            unset($_SESSION['birthday']);

            header('Location: profile.php');
            return;
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
    <title>Register</title>

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
                    <li><a href = "product_list.php?supplier=BG">BG</a></li>
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

<!-- register form section starts -->

<section class="register-form">

    <form method="post">
        <h3>register</h3>
        <div class="inputBox">
            <span class="fas fa-user"></span>
            <input type="text" name="username" value="<?php echo isset($_SESSION["username"]) ? $_SESSION["username"] : ''; ?>" placeholder="Nama" id="">
        </div>
        <div class="inputBox">
            <span class="fas fa-envelope"></span>
            <input type="email" name="email" value="<?php echo isset($_SESSION["email"]) ? $_SESSION["email"] : ''; ?>" placeholder="Email" id="">
        </div>
        <div class="inputBox">
            <span class="fas fa-key"></span>
            <input type="password" name="password_1" value="<?php echo isset($_SESSION["password_1"]) ? $_SESSION["password_1"] : ''; ?>" placeholder="Password" id="">
        </div>
        <div class="inputBox">
            <span class="fas fa-lock"></span>
            <input type="password" name="password_2" value="<?php echo isset($_SESSION["password_2"]) ? $_SESSION["password_2"] : ''; ?>" placeholder="Konfirmasi Password" id="">
        </div>
        <div class="inputBox">
            <span class="fab fa-whatsapp"></span>
            <input type="text" name="phone" value="<?php echo isset($_SESSION["phone"]) ? $_SESSION["phone"] : ''; ?>" placeholder="Nomor WA" id="">
        </div>
        <div class="inputBox">
            <span class="far fa-address-book"></span>
            <input type="text" name="address" value="<?php echo isset($_SESSION["address"]) ? $_SESSION["address"] : ''; ?>" placeholder="Alamat" id="">
        </div>
        <div class="inputBox">
            <span class="fas fa-birthday-cake"></span>
            <input type="date" name="birthday" value="<?php echo isset($_SESSION["birthday"]) ? $_SESSION["birthday"] : ''; ?>" placeholder="Tanggal Lahir">
        </div>
        <input type="submit" value="sign up" name="register" class="btn">
        <?php
          if (isset($_SESSION['error'])) {
              echo ("<p class=\"error\">".$_SESSION['error']."</p>\n");
              unset($_SESSION['error']);
          }
        ?>
        <a href="login.php" class="btn">Login</a>
    </form>

</section>

<!-- register form section ends -->


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
                <a href = "product_list.php?supplier=BG"><i class="fas fa-angle-right"></i>BG</a>
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