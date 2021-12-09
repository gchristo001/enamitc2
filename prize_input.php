<?php
require_once "pdo.php";
session_start();
error_reporting(0);


if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}

if (isset($_POST['name'])){

    date_default_timezone_set('Asia/Jakarta');
    $inputdate = date("Y-m-d H:i:s");

    $temp = explode(".", $_FILES["fileToUpload"]["name"]);
    $newfilename = round(microtime(true)) . '.' . end($temp);
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "prize-image/" . $newfilename);

    $sql = "INSERT INTO prizes (name, cost, quantity, image)
              VALUES (:name, :cost, :quantity, :image)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':name' => $_POST['name'],
        ':cost' => $_POST['cost'],
        ':quantity' => $_POST['quantity'],
        ':image' => $newfilename));

    $stmt = $pdo->prepare("SELECT prizeid FROM prizes where name = :name");
    $stmt->execute(array(":name" => $_POST['name']));
    $prizeid = $stmt->fetch(PDO::FETCH_ASSOC);


    $_SESSION['success'] = 'Record Added';
    header("Location: prize_input.php");
    return;

} 
    $sql = " SELECT * FROM prizes
    ORDER BY prizeid DESC
    LIMIT 10";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Hadiah</title>

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
    <form method="post"  id="prize-input" action="prize_input.php" enctype="multipart/form-data">
       <h1>Input Hadiah</h1>
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
                <label for="name">Nama Hadiah :</label>
                <input type="text" name="name" id="name" class= "input" required>			
            </div>
            <div class="form-field">
  				<label for="code">Harga :</label>
                <input type="cost" id="cost" name="cost" class= "input">
  			</div>
            <div class="form-field">
  				<label for="quantity">Jumlah :</label>
                <input type="quantity" id="quantity" name="quantity" class= "input">
  			</div>
            <div class="form-field">
                <label for="fileToUpload">Gambar :</label>
  			    <input type="file" id="fileToUpload" name="fileToUpload" value=""  class="input" required>
  			</div>
  			<div class="form-field">
  				<input id="Submit" type="submit" name="action" value="Insert" class="button">
  			</div>
       
       </form>
    </div>

    <div class="box">
        <table>
            <tr>
              <th>Id</th>
              <th>Nama</th>
              <th>Harga</th>
              <th>Jumlah</th>
              <th>Gambar</th>
            </tr>
            
            <?php
            foreach ($rows as $row) {
                echo ("<tr>");
                echo ("<td>".$row['prizeid']."</td>");
                echo ("<td>".$row['name']."</td>");
                echo ("<td>".$row['cost']."</td>");
                echo ("<td>".$row['quantity']."</td>");
                echo ("<td><img class=\"logo\" src=\"prize-image/".$row['image']."\"</td>");
                echo ("</tr>");
            }
            ?>
        </table>
    </div>


</section>

<!-- banner section ends -->


</body>
</html>