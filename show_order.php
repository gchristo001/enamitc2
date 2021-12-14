<?php
require_once "pdo.php";
session_start();


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}



if(isset($_POST['action'])){
    $sql = 
    " SELECT 
    orders.orderid as orderid,
    orders.orderdate,
    orders.userid,
    orders.status,
    users.username,
    users.phone,
    users.address,
    item_attributes.attributeid as attributeid,
    item_attributes.size as size,
    item_attributes.weight as weight,
    item_attributes.price as price,
    items.name,
    items.image
    FROM orders
    LEFT JOIN users
    ON orders.userid = users.userid 
    LEFT JOIN item_attributes
    ON orders.attributeid = item_attributes.attributeid
    LEFT JOIN items
    ON item_attributes.itemid = items.itemid
    WHERE orders.status ='approved' 
    AND orders.userid LIKE :userid 
    AND users.phone LIKE :phone
    AND orders.orderdate LIKE :date
    AND items.name LIKE :name
    ORDER BY orderid DESC
    LIMIT 200
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ":userid" => "%".$_POST['userid']."%",
        ":date" => "%".$_POST['date']."%",
        ":phone" => "%".$_POST['phone']."%",
        ":name" => "%".$_POST['name']."%"));
    $orders = $stmt->fetchALL(PDO::FETCH_ASSOC);
}






?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Online</title>

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
    <form method="post"  id="order-input">
       <h1>Filter Order Online</h1>
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
                <label for="name">Nama Barang :</label>
                <input type="text" name="name" id="name" class= "input" >			
            </div>           
            <div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Lihat" class="button">
  			</div>
       </form>
    </div>

   

    <div class="box-table">
    <table>
            <tr>
              <th>OrderId</th>
              <th>OrderDate</th>
              <th>Status</th>
              <th>Userid</th>
              <th>Username</th>
              <th>No WA</th>
              <th>Alamat</th>
              <th>Nama Barang</th>
              <th>Gambar</th>
              <th>Size</th>
              <th>Gram</th>
              <th>Harga</th>
            </tr>
            
            <?php
            if(!empty($orders)){
                foreach ($orders as $row) {
                    echo ("<form method=\"post\">");
                    echo ("<input type=\"hidden\" name=\"att_id\" value=\"".$row['attributeid']."\">");
                    echo ("<input type=\"hidden\" name=\"orderid\" value=\"".$row['orderid']."\">");
                    echo ("<tr>");
                    echo ("<td>".$row['orderid']."</td>");
                    echo ("<td>".$row['orderdate']."</td>");
                    echo ("<td>".$row['status']."</td>");
                    echo ("<td>".$row['userid']."</td>");
                    echo ("<td>".$row['username']."</td>");
                    echo ("<td>".$row['phone']."</td>");
                    echo ("<td>".$row['address']."</td>");
                    echo ("<td>".$row['name']."</td>");
                    echo ("<td><img class=\"logo\" src=\"item-image/".$row['image']."\"</td>");
                    echo ("<td>".$row['size']."</td>");
                    echo ("<td>".$row['weight']."</td>");
                    echo ("<td>".$row['price']."</td>");
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