<?php
require_once "pdo.php";
session_start();
error_reporting(0);


if ( !($_SESSION['userid'] == 1 || $_SESSION['userid'] == 4) ) {
    die("ACCESS DENIED");
}

if (isset($_GET['adid'])){
    $sql = "SELECT * FROM advert WHERE adid = :adid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(":adid" => $_GET['adid']));
    $adinfo = $stmt->fetch(PDO::FETCH_ASSOC);
}
else{
    $_SESSION['error'] = 'Id iklan tidak ditemukan';
    header("Location: advert_input.php");
    return;
}


if (isset($_POST['title'])){
    $sql = "UPDATE advert SET slot = 0
    WHERE slot = :slot";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':slot' => $_POST['slot'] ));

    if(!(!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] == UPLOAD_ERR_NO_FILE)){
        $temp = explode(".", $_FILES["fileToUpload"]["name"]);
        $newfilename = round(microtime(true)) . '.' . end($temp);
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "iklan/" . $newfilename);

        $sql = "UPDATE advert SET title =:title, description = :description, slot =:slot, image =:image
        WHERE adid = :adid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':adid' => $_GET['adid'],
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
            ':slot' => $_POST['slot'],
            ':image' => $newfilename));

        $_SESSION['success'] = 'Informasi iklan dan gambar berhasil diupdate';
        header("Location: advert_input.php");
        return;
        
    }
    else{
        $sql = "UPDATE advert SET title =:title, description = :description, slot =:slot
        WHERE adid = :adid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':adid' => $_GET['adid'],
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
            ':slot' => $_POST['slot']));

        $_SESSION['success'] = 'Informasi iklan berhasil diupdate';
        header("Location: advert_input.php");
        return;
    }
} 
    $sql = " SELECT * FROM advert
    ORDER BY adid DESC
    LIMIT 20";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Iklan</title>

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
            
        td:nth-of-type(1):before { content: "Id"; }
        td:nth-of-type(2):before { content: "Judul"; }
        td:nth-of-type(3):before { content: "Deskripsi"; }
        td:nth-of-type(4):before { content: "Slot"; }
        td:nth-of-type(5):before { content: "Gambar"; }
        td:nth-of-type(6):before { content: "Aksi"; }
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
                if($_SESSION['userid'] = 4){
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
    <form method="post"  id="advert-edit" enctype="multipart/form-data">
       <h1>Edit Iklan</h1>
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
                <label for="title">Judul :</label>
                <input type="text" name="title" id="title" class= "input" value = "<?=$adinfo['title']?>" required>			
            </div>
            <div class="form-field">
  				<label for="description">Deskripsi :</label>
                <textarea id="description" name="description" class= "input" rows="4" cols="50"><?php echo ($adinfo['description']); ?></textarea>
  			</div>
            <div class="form-field">
                <label for="slot">Slot :</label>
                <select id="slot" name="slot"  class= "input" required>
                    <option value="1"<?php if($adinfo['slot'] === '1'){echo("selected = \"selected\"");}?>>1</option>
                    <option value="2"<?php if($adinfo['slot'] === '2'){echo("selected = \"selected\"");}?>>2</option>
                    <option value="3"<?php if($adinfo['slot'] === '3'){echo("selected = \"selected\"");}?>>3</option>
                    <option value="0"<?php if($adinfo['slot'] === '0'){echo("selected = \"selected\"");}?>>Non-aktif</option>
                </select>
  			</div>
            <div class="form-field">
                <label for="fileToUpload">Gambar :</label>
  			    <input type="file" id="fileToUpload" name="fileToUpload" value=""  class="input">
  			</div>
  			<div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Update" class="button">
  			</div>
       
       </form>
    </div>

    <div class="box-table">
        <table>
            <tr>
              <th>Id</th>
              <th>Judul</th>
              <th>Deskripsi</th>
              <th>Slot</th>
              <th>Gambar</th>
              <th>Aksi</th> 
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['adid']."</td>");
                echo ("<td>".$row['title']."</td>");
                echo ("<td>".$row['description']."</td>");
                echo ("<td>".$row['slot']."</td>");
                echo ("<td><img class=\"logo\" src=\"iklan/".$row['image']."\" alt=\"test\"></td>");
                echo ('<td><a href="advert_edit.php?adid='.$row['adid'].'">Edit /</a>');          
                echo ('<a href="advert_delete.php?adid='.$row['adid'].'">Delete</a></td>');             
                echo ("</tr>");
            }
            ?>
        </table>
    </div>


</section>

<!-- banner section ends -->


</body>
</html>