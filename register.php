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