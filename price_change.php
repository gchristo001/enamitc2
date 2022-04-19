<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}

$sql = $pdo->query("SELECT gold_price FROM gold_price");
$gold_price = $sql->fetch(PDO::FETCH_ASSOC);

    if ( isset($_POST['gp'])){
            $sql = "UPDATE gold_price SET gold_price = :gold_price";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(":gold_price" => $_POST['gold_price']));

            $stmt = $pdo->prepare("UPDATE item_attributes left join items on item_attributes.itemid = items.itemid
            set price = ceiling(:gold_price * items.code * item_attributes.weight /500)*5  WHERE items.event = 0");
            $stmt->execute(array(":gold_price" => $_POST['gold_price']));

            $_SESSION['success'] = 'Harga Emas berhasil diganti';
            header( 'Location: price_change.php' ) ;
            return;
    }

$sql = $pdo->query("SELECT harga_event1 FROM gold_price");
$harga_event1 = $sql->fetch(PDO::FETCH_ASSOC);
    
    if ( isset($_POST['e1'])){
            $sql = "UPDATE gold_price SET harga_event1 = :harga_event1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(":harga_event1" => $_POST['harga_event1']));
    
            $stmt = $pdo->prepare("UPDATE item_attributes left join items on item_attributes.itemid = items.itemid 
            set price = ceiling(:harga_event1 * items.code * item_attributes.weight /500)*5 WHERE items.event = 1");
            $stmt->execute(array(":harga_event1" => $_POST['harga_event1']));
    
            $_SESSION['success'] = 'Harga Event 1 berhasil diganti';
            header( 'Location: price_change.php' ) ;
            return;
        }

$sql = $pdo->query("SELECT harga_event2 FROM gold_price");
$harga_event2 = $sql->fetch(PDO::FETCH_ASSOC);
    
    if ( isset($_POST['e2'])){
            $sql = "UPDATE gold_price SET harga_event2 = :harga_event2";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(":harga_event2" => $_POST['harga_event2']));
    
            $stmt = $pdo->prepare("UPDATE item_attributes left join items on item_attributes.itemid = items.itemid 
            set price = ceiling(:harga_event2 * items.code * item_attributes.weight /500)*5 WHERE items.event = 2");
            $stmt->execute(array(":harga_event2" => $_POST['harga_event2']));
    
            $_SESSION['success'] = 'Harga Event 2 berhasil diganti';
            header( 'Location: price_change.php' ) ;
            return;
        }

$sql = $pdo->query("SELECT harga_ciliu FROM gold_price");
$harga_ciliu = $sql->fetch(PDO::FETCH_ASSOC);
    
    if ( isset($_POST['cl'])){
            $sql = "UPDATE gold_price SET harga_ciliu = :harga_ciliu";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(":harga_ciliu" => $_POST['harga_ciliu']));
    
            $stmt = $pdo->prepare("UPDATE item_attributes left join items on item_attributes.itemid = items.itemid 
            set price = ceiling(:harga_ciliu * items.code * item_attributes.weight /500)*5 WHERE items.event = 3");
            $stmt->execute(array(":harga_ciliu" => $_POST['harga_ciliu']));
    
            $_SESSION['success'] = 'Harga Ciliu berhasil diganti';
            header( 'Location: price_change.php' ) ;
            return;
        }



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Harga</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css?<?=filemtime('admin.css');?>">

    <!-- custom js file link  -->
    <script src="admin_script.js"defer></script>

</head>
<body>

<!-- header section starts  -->

<header class="header">

    <a href="logout.php"> <img class="logo" src="images/Logo.png"> </a>

    <nav class="navbar">
        <ul>
            <li><a href="#">Barang +</a>
                <ul>
                    <li><a href="item_input.php">Input</a></li>
                    <li><a href="size_input2.php">Tambah Size</a></li>
                    <li><a href="item_edit.php">Edit</a></li>
                </ul>
            </li>
            <li><a href="#">Hadiah +</a>
                <ul>
                    <li><a href="prize_input.php">Input</a></li>
                    <li><a href="prize_confirm.php">Konfirmasi</a></li>
                    <li><a href="prize_edit.php">Edit</a></li>
                </ul>
            </li>
            <li><a href="#">Order +</a>
                <ul>
                    <li><a href="order_input.php">Input</a></li>
                    <li><a href="order_confirm.php">Konfirmasi</a></li>
                    <li><a href="order_edit.php">Edit</a></li>
                    <li><a href="menu_print.php">Print Bon</a></li>
                </ul>
            </li>
            <li><a href="#">Event +</a>
                <ul>
                    <li><a href="event1.php">Event 1</a></li>
                    <li><a href="event2.php">Event 2</a></li>
                </ul>
            </li>
            <?php
                if($_SESSION['userid'] == 4){
                    echo '<li><a href="#">Admin Access +</a>
                    <ul>';
                    echo '<li><a href="admin_access.php">Cek Akun</a> </li>';
                    echo '<li><a href="show_order.php">Order online</a> </li>';
                    echo '<li><a href="show_offline_order.php">Order fisik</a> </li>';
                    echo '<li><a href="show_redeem.php">Penukaran Hadiah</a> </li>';
                    echo '<li><a href="price_change.php">Ganti Harga</a> </li>';
                    echo '</ul>
                    </li>';
                }
            ?>
        </ul>
    </nav>

    <div class="icons">
        <div id="menu-btn" class="fas fa-bars"></div> 
    </div>


</header>

<!-- header section ends -->

<!-- banner section starts  -->

<section class="banner">

    <div class="box">
    <form method="post"  id="order-input">
       <h1>Ganti Harga</h1>
       <?php
         if ( isset($_SESSION['success']) ) {
             echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
             unset($_SESSION['success']);
         }
         if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
            unset($_SESSION['error']);
         }
         ?>
       <h2> Harga Emas saat ini : <?php echo ($gold_price['gold_price']);?> </h2>

            <div class="form-field">
                <label for="gold_price">Ganti Harga:</label>
                <input type="text" name="gold_price" id="gold_price" class= "input" value= "<?=$gold_price['gold_price']?>"required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="gp" value="Ubah" class="button">
  			</div>
    </form>
    <br><br>
    <form method="post"  id="order-input">
       <h2> Harga Ciliu saat ini : <?php echo ($harga_ciliu['harga_ciliu']);?> </h2>

            <div class="form-field">
                <label for="harga_ciliu">Ganti Harga Ciliu:</label>
                <input type="text" name="harga_ciliu" id="harga_ciliu" class= "input" value= "<?=$harga_ciliu['harga_ciliu']?>"required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="cl" value="Ubah" class="button">
  			</div>
    </form>
    <br><br>
    <form method="post"  id="order-input">
       <h2> Harga Event 1 saat ini : <?php echo ($harga_event1['harga_event1']);?> </h2>

            <div class="form-field">
                <label for="harga_event1">Ganti Harga Event 1:</label>
                <input type="text" name="harga_event1" id="harga_event1" class= "input" value= "<?=$harga_event1['harga_event1']?>"required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="e1" value="Ubah" class="button">
  			</div>
    </form>
    <br><br>
    <form method="post"  id="order-input">
       <h2> Harga Event 2 saat ini : <?php echo ($harga_event2['harga_event2']);?> </h2>

            <div class="form-field">
                <label for="harga_event1">Ganti Harga Event 2:</label>
                <input type="text" name="harga_event2" id="harga_event2" class= "input" value= "<?=$harga_event2['harga_event2']?>"required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="e2" value="Ubah" class="button">
  			</div>
    </form>
    </div>


    </section>

<!-- banner section ends -->

</body>
</html>