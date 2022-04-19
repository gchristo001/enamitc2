<?php
require_once "pdo.php";
session_start();
error_reporting(0);


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
     die("ACCESS DENIED");
}

if (isset($_POST['name'])){

    date_default_timezone_set('Asia/Jakarta');
    $inputdate = date("Y-m-d H:i:s");

    $temp = explode(".", $_FILES["fileToUpload"]["name"]);
    $newfilename = round(microtime(true)) . '.' . end($temp);
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "item-image/" . $newfilename);

    $sql = "INSERT INTO items (name, hot, event, supplier, category, code, image, time, view)
              VALUES (:name, :hot, :event, :supplier, :category, :code, :image, :time, :view)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':hot' => $_POST['hot'],
        ':event' => $_POST['event'],
        ':supplier' => $_POST['supplier'],
        ':category' => $_POST['category'],
        ':code' => $_POST['code'],
        ':image' => $newfilename,
        ':time' => $inputdate,
        ':view' => $_POST['view'] ));

    $stmt = $pdo->prepare("SELECT itemid FROM items where time = :time");
    $stmt->execute(array(":time" => $inputdate));
    $itemid = $stmt->fetch(PDO::FETCH_ASSOC);


    if($_POST['event'] == 1){
        $sql = $pdo->query("SELECT harga_event1 FROM gold_price");
        $harga_event1 = $sql->fetch(PDO::FETCH_ASSOC);
        $gold_price['gold_price'] = $harga_event1['harga_event1'];
    }
    elseif($_POST['event'] == 2){
        $sql = $pdo->query("SELECT harga_event2 FROM gold_price");
        $harga_event2 = $sql->fetch(PDO::FETCH_ASSOC);
        $gold_price['gold_price'] = $harga_event2['harga_event2'];
    }
    elseif($_POST['event'] == 3){
        $sql = $pdo->query("SELECT harga_ciliu FROM gold_price");
        $harga_ciliu = $sql->fetch(PDO::FETCH_ASSOC);
        $gold_price['gold_price'] = $harga_ciliu['harga_ciliu'];
    }
    else{
    $sql = $pdo->query("SELECT gold_price FROM gold_price");
    $gold_price = $sql->fetch(PDO::FETCH_ASSOC);
    }
    
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

