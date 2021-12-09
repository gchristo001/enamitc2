<?php
require_once "pdo.php";
session_start();

if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}
$stmt = $pdo->prepare("SELECT * FROM items where itemid = :itemid");
$stmt->execute(array(":itemid" => $_GET['itemid']));
$item = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM item_attributes where itemid = :itemid");
$stmt->execute(array(
    ':itemid' => $item['itemid']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



if (isset($_POST['size'])){

    $sql = $pdo->query("SELECT gold_price FROM gold_price");
    $gold_price = $sql->fetch(PDO::FETCH_ASSOC);
    
    (float)$price = (ceil($gold_price['gold_price'] * (float)$item['code'] * (float)$_POST['weight'] /5))*5;

    $sql = "INSERT INTO item_attributes (itemid, size, weight, price, quantity)
            VALUES (:itemid, :size, :weight, :price, :quantity)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':itemid' => $item['itemid'],
        ':size' => $_POST['size'],
        ':weight' => $_POST['weight'],
        ':price' => $price,
        ':quantity' => $_POST['quantity'] ));

    $_SESSION['success'] = 'Size Added';

    header( 'Location: size_input.php?itemid='.$_GET['itemid'] ) ;
    return;
} 

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Size</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css">

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
                </ul>
            </li>
            <li><a href="#">Hadiah +</a>
                <ul>
                    <li><a href="prize_input.php">Input</a></li>
                </ul>
            </li>
            <li><a href="#">Order +</a>
                <ul>
                    <li><a href="order_input.php">Input</a></li>
                    <li><a href="order_confirm.php">Konfirmasi</a></li>
                </ul>
            </li>
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
    <form method="post"  id="item-input">
       <h1>Input Ukuran</h1>
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
            <div class="form-field">
  				<label for="weight">Berat :</label>
                <input type="text" id="weight" name="weight" class= "input" required>
  			</div>
            <div class="form-field">
  			    <label for="size">Ukuran :</label>
                <input type="text" id="size" name="size" class= "input">			
  			</div>
            <div class="form-field">
                <label for="quantity">Stok :</label>
  			    <input type="text" id="quantity" name="quantity"  class= "input" required>
  			</div>
  			<div class="form-field">
                <input id="Submit" type="submit" name="action" value="Tambah Size" class="button">
  			</div>
            
            
       </form>
    </div>

    <div class="box">

        <table>
            <tr>
              <th>Item_id</th>
              <th>Nama</th>
              <th>Supplier</th>
              <th>Kategori</th>
              <th>Kode</th>
              <th>Gambar</th>
            </tr>
            
            <?php
                echo ("<tr>");
                echo ("<td>".$item['itemid']."</td>");
                echo ("<td>".$item['name']."</td>");
                echo ("<td>".$item['supplier']."</td>");
                echo ("<td>".$item['category']."</td>");
                echo ("<td>".$item['code']."</td>");
                echo ("<td><img class=\"logo\" src=\"item-image/".$item['image']."\"</td>");
                echo ("</tr>");
            ?>
        </table>
        <br>
        <br>
        <table>
            <tr>
              <th>Att_id</th>
              <th>Size</th>
              <th>Berat</th>
              <th>Harga</th>
              <th>Stok</th>
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['attributeid']."</td>");
                echo ("<td>".$row['size']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                echo ("<td>".$row['quantity']."</td>");              
                echo ("</tr>");
            }
            ?>
        </table>
    </div>


</section>

<!-- banner section ends -->










</body>
</html>