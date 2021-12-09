<?php
require_once "pdo.php";
session_start();

if ( $_SESSION['userid'] != 1) {
     die("ACCESS DENIED");
}


if (isset($_POST['name'])){
$sql = "INSERT INTO items (name, supplier, category, color, size, weight, price, image)
          VALUES (:name, :supplier, :category, :color, :size, :weight, :price, :image)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':name' => $_POST['name'],
    ':supplier' => $_POST['supplier'],
    ':category' => $_POST['category'],
    ':color' => $_POST['color'],
    ':size' => $_POST['size'],
    ':weight' => $_POST['weight'],
    ':price' => $_POST['price'],
    ':image' => $_POST['image'] ));
$_SESSION['success'] = 'Record Added';
header( 'Location: data_input.php' ) ;
return;
}
$sql = $pdo->query("SELECT * FROM items ORDER BY itemid desc limit 10");
$rows = $sql->fetchAll(PDO::FETCH_ASSOC);

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
       <form method="post"  id="item-input" action="data_input.php">
       <h2>Input Data</h2>
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
						<input type="text" name="name" id="name" class= "input" required>
						<small></small>
  			 </div>
  			 <div class="form-field">
  						<label for="supplier">Supplier :</label>
              <select id="supplier" name="supplier"class= "input" required>
                  <option value="DeGold">DeGold</option>
                  <option value="UBS">UBS</option>
                  <option value="Lotus">Lotus</option>
                  <option value="YT">YT</option>
                  <option value="Kinghalim">Kinghalim</option>
                  <option value="Citra">Citra</option>
                  <option value="HWT">HWT</option>
                  <option value="Bulgari">Bulgari</option>
                  <option value="Ayu">Ayu</option>
                  <option value="SJW">SJW</option>
                  <option value="Hala">Hala</option>
                  <option value="Amero">Amero</option>
                  <option value="MT">MT</option>
              </select>
  						<small></small>
  			 </div>
  			 <div class="form-field">
  						<label for="category">Category :</label>
              <select id="category" name="category"  class= "input" required>
                  <option value="Necklace">Necklace</option>
                  <option value="Bangle">Bangle</option>
                  <option value="Bracelet">Bracelet</option>
                  <option value="Ring">Ring</option>
                  <option value="Earings">Earings</option>
                  <option value="Pendant">Pendant</option>
                  <option value="Kids">Kids</option>
                  <option value="Dubai gold">Dubai gold</option>
                  <option value="Gold bar">Gold bar</option>
              </select>
  						<small></small>
  			 </div>
  			 <div class="form-field">
  						<label for="color">Color :</label>
              <select id="color" name="color"  class= "input" required>
                <option value="rosegold">Rose Gold</option>
                <option value="whitegold">White Gold</option>
                <option value="whitegold">Black Gold</option>
              </select>
  						<small></small>
  			 </div>
         <div class="form-field">
  						<label for="size">Size :</label>
              <input type="text" id="size" name="size" class= "input">
  						<small></small>
  			 </div>
         <div class="form-field">
  						<label for="weight">Weight :</label>
              <input type="text" id="weight" name="weight" class= "input" required>
  						<small></small>
  			 </div>
         <div class="form-field">
  						<label for="price">Price :</label>
              <input type="text" id="price" name="price" class= "input">
  						<small></small>
  			 </div>
         <div class="form-field">
              <label for="image">Item Image :</label>
  						<input type="file" id="image" name="image" accept="image/png, image/jpeg" class="input" required>
  			 </div>
  			 <div class="form-field">
  						<input id="Reset" type="reset" value="Clear" class= "input">
  						<input id="Submit" type="submit" value="Insert" class= "input">
  			 </div>
       </form>
       <div class="show-data">
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
