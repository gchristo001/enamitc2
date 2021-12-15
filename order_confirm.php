<?php
require_once "pdo.php";
session_start();

if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}




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
WHERE orders.status ='pending'
ORDER BY orderid DESC
";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchALL(PDO::FETCH_ASSOC);



    if(isset($_POST['action'])){
        if($_POST['action'] == 'Approve'){
            $stmt = $pdo->prepare("UPDATE orders SET status = 'Approved' WHERE orderid = :orderid");
            $stmt->execute(array(":orderid" => $_POST['orderid']));
        }
        if($_POST['action'] == 'Reject'){
            $stmt = $pdo->prepare("UPDATE orders SET status = 'Canceled' WHERE orderid = :orderid");
            $stmt->execute(array(":orderid" => $_POST['orderid']));
            $stmt = $pdo->prepare("UPDATE item_attributes SET quantity = quantity + 1  WHERE attributeid = :attributeid");
            $stmt->execute(array(":attributeid" => $_POST['att_id']));
        }
        if($_POST['action'] == 'Delete'){
            $stmt = $pdo->prepare("UPDATE orders SET status = 'Canceled' WHERE orderid = :orderid");
            $stmt->execute(array(":orderid" => $_POST['orderid']));
            $stmt = $pdo->prepare("UPDATE item_attributes SET quantity = 0  WHERE attributeid = :attributeid");
            $stmt->execute(array(":attributeid" => $_POST['att_id']));
        }
        header("Location: order_confirm.php");
        return;
    }





?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Order</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="admin.css">

    <!-- custom js file link  -->
    <script src="admin_script.js"defer></script>

    <style>
        .delete{
        color: #fff;
        width: 10rem;
        height: 34px;
        background-color: grey;
        border-radius: 5px;
        }
        
        td:nth-of-type(1):before { content: "Orderid"; }
        td:nth-of-type(2):before { content: "Tanggal"; }
        td:nth-of-type(3):before { content: "Status"; }
        td:nth-of-type(4):before { content: "Userid"; }
        td:nth-of-type(5):before { content: "Nama"; }
        td:nth-of-type(6):before { content: "No WA"; }
        td:nth-of-type(7):before { content: "Alamat"; }
        td:nth-of-type(8):before { content: "Barang"; }
        td:nth-of-type(9):before { content: "Gambar"; }
        td:nth-of-type(10):before { content: "Size"; }
        td:nth-of-type(11):before { content: "Berat"; }
        td:nth-of-type(12):before { content: "Harga"; }

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

<section class="container">

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
              <th>Approve</th>
              <th>Reject</th>
              <th>Delete</th>
            </tr>
            
            <?php
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
                echo ("<td><input type=\"submit\" name=\"action\"class=\"approve\"value=\"Approve\"onClick=\"return confirm('Approve?') \"></td><td><input type=\"submit\"name=\"action\" class=\"reject\"value=\"Reject\"onClick=\"return confirm('Reject?') \"></td>");
                echo ("<td><input type=\"submit\" name=\"action\"class=\"delete\"value=\"Delete\"onClick=\"return confirm('Barang sudah habis?') \"></td>");
                echo ("</tr>");
                echo ("</form>");
            }
            ?>
    </table>
    
    </div>

</section>

<!-- banner section ends -->










</body>
</html>