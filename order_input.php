<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}


if ( isset($_POST['action'])){
    
    $sql = "SELECT userid from users WHERE userid = :userid OR phone = :phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':userid' => $_POST['userid'],
        ':phone' => $_POST['userid'] ));
    $userid = $stmt->fetch(PDO::FETCH_ASSOC);

    if(empty($userid['userid'])){
        $_SESSION['error'] = 'User Belum Terdaftar';
        header("Location: order_input.php");
        return;
    }
    else{
    date_default_timezone_set('Asia/Jakarta');
    $inputdate = date("Y-m-d H:i:s");

    $sql = "INSERT INTO offline_order (offline_order_date, userid, weight, price, nomor_bon)
              VALUES (:offline_order_date, :userid, :weight, :price, :nomor_bon)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':offline_order_date' => $inputdate,
        ':userid' => $userid['userid'],
        ':weight' => $_POST['weight'],
        ':price' => $_POST['price'],
        ':nomor_bon' => $_POST['bon']));

    $_SESSION['success'] = 'Record Added';
    header("Location: order_input.php");
    return;
    }
}


$stmt = $pdo->query("SELECT * FROM offline_order LEFT JOIN users ON offline_order.userid = users.userid ORDER BY offline_order_date DESC LIMIT 60");
$offline_order = $stmt->fetchALL(PDO::FETCH_ASSOC);

?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Order</title>

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
            
        td:nth-of-type(1):before { content: "OrderId"; }
        td:nth-of-type(2):before { content: "Tanggal"; }
        td:nth-of-type(3):before { content: "Userid"; }
        td:nth-of-type(4):before { content: "Nama"; }
        td:nth-of-type(5):before { content: "No WA"; }
        td:nth-of-type(6):before { content: "Berat"; }
        td:nth-of-type(7):before { content: "Harga(K)"; }
        td:nth-of-type(8):before { content: "Nomor Bon"; }
        td:nth-of-type(9):before { content: "Aksi"; }
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
    <form method="post"  id="order-input">
       <h1>Input Order</h1>
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
                <label for="userid">Userid/No. Wa :</label>
                <input type="text" name="userid" id="userid" class= "input" required>			
            </div>
            <div class="form-field">
  				<label for="weight">Berat Barang:</label>
                <input type="text" id="weight" name="weight" class= "input" required>
  			</div>
            <div class="form-field">
  				<label for="price">Harga :</label>
                <input type="text" id="price" name="price" class= "input" required>
  			</div>
            <div class="form-field">
  				<label for="bon">Nomor Bon :</label>
                <input type="text" id="bon" name="bon" class= "input" required>
  			</div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Insert" class="button">
  			</div>
            
       </form>
    </div>

   

    <div class="box-table">
        <table>
            <tr>
              <th>Order id</th>
              <th>Order date</th>
              <th>Userid</th>
              <th>Nama</th>
              <th>No WA</th>
              <th>Berat</th>
              <th>Harga (K)</th>
              <th>Nomor Bon</th>
              <th>Aksi</th>
            </tr>
            
            <?php
            foreach ($offline_order as $row) {
                echo ("<tr>");
                echo ("<td>".$row['offline_order_id']."</td>");
                echo ("<td>".$row['offline_order_date']."</td>");
                echo ("<td>".$row['userid']."</td>");
                echo ("<td>".$row['username']."</td>");
                echo ("<td>".$row['phone']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                echo ("<td>".$row['nomor_bon']."</td>");
                echo ('<td><a href="order_edit1.php?offline_order_id='.$row['offline_order_id'].'">Edit /</a>');                             
                echo ('<a href="order_delete.php?offline_order_id='.$row['offline_order_id'].'">Delete</a></td>');                             
                echo ("</tr>");
            }
            ?>
        </table>
    </div>





</section>

<!-- banner section ends -->










</body>
</html>