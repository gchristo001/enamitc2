<?php
require_once "pdo.php";
session_start();


if ( $_SESSION['userid'] != 4) {
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
        header("Location: admin_access.php");
        return;
    }
    else{
        $_SESSION['userid'] = $userid['userid'];
        header("Location: profile.php");
        return;
    }

    }



    if(isset($_POST['search'])){
        $sql = 
        " SELECT * 
        FROM users
        WHERE userid LIKE :userid 
        AND phone LIKE :phone
        AND username LIKE :name
        ORDER BY userid DESC
        LIMIT 100
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ":userid" => "%".$_POST['userid']."%",
            ":name" => "%".$_POST['name']."%",
            ":phone" => "%".$_POST['phone']."%"));
        $users = $stmt->fetchALL(PDO::FETCH_ASSOC);
    }


    


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css">

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
            padding-left: 35%;
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
            
        td:nth-of-type(1):before { content: "Userid"; }
        td:nth-of-type(2):before { content: "Username"; }
        td:nth-of-type(3):before { content: "Email"; }
        td:nth-of-type(4):before { content: "Alamat"; }
        td:nth-of-type(5):before { content: "No WA"; }
        td:nth-of-type(6):before { content: "Tanggal Lahir"; }
        td:nth-of-type(7):before { content: "Member since"; }
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
    <form method="post"  id="admin_access-input">
       <h1>Cek Akun</h1>
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
                <label for="userid">Userid/NoWA :</label>
                <input type="text" name="userid" id="userid" class= "input" required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Go" class="button">
  			</div>
            
       </form>
         <br>
         <hr>
       <form method="post"  id="admin_access-search">
       <h1>Cari Akun</h1>
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
                <label for="userid">Userid :</label>
                <input type="text" name="userid" id="userid" class= "input">			
            </div>
            <div class="form-field">
                <label for="phone">No Wa :</label>
                <input type="text" name="phone" id="phone" class= "input">			
            </div>
            <div class="form-field">
                <label for="name">Nama :</label>
                <input type="text" name="name" id="name" class= "input">			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="search" value="Search" class="button">
  			</div>
            
       </form>
       
    </div>

    <div class="box-table">
    <table>
            <tr>
              <th>Userid</th>
              <th>Username</th>
              <th>Email</th>
              <th>Alamat</th>
              <th>No Wa</th>
              <th>Tanggal Lahir</th>
              <th>Member Since</th>
            </tr>
            
            <?php
            if(!empty($users)){
                foreach ($users as $row) {
                    echo ("<form method=\"post\">");
                    echo ("<tr>");
                    echo ("<td>".$row['userid']."</td>");
                    echo ("<td>".$row['username']."</td>");
                    echo ("<td>".$row['email']."</td>");
                    echo ("<td>".$row['address']."</td>");
                    echo ("<td>".$row['phone']."</td>");
                    echo ("<td>".$row['birthday']."</td>");
                    echo ("<td>".$row['member_since']."</td>");
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