<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}


    if ( isset($_POST['action'])){
        unset($_SESSION['jsflag']);
        unset($_SESSION['user']);
        unset($_SESSION['username']);
        unset($_SESSION['attid']);
        unset($_SESSION['weight']);
        header("Location: print_bon.php?orderid=".$_POST["orderid"]);
        return;
    }

    if ( isset($_POST['check_data'])){
        $sql = "SELECT username from users WHERE userid = :userid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':userid' => $_POST['userid']));
        $_SESSION['username'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT weight from item_attributes WHERE attributeid = :attributeid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':attributeid' => $_POST['attid']));
        $_SESSION['weight'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user'] = $_POST["userid"];
        $_SESSION['attid'] = $_POST["attid"];

        if(!$_SESSION['username']){
            $_SESSION['error'] = "User tidak ditemukan";
            header( 'Location: menu_print.php' );
            return;
        }
        if(!$_SESSION['weight']){
            $_SESSION['error'] = "Barang tidak ditemukan";
            header( 'Location: menu_print.php' );
            return;
        }
        $_SESSION['jsflag'] = 1;

        header( 'Location: menu_print.php' );
        return;
    }

    if ( isset($_POST['manual_order'])){
        date_default_timezone_set('Asia/Jakarta');
        $orderdate = date("Y-m-d H:i:s");
        $sql = "INSERT INTO orders (userid, admin, orderdate, attributeid, status)
        VALUES (:userid, :admin, :orderdate, :attributeid, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':userid' => $_POST['userid'],
            ':admin' => "toko",
            ':orderdate' => $orderdate,
            ':attributeid' => $_POST['attid'],
            ':status' => "Approved"));

        $sql = "UPDATE item_attributes SET quantity = quantity-1 WHERE attributeid=:attributeid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':attributeid' => $attid));
        
        $sql = "SELECT orderid from orders WHERE orderdate = :orderdate";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':orderdate' => $orderdate));
        $orderid = $stmt->fetch(PDO::FETCH_ASSOC);
        
        unset($_SESSION['jsflag']);
        unset($_SESSION['user']);
        unset($_SESSION['username']);
        unset($_SESSION['attid']);
        unset($_SESSION['weight']);

        header("Location: print_bon.php?orderid=".$orderid['orderid']);
        return;
    }
        
    if ( isset($_POST['print_manual'])){
        unset($_SESSION['jsflag']);
        unset($_SESSION['user']);
        unset($_SESSION['username']);
        unset($_SESSION['attid']);
        unset($_SESSION['weight']);
        header("Location: print_bon_manual.php");
        return;
    }
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bon</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css?<?=filemtime('admin.css');?>">

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

    <div class="box" style="display: flex; flex-direction: column;">
    <form method="post"  id="order-input">
       <h1>Print Bon</h1>
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
                <label for="orderid">Order ID :</label>
                <input type="text" onfocus=this.value='' name="orderid" id="orderid" class= "input" required>			
            </div>
            <div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Go" class="button">
  			</div>
            
       </form>
       <form method="post"  id="manual-order-input">
           <br>
           <br>
           <h2>Input Order</h2>
            <div class="form-field">
                <label for="userid">User Id :</label>
                <input type="text" onfocus=this.value='' name="userid" id="userid" class= "input" required>			
            </div>
            <p id = "data_user"> </p>
            <br>
            <div class="form-field">
                <label for="attid">Id Barang :</label>
                <input type="text" onfocus=this.value='' name="attid" id="attid" class= "input" required>			
            </div>
            <p id = "data_barang"> </p>
            <br>
            <div class="form-field">
                <input id="Submit" type="submit" name="check_data" value="Check Data" class="button">
  				<input id="manual_order" type="submit" name="manual_order" value="Buat Bon" class="button" style = "display: none;">
  			</div>
    
       </form>
       <form method="post"  id="manual-print">
           <br>
           <br>
            <div class="form-field">
  				<input id="Submit" type="submit" name="print_manual" value="Manual Print" class="button">
  			</div>
        </form>
    </div>


    </section>

<!-- banner section ends -->
<script>
window.onload = function() {
    var flag = "<?php echo($_SESSION['jsflag']);?>";
    var username = "<?php echo $_SESSION['username']['username'];?>";
    var weight = "<?php echo $_SESSION['weight']['weight'];?>";   	    

    if (flag == "1"){
        document.getElementById("data_user").innerHTML = "Nama = " + username ;
        document.getElementById("data_barang").innerHTML = "Berat = " + weight ;
        document.getElementById("manual_order").style.display = "block";
        document.getElementById("userid").value = "<?php echo $_SESSION['user'];?>";  
        document.getElementById("attid").value = "<?php echo $_SESSION['attid'];?>"; 
    }
    
}
</script>



</body>
</html>
