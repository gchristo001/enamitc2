<?php
require_once "pdo.php";
session_start();

if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}

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
WHERE redeem.status ='pending'
ORDER BY redeemdate DESC
";
$stmt = $pdo->query($sql);
$orders = $stmt->fetchALL(PDO::FETCH_ASSOC);

    if(isset($_POST['action'])){
        if($_POST['action'] == 'Approve'){
            $stmt = $pdo->prepare("UPDATE redeem SET status = 'Approved' WHERE redeemid = :redeemid");
            $stmt->execute(array(":redeemid" => $_POST['redeemid']));
        }
        if($_POST['action'] == 'Reject'){
            $stmt = $pdo->prepare("UPDATE redeem SET status = 'Canceled' WHERE redeemid = :redeemid");
            $stmt->execute(array(":redeemid" => $_POST['redeemid']));
            $stmt = $pdo->prepare("UPDATE prizes SET quantity = quantity + 1  WHERE prizeid = :prizeid");
            $stmt->execute(array(":prizeid" => $_POST['prizeid']));
        }
        if($_POST['action'] == 'Delete'){
            $stmt = $pdo->prepare("UPDATE redeem SET status = 'Canceled' WHERE redeemid = :redeemid");
            $stmt->execute(array(":redeemid" => $_POST['redeemid']));
            $stmt = $pdo->prepare("UPDATE prizes SET quantity = 0  WHERE prizeid = :prizeid");
            $stmt->execute(array(":prizeid" => $_POST['prizeid']));
        }
        header("Location: prize_confirm.php");
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

<section class="container">

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
              <th>Approve</th>
              <th>Reject</th>
              <th>Delete</th>
            </tr>
            
            <?php
            foreach ($orders as $row) {
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
                echo ("<td><img class=\"logo\" src=\"item-image/".$row['image']."\"</td>");
                echo ("<td><input type=\"submit\" name=\"action\"class=\"approve\"value=\"Approve\"onClick=\"return confirm('Approve?') \"></td><td><input type=\"submit\"name=\"action\" class=\"reject\"value=\"Reject\"onClick=\"return confirm('Reject?') \"></td>");
                echo ("<td><input type=\"submit\" name=\"action\"class=\"delete\"value=\"Delete\"onClick=\"return confirm('Hadiah sudah habis?') \"></td>");
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