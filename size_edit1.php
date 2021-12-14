<?php
require_once "pdo.php";
session_start();

if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}

$sql = " SELECT * FROM items
    LEFT JOIN item_attributes
    ON items.itemid = item_attributes.itemid
    where
    items.itemid = :itemid AND 
    item_attributes.attributeid in (select t.attributeid from (select itemid, min(attributeid) as attributeid from item_attributes group by 1) as t)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":itemid" => $_GET['itemid']));
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM item_attributes where itemid = :itemid");
    $stmt->execute(array(
        ':itemid' => $item['itemid']));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM item_attributes where attributeid = :attributeid");
    $stmt->execute(array(
        ':attributeid' => $item['attributeid']));
    $att = $stmt->fetch(PDO::FETCH_ASSOC);



if (isset($_POST['size'])){

    $sql = $pdo->query("SELECT gold_price FROM gold_price");
    $gold_price = $sql->fetch(PDO::FETCH_ASSOC);
    
    (float)$price = (ceil($gold_price['gold_price'] * (float)$item['code'] * (float)$_POST['weight'] /500))*5;

    $sql = "UPDATE item_attributes SET size =:size, weight=:weight, price=:price, quantity=:quantity
            WHERE attributeid =:attributeid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':attributeid' => $_GET['attributeid'],
        ':size' => $_POST['size'],
        ':weight' => $_POST['weight'],
        ':price' => $price,
        ':quantity' => $_POST['quantity'] ));

    $_SESSION['success'] = 'Size Updated';

    header( 'Location: size_edit1.php?attributeid='.$att['attributeid'].'&itemid='.$_GET['itemid'] ) ;
    return;
} 

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Size</title>

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
                </ul>
            </li>
            <?php
                if($_SESSION['userid'] == 4){
                    echo '<li><a href="admin_access.php">Cek Akun</a> </li>';
                    echo '<li><a href="show_order.php">Order online</a> </li>';
                    echo '<li><a href="show_offline_order.php">Order fisik</a> </li>';
                    echo '<li><a href="show_redeem.php">Penukaran Hadiah</a> </li>';
                    echo '<li><a href="price_change.php">Ganti Harga</a> </li>';
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
    <form method="post"  id="item-input">
       <h1>Edit Size</h1>
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
                <input type="text" id="weight" name="weight" value = "<?=$att['weight']?>" class= "input" required>
  			</div>
            <div class="form-field">
  			    <label for="size">Ukuran :</label>
                <input type="text" id="size" name="size" value = "<?=$att['size']?>" class= "input">			
  			</div>
            <div class="form-field">
                <label for="quantity">Stok :</label>
  			    <input type="text" id="quantity" name="quantity" value = "<?=$att['quantity']?>" class= "input" required>
  			</div>
  			<div class="form-field">
                <input id="Submit" type="submit" name="action" value="Edit" class="button">
  			</div>
            
            
       </form>
    </div>

    <div class="box-table">

        <table>
            <tr>
              <th>Item_id</th>
              <th>Nama</th>
              <th>Hot Deal</th>
              <th>Event</th>
              <th>Supplier</th>
              <th>Kategori</th>
              <th>Kode</th>
              <th>Gambar</th>
              <th>Aksi</th>
            </tr>
            
            <?php
                echo ("<tr>");
                echo ("<td>".$item['itemid']."</td>");
                echo ("<td>".$item['name']."</td>");
                echo ("<td>".$item['hot']."</td>");
                echo ("<td>".$item['event']."</td>");
                echo ("<td>".$item['supplier']."</td>");
                echo ("<td>".$item['category']."</td>");
                echo ("<td>".$item['code']."</td>");
                echo ("<td><img class=\"logo\" src=\"item-image/".$item['image']."\"</td>");
                echo("<td>");
                echo('<a href="item_edit1.php?itemid='.$item['itemid'].'"> Edit /</a>');
                echo('<a href="item_delete.php?itemid='.$item['itemid'].'"> Delete</a>');
                echo ("</td></tr>");
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
              <th>Edit</th>
              <th>Delete</th>
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['attributeid']."</td>");
                echo ("<td>".$row['size']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                echo ("<td>".$row['quantity']."</td>"); 
                echo ('<td><a href="size_edit1.php?attributeid='.$row['attributeid'].'&itemid='.$row['itemid'].'">Edit</a></td>');
                if ($row['attributeid'] != $item['attributeid']){
                echo ('<td><a href="size_delete.php?attributeid='.$row['attributeid'].'&itemid='.$row['itemid'].'">Delete</a></td>');             
                }
                echo ("</tr>");
            }
            ?>
        </table>
    </div>


</section>

<!-- banner section ends -->










</body>
</html>