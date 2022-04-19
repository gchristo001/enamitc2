<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}



if(isset($_POST['action'])){
    $sql = 
    " SELECT 
    redeem.prizeid as prizeid,
    redeem.redeemdate,
    redeem.redeemid,
    redeem.userid,
    redeem.status,
    users.username,
    users.phone,
    prizes.name,
    prizes.image
    FROM redeem
    LEFT JOIN users
    ON redeem.userid = users.userid 
    LEFT JOIN prizes
    ON prizes.prizeid = redeem.prizeid
    WHERE redeem.userid LIKE :userid 
	 AND redeem.status = 'Approved'
    AND users.phone LIKE :phone
    AND redeem.redeemdate LIKE :date
    AND prizes.name LIKE :prize
    ORDER BY redeemdate DESC
    LIMIT 200
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ":userid" => "%".$_POST['userid']."%",
        ":date" => "%".$_POST['date']."%",
        ":phone" => "%".$_POST['phone']."%",
        ":prize" => "%".$_POST['prize']."%"));
    $redeems = $stmt->fetchALL(PDO::FETCH_ASSOC);
}






?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penukaran Hadiah</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css?<?=filemtime('admin.css');?>">

    <!-- custom js file link  -->
    <script src="admin_script.js"defer></script>

    <style>
        @media (max-width: 400px) {
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
            
        td:nth-of-type(1):before { content: "RedeemId"; }
        td:nth-of-type(2):before { content: "Tanggal"; }
        td:nth-of-type(3):before { content: "Status"; }
        td:nth-of-type(4):before { content: "Userid"; }
        td:nth-of-type(5):before { content: "Nama"; }
        td:nth-of-type(6):before { content: "No Wa"; }
        td:nth-of-type(7):before { content: "Hadiah"; }
        td:nth-of-type(8):before { content: "Gambar"; }
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
    <form method="post"  id="order-input">
       <h1>Filter Penukaran Hadiah</h1>
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
                <label for="userid">Userid:</label>
                <input type="text" name="userid" id="userid" class= "input" >			
            </div>
            <div class="form-field">
                <label for="phone">No. Wa :</label>
                <input type="text" name="phone" id="phone" class= "input" >			
            </div>
            <div class="form-field">
  				<label for="date">Tanggal :</label>
                <input type="date" id="date" name="date" value="" class= "input" >
  			</div>  
            <div class="form-field">
  				<label for="prize">Nama Hadiah :</label>
                <input type="text" id="prize" name="prize" value="" class= "input" >
  			</div>                 
            <div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Lihat" class="button">
  			</div>
       </form>
    </div>

   

    <div class="box-table">
    <table>
            <tr>
              <th>Redeem Id</th>
              <th>Redeem Date</th>
              <th>Status</th>
              <th>Userid</th>
              <th>Username</th>
              <th>No WA</th>
              <th>Nama Hadiah</th>
              <th>Gambar</th>
            </tr>
            
            <?php
            if(!empty($redeems)){
                foreach ($redeems as $row) {
                    echo ("<form method=\"post\">");
                    echo ("<input type=\"hidden\" name=\"prizeid\" value=\"".$row['prizeid']."\">");
                    echo ("<input type=\"hidden\" name=\"redeemid\" value=\"".$row['redeemid']."\">");
                    echo ("<tr>");
                    echo ("<td>".$row['redeemid']."</td>");
                    echo ("<td>".$row['redeemdate']."</td>");
                    echo ("<td>".$row['status']."</td>");
                    echo ("<td>".$row['userid']."</td>");
                    echo ("<td>".$row['username']."</td>");
                    echo ("<td>".$row['phone']."</td>");
                    echo ("<td>".$row['name']."</td>");
                    echo ("<td><img class=\"logo\" src=\"prize-image/".$row['image']."\"</td>");
                    echo ("</tr>");
                    echo ("</form>");
                }
            }
            ?>
    </table>
    
    </div>


</section>

<!-- banner section ends -->










</body>
</html>