if(isset($_POST['publish'])){
    $sql = "UPDATE items set view = 1";
    $stmt = $pdo->query($sql);
}

    $sql = " SELECT * FROM items
    LEFT JOIN item_attributes
    ON items.itemid = item_attributes.itemid
    where
    attributeid in (select t.attributeid from (select itemid, min(attributeid) as attributeid from item_attributes group by 1) as t) and quantity > 0
    ORDER BY items.itemid DESC
    LIMIT 50";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Barang</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css?<?=filemtime('admin.css');?>">

    <!-- custom js file link  -->
    <script src="admin_script.js"defer></script>

    <style>

        .banner{
            display: grid;
            grid-template-columns:  repeat(auto-fill, minmax(400px, 1fr));
            padding-top: 100px;
        }


        @media (max-width: 500px) {
            html {
                font-size: 50%;
                overflow: scroll;
            }
            .home .slide .content h3 {
                font-size: 4rem;
            }
            label{
                font-size: 12rem;
            }
            .input {
                width: 14rem;
                font-size: 1.5rem;
                color: black;
                padding: .5rem 1rem;
                border-radius: .5rem;
                background: #eee;
            }
            .button{
                color: #fff;
                width: 14rem;
                height: 34px;
                background: black;
                border-radius: 5px;
            }

            .banner .box{
                display: flex;
                flex-direction: column;
                padding-top: 20px;
            }

            table, thead, tbody, th, td, tr { 
            display: flex;
            flex-direction: column;
            width: 30rem;
            padding: 5px; 
            }
            
            
            tr { border: 1px solid #ccc; }
            
            td { 
            border: none;
            border-bottom: 1px solid #eee; 
            position: relative;
            padding-left: 30%;
            width: auto;
            text-align: left;  
            }

            th{display: none;}
            
            td:before { 
            position: absolute;
            top: 6px;
            left: 6px;
            width: 35%; 
            padding-right: 10px; 
            white-space: nowrap;
            font-weight: bold;
            text-align: left;
            }
            
        td:nth-of-type(1):before { content: "Id"; }
        td:nth-of-type(2):before { content: "Tampil"; }
        td:nth-of-type(3):before { content: "Nama"; }
        td:nth-of-type(4):before { content: "Hot Deal"; }
        td:nth-of-type(5):before { content: "Event"; }
        td:nth-of-type(6):before { content: "Supplier"; }
        td:nth-of-type(7):before { content: "Kategori"; }
        td:nth-of-type(8):before { content: "Kode"; }
        td:nth-of-type(9):before { content: "Size"; }
        td:nth-of-type(10):before { content: "Berat"; }
        td:nth-of-type(11):before { content: "Harga"; }
        td:nth-of-type(12):before { content: "Gambar"; }
        td:nth-of-type(13):before { content: "Aksi"; }
  }
        
    </style>

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
    <div class="form-field">
        <div>
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
        </div>
        <form method="post">
            <input id="Submit" type="submit" name="publish" value="Publish" class="button">
        </form>
    </div>
    <br>
    <br>

    <form method="post"  id="item-input" action="item_input.php" enctype="multipart/form-data">
       
        
            <div class="form-field">
                <label for="name">Nama Barang :</label>
                <input type="text" name="name" id="name" class= "input" required>			
            </div>
            <div class="form-field">
                <label for="hot">Hot Deal ? :</label>
                <select id="hot" name="hot"  class= "input" required>
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </select>
            </div>
            <div class="form-field">
                <label for="event">Event :</label>
                <select id="event" name="event"  class= "input" required>
                    <option value="0"selected>Null</option>
                    <option value="3">Ciliu</option>
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
                    <option value="BG">BG</option>
                    <option value="Ayu">Ayu</option>
                    <option value="SDW">SDW</option>
                    <option value="Hala">Hala</option>
                    <option value="Hartadinata">Hartadinata</option>
                    <option value="MT">MT</option>
                    <option value="KAP">KAP</option>
                    <option value="Other">Other</option>
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
                <p>Tampilkan :</p>
                <div style="display:flex ; flex-direction: column ; justify-content=left; gap: 3px;">
  			        <div>
                    <input type="radio" id="yes" name="view" style="transform:scale(1.5); " value="1" checked="checked">
                    <label for="yes" style="padding-left: 5px;"> Ya </label>
                    </div>
                    <div>
                    <input type="radio" id="no" name="view" style="transform:scale(1.5);" value="0">
                    <label for="no"  style="padding-left: 5px;"> Tidak </label>
                    </div>
                </div>
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
              <th>Tampil</th>
              <th>Nama</th>
              <th>Hot Deal</th>
              <th>Event</th>
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
                if($row['view'] == 1){
                    echo ("<td>Ya</td>");
                }
                else{
                    echo ("<td>Tidak</td>");
                }
                echo ("<td>".$row['name']."</td>");
                echo ("<td>".$row['hot']."</td>");
                echo ("<td>".$row['event']."</td>");
                echo ("<td>".$row['supplier']."</td>");
                echo ("<td>".$row['category']."</td>");
                echo ("<td>".$row['code']."</td>");
                echo ("<td>".$row['size']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                echo ("<td><img class=\"logo\" src=\"item-image/".$row['image']."\"</td>");
                echo("<td>");
                echo('<a href="size_input.php?itemid='.$row['itemid'].'">Tambah Size  /</a>');
                echo('<a href="item_edit1.php?itemid='.$row['itemid'].'"> Edit /</a>');
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