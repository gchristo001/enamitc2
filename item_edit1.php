<?php
require_once "pdo.php";
session_start();
error_reporting(0);


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
    $items = $stmt->fetch(PDO::FETCH_ASSOC);


    
    $stmt = $pdo->prepare("SELECT * FROM items where itemid = :itemid");
    $stmt->execute(array(":itemid" => $_GET['itemid']));
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM item_attributes where itemid = :itemid");
    $stmt->execute(array(
        ':itemid' => $item['itemid']));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['name'])){

    if(!(!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] == UPLOAD_ERR_NO_FILE)){
        $temp = explode(".", $_FILES["fileToUpload"]["name"]);
        $newfilename = round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "../item-image/" . $newfilename);

        $sql = "UPDATE items SET name =:name, hot =:hot, event =:event, supplier = :supplier, category =:category, code =:code, image =:image
        WHERE itemid = :itemid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':name' => $_POST['name'],
            ':hot' => $_POST['hot'],
            ':event' => $_POST['event'],
            ':supplier' => $_POST['supplier'],
            ':category' => $_POST['category'],
            ':code' => $_POST['code'],
            ':image' => $newfilename,
            ':itemid' => $items['itemid'] ));


        $sql = $pdo->query("SELECT gold_price FROM gold_price");
        $gold_price = $sql->fetch(PDO::FETCH_ASSOC);
        
        (float)$price = (ceil($gold_price['gold_price'] * (float)$_POST['code'] * (float)$_POST['weight'] /500))*5;

        $sql = "UPDATE item_attributes SET size=:size, weight=:weight, price=:price, quantity=:quantity
        WHERE itemid = :itemid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':itemid' => $_GET['itemid'],
            ':size' => $_POST['size'],
            ':weight' => $_POST['weight'],
            ':price' => $price,
            ':quantity' => $_POST['quantity'] ));

        $_SESSION['success'] = 'Barang & gambar berhasil diupdate';
        header( 'Location: item_edit1.php?itemid='.$items['itemid'] ) ;
        return;
    }
    else{
    $sql = "UPDATE items SET name =:name, hot =:hot, event =:event, supplier = :supplier, category =:category, code =:code
    WHERE itemid = :itemid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':hot' => $_POST['hot'],
        ':event' => $_POST['event'],
        ':supplier' => $_POST['supplier'],
        ':category' => $_POST['category'],
        ':code' => $_POST['code'],
        ':itemid' => $items['itemid'] ));


    $sql = $pdo->query("SELECT gold_price FROM gold_price");
    $gold_price = $sql->fetch(PDO::FETCH_ASSOC);
    
    (float)$price = (ceil($gold_price['gold_price'] * (float)$_POST['code'] * (float)$_POST['weight'] /500))*5;

    $sql = "UPDATE item_attributes SET size=:size, weight=:weight, price=:price, quantity=:quantity
    WHERE itemid = :itemid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':itemid' => $_GET['itemid'],
        ':size' => $_POST['size'],
        ':weight' => $_POST['weight'],
        ':price' => $price,
        ':quantity' => $_POST['quantity'] ));

    $_SESSION['success'] = 'Barang berhasil diupdate';
    header( 'Location: item_edit1.php?itemid='.$items['itemid'] ) ;
    return;
    }

} 
  

?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css?<?=filemtime('admin.css');?>">

    <!-- custom js file link  -->
    <script src="admin_script.js"defer></script>

    <style>
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

            .banner{
                display: flex;
                flex-direction: column;
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
        td:nth-of-type(2):before { content: "Nama"; }
        td:nth-of-type(3):before { content: "Hot Deal"; }
        td:nth-of-type(4):before { content: "Event"; }
        td:nth-of-type(5):before { content: "Supplier"; }
        td:nth-of-type(6):before { content: "Kategori"; }
        td:nth-of-type(7):before { content: "Kode"; }
        

        .att td:nth-of-type(1):before { content: "Att_id"; }
        .att td:nth-of-type(2):before { content: "Size"; }
        .att td:nth-of-type(3):before { content: "Berat"; }
        .att td:nth-of-type(4):before { content: "Harga"; }
        .att td:nth-of-type(5):before { content: "Stok"; }
        .att td:nth-of-type(6):before { content: "Edit"; }
        .att td:nth-of-type(7):before { content: "Delete"; }
    }
    </style>

</head>
<body>

<!-- header section starts  -->

