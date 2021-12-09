<?php
require_once "pdo.php";
session_start();

if ( $_SESSION['userid'] != 1) {
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

<section class="container">

    <div class="box">
    <table>
            <tr>
              <th>OrderId</th>
              <th>OrderDate</th>
              <th>Status</th>
              <th>Userid</th>
              <th>Username</th>
              <th>No WA</th>
              <th>Nama Barang</th>
              <th>Gambar</th>
              <th>Size</th>
              <th>Gram</th>
              <th>Harga</th>
              <th>Approve</th>
              <th>Reject</th>
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
                echo ("<td>".$row['name']."</td>");
                echo ("<td><img class=\"logo\" src=\"item-image/".$row['image']."\"</td>");
                echo ("<td>".$row['size']."</td>");
                echo ("<td>".$row['weight']."</td>");
                echo ("<td>".$row['price']."</td>");
                echo ("<td><input type=\"submit\" name=\"action\"class=\"approve\"value=\"Approve\"onClick=\"return confirm('Approve?') \"></td><td><input type=\"submit\"name=\"action\" class=\"reject\"value=\"Reject\"onClick=\"return confirm('Reject?') \"></td>");
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