<?php
require_once "pdo.php";
session_start();


if ( $_SESSION['userid'] != 1) {
    die("ACCESS DENIED");
}

    if ( isset($_POST['action'])){
        $sql = "SELECT itemid from items WHERE itemid = :itemid OR name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':itemid' => $_POST['itemid'],
            ':name' => $_POST['itemid'] ));
        $itemid = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if(empty($itemid['itemid'])){
            $_SESSION['error'] = 'Barang Tidak Ditemukan';
            header("Location: size_input2.php");
            return;
        }
        else{
            header("Location:size_input.php?itemid=".$itemid['itemid']);
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
    <form method="post"  id="order-input">
       <h1>Tambah Size</h1>
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
                <label for="itemid">Nama Barang/Id Barang :</label>
                <input type="text" name="itemid" id="itemid" class= "input" required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Go" class="button">
  			</div>
            
       </form>
    </div>


    </section>

<!-- banner section ends -->

</body>
</html>