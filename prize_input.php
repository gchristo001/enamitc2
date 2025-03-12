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
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "../prize-image/" . $newfilename);

    $sql = "INSERT INTO prizes (name, cost, quantity, image)
              VALUES (:name, :cost, :quantity, :image)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':cost' => $_POST['cost'],
        ':quantity' => $_POST['quantity'],
        ':image' => $newfilename));

    $stmt = $pdo->prepare("SELECT prizeid FROM prizes where name = :name");
    $stmt->execute(array(":name" => $_POST['name']));
    $prizeid = $stmt->fetch(PDO::FETCH_ASSOC);


    $_SESSION['success'] = 'Record Added';
    header("Location: prize_input.php");
    return;

} 
    $sql = " SELECT * FROM prizes
    ORDER BY prizeid DESC
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
    <title>Input Hadiah</title>

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
        td:nth-of-type(3):before { content: "Harga"; }
        td:nth-of-type(4):before { content: "Jumlah"; }
        td:nth-of-type(5):before { content: "Gambar"; }
        td:nth-of-type(6):before { content: "Aksi"; }
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
                if($_SESSION['userid'] = 4){
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
    <form method="post"  id="prize-input" action="prize_input.php" enctype="multipart/form-data">
       <h1>Input Hadiah</h1>
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
                <label for="name">Nama Hadiah :</label>
                <input type="text" name="name" id="name" class= "input" required>			
            </div>
            <div class="form-field">
  				<label for="code">Harga :</label>
                <input type="cost" id="cost" name="cost" class= "input">
  			</div>
            <div class="form-field">
  				<label for="quantity">Jumlah :</label>
                <input type="quantity" id="quantity" name="quantity" class= "input">
  			</div>
            <div class="form-field">
                <label for="fileToUpload">Gambar :</label>
  			    <input type="file" id="fileToUpload" name="fileToUpload" value=""  class="input" required>
  			</div>
  			<div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Insert" class="button">
  			</div>
       
       </form>
    </div>

    <div class="box-table">
        <table>
            <tr>
              <th>Id</th>
              <th>Nama</th>
              <th>Harga</th>
              <th>Jumlah</th>
              <th>Gambar</th>
              <th>Aksi</th> 
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['prizeid']."</td>");
                echo ("<td>".$row['name']."</td>");
                echo ("<td>".$row['cost']."</td>");
                echo ("<td>".$row['quantity']."</td>");
                echo ("<td><img class=\"logo\" src=\"../prize-image/".$row['image']."\"loading=\"lazy\"></td>");
                echo ('<td><a href="prize_edit1.php?prizeid='.$row['prizeid'].'">Edit /</a>');          
                echo ('<a href="prize_delete.php?prizeid='.$row['prizeid'].'">Delete</a></td>');             
                echo ("</tr>");
            }
            ?>
        </table>
    </div>


</section>

<!-- banner section ends -->


</body>
</html>