<?php
require_once "pdo.php";
session_start();

if ( $_SESSION['userid'] != 10) {
     die("ACCESS DENIED");
}
if (isset($_POST['itemid'])){
$sql = "UPDATE items SET name = :name, supplier = :supplier, category = :category, color = :color, size = :size, weight = :weight, price = :price
        WHERE itemid = :itemid";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':name' => $_POST['name'],
    ':supplier' => $_POST['supplier'],
    ':category' => $_POST['category'],
    ':color' => $_POST['color'],
    ':size' => $_POST['size'],
    ':weight' => $_POST['weight'],
    ':price' => $_POST['price'],
    ':itemid' => $_POST['itemid']));
if (!empty($_POST['image'])){
  $sql = "UPDATE items SET image = :image WHERE itemid = :itemid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
      ':image' => $_POST['image'],
      ':itemid' => $_POST['itemid']  ));
}
$_SESSION['success'] = 'Record Updated';
$redirect = 'Location: edit_item.php?itemid='.$_POST['itemid'];
header( $redirect ) ;
return;
}

$sql = $pdo->query("SELECT * FROM items ORDER BY itemid desc limit 10");
$rows = $sql->fetchAll(PDO::FETCH_ASSOC);

if (empty($_GET['itemid'])) {
  $_SESSION['error'] = "Missing itemid";
}
else{
  $stmt = $pdo->prepare("SELECT * FROM items where itemid = :xyz");
  $stmt->execute(array(":xyz" => $_GET['itemid']));
  $edited = $stmt->fetch(PDO::FETCH_ASSOC);
  if ( empty($edited) ) {
      $_SESSION['error'] = 'No Record Found';
}

}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta name="viewport" content="with=device-width, initial-scale = 1.0">
    <title>Toko Mas Enam ITC 2</title>
    <link rel="stylesheet" href="style_admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400;1,700&family=Open+Sans:ital,wght@0,600;1,300&family=Questrial&display=swap" rel="stylesheet">
  </head>
  <body>
      <nav>
            <a href="logout.php"><img class="logo" src="images/Logo.png"></a>
            <div class="nav-links">
              <ul>
                <li><a href = "data_input.php">INPUT</a>
                </li>
                <li><a href = "edit_item.php">UPDATE</a>
                </li>
                <li><a href = "delete_item.php">DELETE</a>
                </li>
                <li><a href = "insert_sales.php">INSERT SALES</a>
                </li>
              </ul>
            </div>
            <div class= "Logout">
                <a href="logout.php"><img class="icon" src="images/home.png"></a>
            </div>
      </nav>
   <section class="data-input-content">
     <div class="input-data">
       <form method="post"  id="item-input">
       <h2>Edit Data</h2>
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
						<label for="name">Item Name :</label>
						<input type="text" name="name" id="name" class= "input" required value="<?= $edited['name'] ?>">
						<small></small>
  			 </div>
  			 <div class="form-field">
  						<label for="supplier">Supplier :</label>
              <select id="supplier" name="supplier"class= "input" required >
                  <option <?php if($edited['supplier'] === 'DeGold'){echo("selected = \"selected\"");}?>value="DeGold">DeGold</option>
                  <option <?php if($edited['supplier'] === 'UBS'){echo("selected = \"selected\"");}?>value="UBS">UBS</option>
                  <option <?php if($edited['supplier'] === 'Lotus'){echo("selected = \"selected\"");}?>value="Lotus">Lotus</option>
                  <option <?php if($edited['supplier'] === 'YT'){echo("selected = \"selected\"");}?>value="YT">YT</option>
                  <option <?php if($edited['supplier'] === 'Kinghalim'){echo("selected = \"selected\"");}?>value="Kinghalim">Kinghalim</option>
                  <option <?php if($edited['supplier'] === 'Citra'){echo("selected = \"selected\"");}?>value="Citra">Citra</option>
                  <option <?php if($edited['supplier'] === 'HWT'){echo("selected = \"selected\"");}?>value="HWT">HWT</option>
                  <option <?php if($edited['supplier'] === 'Bulgari'){echo("selected = \"selected\"");}?>value="Bulgari">Bulgari</option>
                  <option <?php if($edited['supplier'] === 'Ayu'){echo("selected = \"selected\"");}?>value="Ayu">Ayu</option>
                  <option <?php if($edited['supplier'] === 'SJW'){echo("selected = \"selected\"");}?>value="SJW">SJW</option>
                  <option <?php if($edited['supplier'] === 'Hala'){echo("selected = \"selected\"");}?>value="Hala">Hala</option>
                  <option <?php if($edited['supplier'] === 'Amero'){echo("selected = \"selected\"");}?>value="Amero">Amero</option>
                  <option <?php if($edited['supplier'] === 'MT'){echo("selected = \"selected\"");}?>value="MT">MT</option>
              </select>
  						<small></small>
  			 </div>
  			 <div class="form-field">
  						<label for="category">Category :</label>
              <select id="category" name="category"  class= "input" required value="<?= $edited['category'] ?>">
                  <option <?php if($edited['category'] === 'Necklace'){echo("selected = \"selected\"");}?>value="Necklace">Necklace</option>
                  <option <?php if($edited['category'] === 'Bangle'){echo("selected = \"selected\"");}?>value="Bangle">Bangle</option>
                  <option <?php if($edited['category'] === 'Bracelet'){echo("selected = \"selected\"");}?>value="Bracelet">Bracelet</option>
                  <option <?php if($edited['category'] === 'Ring'){echo("selected = \"selected\"");}?>value="Ring">Ring</option>
                  <option <?php if($edited['category'] === 'Earings'){echo("selected = \"selected\"");}?>value="Earings">Earings</option>
                  <option <?php if($edited['category'] === 'Pendant'){echo("selected = \"selected\"");}?>value="Pendant">Pendant</option>
                  <option <?php if($edited['category'] === 'Kids'){echo("selected = \"selected\"");}?>value="Kids">Kids</option>
                  <option <?php if($edited['category'] === 'Dubai gold'){echo("selected = \"selected\"");}?>value="Dubai gold">Dubai gold</option>
                  <option <?php if($edited['category'] === 'Gold bar'){echo("selected = \"selected\"");}?>value="Gold bar">Gold bar</option>
              </select>
  						<small></small>
  			 </div>
  			 <div class="form-field">
  						<label for="color">Color :</label>
              <select id="color" name="color"  class= "input" required value="<?= $edited['color'] ?>">
                <option <?php if($edited['color'] === 'rosegold'){echo("selected = \"selected\"");}?>value="rosegold">Rose Gold</option>
                <option <?php if($edited['color'] === 'whitegold'){echo("selected = \"selected\"");}?>value="whitegold">White Gold</option>
                <option <?php if($edited['color'] === 'blackgold'){echo("selected = \"selected\"");}?>value="blackgold">Black Gold</option>
              </select>
  						<small></small>
  			 </div>
         <div class="form-field">
  						<label for="size">Size :</label>
              <input type="text" id="size" name="size" class= "input" value="<?= $edited['size'] ?>">
  						<small></small>
  			 </div>
         <div class="form-field">
  						<label for="weight">Weight :</label>
              <input type="text" id="weight" name="weight" class= "input" required value="<?= $edited['weight'] ?>">
  						<small></small>
  			 </div>
         <div class="form-field">
  						<label for="price">Price :</label>
              <input type="text" id="price" name="price" class= "input" value="<?= $edited['price'] ?>">
  						<small></small>
  			 </div>
         <div class="form-field">
              <label for="image">Item Image :</label>
  						<input type="file" id="image" name="image" accept="image/png, image/jpeg" class="input" >
  			 </div>
  			 <div class="form-field">
              <input type="hidden" name="itemid" value="<?= $edited['itemid'] ?>">
  						<input id="Reset" type="reset" value="Clear" class= "input">
  						<input id="Submit" type="submit" value="Update" class= "input">
  			 </div>
       </form>
       <div class="show-data">
         <form method="get" action="edit_item.php">
         <div class="form-field">
            <label for="itemid">Item ID :</label>
            <input type="text" name="itemid" id="itemid" class= "input" required value="<?= $edited['itemid'] ?>">
            <small></small>
            <input id="Submit" type="submit" value="Generate" class= "input">
            <small></small>
         </div>
        </form>
          <table>
            <tr>
              <th>Id</th>
              <th>Name</th>
              <th>Suppliier</th>
              <th>Category</th>
              <th>Color</th>
              <th>Size</th>
              <th>Weight</th>
              <th>Price</th>
              <th>Image</th>
              <th>Edit/Delete</th>
            </tr>
            <form method="post">
            <?php
          foreach ($rows as $row) {
              echo ("<tr>");
              echo ("<td>".$row['itemid']."</td>");
              echo ("<td>".$row['name']."</td>");
              echo ("<td>".$row['supplier']."</td>");
              echo ("<td>".$row['category']."</td>");
              echo ("<td>".$row['color']."</td>");
              echo ("<td>".$row['size']."</td>");
              echo ("<td>".$row['weight']."</td>");
              echo ("<td>".$row['price']."</td>");
              echo ("<td>".$row['image']."</td>");
              echo("<td>");
              echo('<a href="edit_item.php?itemid='.$row['itemid'].'">Edit</a> / ');
              echo('<a href="delete_item.php?itemid='.$row['itemid'].'">Delete</a>');
              echo ("</td></tr>");
          }
          ?>
           </form>
          </table>
       </div>
     </div>
   </section>
</body>
</html>
