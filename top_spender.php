<?php
require_once "pdo.php";
session_start();


if ( $_SESSION['userid'] != 4) {
    die("ACCESS DENIED");
}


    if(isset($_POST['top_spender'])){

        $inputMonth = $_POST['month']; // Format: YYYY-MM
        list($year, $month) = explode('-', $inputMonth); // Extract year and month
    
        $stmt = $pdo->prepare(
            "SELECT u.userid, u.username, SUM(total_weight) AS total_weight_bought
            FROM users u
            JOIN (
                -- Calculate total weight from approved online orders within the selected month
                SELECT o.userid, SUM(ia.weight) AS total_weight
                FROM orders o
                JOIN item_attributes ia ON o.attributeid = ia.attributeid
                WHERE MONTH(o.orderdate) = :month
                  AND YEAR(o.orderdate) = :year
                  AND o.status = 'approved'
                GROUP BY o.userid
            
                UNION ALL
            
                -- Calculate total weight from offline orders within the selected month
                SELECT oo.userid, SUM(oo.weight) AS total_weight
                FROM offline_order oo
                WHERE MONTH(oo.offline_order_date) = :month
                  AND YEAR(oo.offline_order_date) = :year
                GROUP BY oo.userid
            ) AS total_weights ON u.userid = total_weights.userid
            GROUP BY u.userid
            ORDER BY total_weight_bought DESC
            LIMIT 10;"
        );
    
        // Bind parameters
        $stmt->bindParam(':month', $month, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    
        $stmt->execute();
        $topspender = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
       <h1>Lihat Top Spender</h1>
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
                <label for="month">Bulan :</label>
                <input type="month" name="month" id="month" class= "input">			
            </div>
            
            <div class="form-field">
  				<input id="Submit" type="submit" name="top_spender" value="Tampilkan" class="button">
  			</div>  
    </form>      
    </div>

    <div class="box-table">
    <?php 
    // Check if there's data in the $topspender array
    if (!empty($topspender)) {
        echo "<h1> Top Spender ". date("F", mktime(0, 0, 0, $month, 1))." ".$year. "</h1>";
    } 
    ?>
        <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Userid</th>
                <th>Nama</th>
                <th>Total gram</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Check if there's data in the $topspender array
            if (!empty($topspender)) {
                $rank = 1; // Initialize ranking number
                foreach ($topspender as $row) {
                    echo "<tr>";
                    echo "<td>" . $rank . "</td>";
                    echo "<td>" . htmlspecialchars($row['userid']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . number_format($row['total_weight_bought'],2) . "</td>";
                    echo "</tr>";
                    $rank++; // Increment rank for each user
                }
            } else {
                echo "<tr><td colspan='4'>No data available for the specified date range.</td></tr>";
            }
            ?>
        </tbody>
        </table>
    
    </div>
    </section>

<!-- banner section ends -->

</body>
</html>