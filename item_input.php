<?php
require_once "pdo.php";
session_start();
error_reporting(0);


if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}

if (isset($_POST['name'])){

    date_default_timezone_set('Asia/Jakarta');
    $inputdate = date("Y-m-d H:i:s");

    $temp = explode(".", $_FILES["fileToUpload"]["name"]);
    $newfilename = round(microtime(true)) . '.' . end($temp);
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "item-image/" . $newfilename);

    $sql = "INSERT INTO items (name, hot, event, supplier, category, code, image, time)
              VALUES (:name, :hot, :event, :supplier, :category, :code, :image, :time)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':hot' => $_POST['hot'],
        ':event' => $_POST['event'],
        ':supplier' => $_POST['supplier'],
        ':category' => $_POST['category'],
        ':code' => $_POST['code'],
        ':image' => $newfilename,
        ':time' => $inputdate ));

    $stmt = $pdo->prepare("SELECT itemid FROM items where time = :time");
    $stmt->execute(array(":time" => $inputdate));
    $itemid = $stmt->fetch(PDO::FETCH_ASSOC);



    $sql = $pdo->query("SELECT gold_price FROM gold_price");
    $gold_price = $sql->fetch(PDO::FETCH_ASSOC);
    
    (float)$price = (ceil($gold_price['gold_price'] * (float)$_POST['code'] * (float)$_POST['weight'] /500))*5;

    $sql = "INSERT INTO item_attributes (itemid, size, weight, price, quantity)
              VALUES (:itemid, :size, :weight, :price, :quantity)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':itemid' => $itemid['itemid'],
        ':size' => $_POST['size'],
        ':weight' => $_POST['weight'],
        ':price' => $price,
        ':quantity' => $_POST['quantity'] ));

    $_SESSION['success'] = 'Record Added';

    if ($_POST['action'] == "Tambah Size"){
        header( 'Location: size_input.php?itemid='.$itemid['itemid'] ) ;
        return;
    }
    else{
    header( 'Location: item_input.php' ) ;
    return;
    }
} 
    $sql = " SELECT * FROM items
    LEFT JOIN item_attributes
    ON items.itemid = item_attributes.itemid
    where
    attributeid in (select t.attributeid from (select itemid, min(attributeid) as attributeid from item_attributes group by 1) as t)
    ORDER BY items.itemid DESC
    LIMIT 10";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>

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
                    <li><a href="prize_confirm.php">Konfirmasi</a></li>
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
    <form method="post"  id="item-input" action="item_input.php" enctype="multipart/form-data">
       <h1>Input Data</h1>
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
                <label for="name">Nama Barang :</label>
                <input type="text" name="name" id="name" class= "input" required>			
            </div>
            <div class="form-field">
                <label for="hot">Hot Deal ? :</label>
                <select id="hot" name="hot"  class= "input" required>
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>
            <div class="form-field">
                <label for="event">Event :</label>
                <select id="event" name="event"  class= "input" required>
                    <option value="0"selected>Null</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="form-field">
                <label for="supplier">Supplier :</label>
                <select id="supplier" name="supplier"class= "input" required>
                    <option value="DeGold">DeGold</option>
                    <option value="UBS">UBS</option>
                    <option value="Lotus">Lotus</option>
                    <option value="YT">YT</option>
                    <option value="Kinghalim">Kinghalim</option>
                    <option value="Citra">Citra</option>
                    <option value="HWT">HWT</option>
                    <option value="Bulgari">Bulgari</option>
                    <option value="Ayu">Ayu</option>
                    <option value="SDW">SDW</option>
                    <option value="Hala">Hala</option>
                    <option value="Amero">Amero</option>
                    <option value="MT">MT</option>
                </select>
                            
            </div>
            <div class="form-field">
                <label for="category">Kategori :</label>
                <select id="category" name="category"  class= "input" required>
                    <option value="Ring">Cincin</option>
                    <option value="Earings">Anting</option>
                    <option value="Necklace">Kalung</option>
                    <option value="Bracelet">Gelang</option>
                    <option value="Pendant">Liontin</option>
                    <option value="Bangle">Gelondong</option>
                    <option value="Kids">Anak</option>
                    <option value="Dubai gold">Dubai</option>
                    <option value="Gold bar">LM</option>
                </select>			
            </div>
            <div class="form-field">
  				<label for="code">Kode :</label>
                <input type="text" id="code" name="code" class= "input">
  			</div>
            <div class="form-field">
                <label for="fileToUpload">Gambar :</label>
  			    <input type="file" id="fileToUpload" name="fileToUpload" value=""  class="input" required>
  			</div>
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
  				<input id="Submit" type="submit" name="action" value="Insert" class="button">
                <input id="Submit" type="submit" name="action" value="Tambah Size" class="button">
  			</div>
            
            
       </form>
    </div>

    <div class="box-table">
        <table>
            <tr>
              <th>Id</th>
              <th>Nama</th>
              <th>Supplier</th>
              <th>Kategori</th>
              <th>Kode</th>
              <th>Size</th>
              <th>Berat</th>
              <th>Harga</th>
              <th>Gambar</th>
              <th>Aksi</th>
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['itemid']."</td>");
                echo ("<td>".$row['name']."</td>");
                echo ("<td>".$row['supplier']."</td>");
                echo ("<td>".$row['category']."</td>");
                echo ("<td>".$row['code']."</td>");
                echo ("<td>".$row['size']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                echo ("<td><img class=\"logo\" src=\"item-image/".$row['image']."\"</td>");
                echo("<td>");
                echo('<a href="size_input.php?itemid='.$row['itemid'].'">Tambah Size  /</a>');
                echo('<a href="item_delete.php?itemid='.$row['itemid'].'"> Delete</a>');
                echo ("</td></tr>");
            }
            ?>
        </table>
    </div>


</section>

<!-- banner section ends -->










</body>
</html>