<header class="header">

    <a href="admin_page.php"> <img class="logo" src="images/Logo.png"> </a>

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
    <form method="post"  id="item-edit" enctype="multipart/form-data">
       <h1>Edit Barang</h1>
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
                <label for="name">Nama Barang</label>
                <input type="text" name="name" id="name" value = "<?=$items['name']?>" class= "input" required>			
            </div>
            <div class="form-field">
                <label for="hot">Hot Deal ? :</label>
                <select id="hot" name="hot"  value = "<?=$items['hot']?>" class= "input" required>
                    <option <?php if($items['hot'] === '0'){echo("selected = \"selected\"");}?>value="0">Tidak</option>
                    <option <?php if($items['hot'] === '1'){echo("selected = \"selected\"");}?>value="1">Ya</option>
                </select>
            </div>
            <div class="form-field">
                <label for="event">Event :</label>
                <select id="event" name="event"  value = "<?=$items['event']?>" class= "input" required>
                    <option <?php if($items['event'] === '0'){echo("selected = \"selected\"");}?>value="0">Null</option>
                    <option <?php if($items['event'] === '1'){echo("selected = \"selected\"");}?>value="1">1</option>
                    <option <?php if($items['event'] === '2'){echo("selected = \"selected\"");}?>value="2">2</option>
                    <option <?php if($items['event'] === '3'){echo("selected = \"selected\"");}?>value="3">3</option>
                </select>
            </div>
            <div class="form-field">
                <label for="supplier">Supplier :</label>
                <select id="supplier" name="supplier" class= "input"  required>
                    <option <?php if($items['supplier'] === 'DeGold'){echo("selected = \"selected\"");}?>value="DeGold">DeGold</option>
                    <option <?php if($items['supplier'] === 'UBS'){echo("selected = \"selected\"");}?>value="UBS">UBS</option>
                    <option <?php if($items['supplier'] === 'Lotus'){echo("selected = \"selected\"");}?>value="Lotus">Lotus</option>
                    <option <?php if($items['supplier'] === 'YT'){echo("selected = \"selected\"");}?>value="YT">YT</option>
                    <option <?php if($items['supplier'] === 'Kinghalim'){echo("selected = \"selected\"");}?>value="Kinghalim">Kinghalim</option>
                    <option <?php if($items['supplier'] === 'Citra'){echo("selected = \"selected\"");}?>value="Citra">Citra</option>
                    <option <?php if($items['supplier'] === 'HWT'){echo("selected = \"selected\"");}?>value="HWT">HWT</option>
                    <option <?php if($items['supplier'] === 'BG'){echo("selected = \"selected\"");}?>value="BG">Bulgari</option>
                    <option <?php if($items['supplier'] === 'Ayu'){echo("selected = \"selected\"");}?>value="Ayu">Ayu</option>
                    <option <?php if($items['supplier'] === 'SDW'){echo("selected = \"selected\"");}?>value="SDW">SJW</option>
                    <option <?php if($items['supplier'] === 'Hala'){echo("selected = \"selected\"");}?>value="Hala">Hala</option>
                    <option <?php if($items['supplier'] === 'Hartadinata'){echo("selected = \"selected\"");}?>value="Hartadinata">Amero</option>
                    <option <?php if($items['supplier'] === 'MT'){echo("selected = \"selected\"");}?>value="MT">MT</option>
                    <option <?php if($items['supplier'] === 'KAP'){echo("selected = \"selected\"");}?>value="KAP">KAP</option>
                    <option <?php if($items['supplier'] === 'Other'){echo("selected = \"selected\"");}?>value="Other">Other</option>
                </select>
                            
            </div>
            <div class="form-field">
                <label for="category">Kategori :</label>
                <select id="category" name="category"  class= "input" required>
                    <option <?php if($items['category'] === 'Ring'){echo("selected = \"selected\"");}?>value="Ring">Cincin</option>
                    <option <?php if($items['category'] === 'Earings'){echo("selected = \"selected\"");}?>value="Earings">Anting</option>
                    <option <?php if($items['category'] === 'Necklace'){echo("selected = \"selected\"");}?>value="Necklace">Kalung</option>
                    <option <?php if($items['category'] === 'Bracelet'){echo("selected = \"selected\"");}?>value="Bracelet">Gelang</option>
                    <option <?php if($items['category'] === 'Pendant'){echo("selected = \"selected\"");}?>value="Pendant">Liontin</option>
                    <option <?php if($items['category'] === 'Bangle'){echo("selected = \"selected\"");}?>value="Bangle">Gelondong</option>
                    <option <?php if($items['category'] === 'Kids'){echo("selected = \"selected\"");}?>value="Kids">Anak</option>
                    <option <?php if($items['category'] === 'Dubai gold'){echo("selected = \"selected\"");}?>value="Dubai gold">Dubai</option>
                    <option <?php if($items['category'] === 'Gold bar'){echo("selected = \"selected\"");}?>value="Gold bar">LM</option>
                </select>			
            </div>
            <div class="form-field">
  				<label for="code">Kode :</label>
                <input type="text" id="code" name="code" value = "<?=$items['code']?>"class= "input">
  			</div>
            <div class="form-field">
                <label for="fileToUpload">Gambar :</label>
  			    <input type="file" id="fileToUpload" name="fileToUpload" class="input" >
  			</div>
            <div class="form-field">
  				<label for="weight">Berat :</label>
                <input type="text" id="weight" name="weight" value = "<?=$items['weight']?>" class= "input" required>
  			</div>
            <div class="form-field">
  			    <label for="size">Ukuran :</label>
                <input type="text" id="size" name="size" value = "<?=$items['size']?>" class= "input">			
  			</div>
            <div class="form-field">
                <label for="quantity">Stok :</label>
  			    <input type="text" id="quantity" name="quantity" value = "<?=$items['quantity']?>" class= "input" required>
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
                echo ("<td><img class=\"logo\" src=\"../item-image/".$item['image']."\"</td>");
                echo("<td>");
                echo('<a href="size_input.php?itemid='.$item['itemid'].'">Tambah Size  /</a>');
                echo('<a href="item_edit1.php?itemid='.$item['itemid'].'"> Edit /</a>');
                echo('<a href="item_delete.php?itemid='.$item['itemid'].'"> Delete</a>');
                echo ("</td></tr>");
            ?>
        </table>
        <br>
        <br>
        <table class="att">
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
                if ($row['attributeid'] != $items['attributeid']){
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