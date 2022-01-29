<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

$badge = count($_SESSION['cart']);

if(!isset($_SESSION['userid'])){
    header('Location : login.php');
    return;
}

$sql ="SELECT 
COUNT(orderid) as totalorder
FROM orders 
WHERE status = 'Approved' 
AND userid = :userid
AND orderdate > '2022-01-31 00:00'
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $_SESSION['userid']));
$totalorder =  $stmt->fetch(PDO::FETCH_ASSOC);

$sql ="SELECT 
COUNT(id) as totalredeem
FROM ticket_redeem
WHERE userid = :userid
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
        ':userid' => $_SESSION['userid']));
$totalredeem =  $stmt->fetch(PDO::FETCH_ASSOC);

$ticket = 2*$totalorder['totalorder'] - $totalredeem['totalredeem'];

$sql = "SELECT *
FROM ticket_redeem
WHERE userid = :userid 
ORDER BY date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
    ':userid' => $_SESSION['userid']));
$redeems = $stmt->fetchALL(PDO::FETCH_ASSOC);



if(isset($_COOKIE['prize'])){

        date_default_timezone_set('Asia/Jakarta');
        $date = date("Y-m-d H:i:s");
        $sql = "INSERT INTO ticket_redeem (userid, date, prize, status)
        VALUES (:userid, :date, :prize, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':userid' => $_SESSION['userid'],
            ':date' => $date,
            ':prize' => $_COOKIE['prize'],
            ':status' => 'pending'));
	
    setcookie("prize", "", time()-3600);
	header("Location: roullete.php");
	return;
}
?>


<!DOCTYPE html>
<html lang="en">

	<head>
	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Spesial Imlek </title>

		<!-- font awesome cdn link  -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

		<!-- custom css file link  -->
		<link rel="stylesheet" href="style.css?<?=filemtime('style.css');?>">

		<script src="roulette/jquery.js"></script>
		<script src="roulette/phaser.js"></script>
		
		<style>
			body{
				text-align: center;
			}
            .roulette{
                padding-top: 80px;
            }
            .ticket span{
                font-size: 2.3rem;
                color:#AE9238;
            }
            .ticket h3{
                font-size: 2.5rem;
                color:#FFF;
            }
            .ticket{
                padding: 10px;
                margin: auto;
                width: auto;
            }

		</style>

<script>
        function ajaxgo(j){
            var data = new FormData();
            data.append("itemid",j);
        

            var xhr = new XMLHttpRequest();
            xhr.open("POST","addcart.php");
            xhr.onload = function(){
                console.log(this.response);
            };

            xhr.send(data);

            

            var oReq = new XMLHttpRequest(); // New request object
            oReq.onload = function() {
            document.getElementById('notif').innerText = this.responseText;
            };
            oReq.open("get", "addcart.php", true);
    
            oReq.send();
            
            return false;
        }

        function reqListener () {
                 console.log(this.responseText);
        }
   </script>
		
		
	</head>
	<body>

<!-- header section starts  -->

<header class="header">

    <a href="index.php" class="logo"> <img src="images/Logo.png"> </a>
    
    <nav class="navbar">
        <ul>
            <li><a href="index.php">HOME</a></li>
            <li><a>BELANJA </a>                
                <ul>
                    <li><a href = "product_list.php?category=Necklace">Kalung</a></li>
                    <li><a href = "product_list.php?category=Bangle">Gelondong</a></li>
                    <li><a href = "product_list.php?category=Bracelet">Gelang</a></li>
                    <li><a href = "product_list.php?category=Ring">Cincin</a></li>
                    <li><a href = "product_list.php?category=Earings">Anting</a></li>
                    <li><a href = "product_list.php?category=Pendant">Liontin</a></li>
                    <li><a href = "product_list.php?category=Kids">Anak</a></li>
                    <li><a href = "product_list.php?category=Dubai gold">Dubai</a></li>
                    <li><a href = "product_list.php?category=Gold bar">Emas Batang</a></li>
                    <li><a href = "product_list.php?category=Sold out">Sold Out</a></li>
                </ul>
            </li>
            <li><a>KOLEKSI</a>
                <ul>
                    <li><a href = "product_list.php?supplier=DeGold">DeGold</a></li>
                    <li><a href = "product_list.php?supplier=UBS">UBS</a></li>
                    <li><a href = "product_list.php?supplier=Citra">Citra</a></li>
                    <li><a href = "product_list.php?supplier=Lotus">Lotus</a></li>
                    <li><a href = "product_list.php?supplier=YT">YT</a></li>
                    <li><a href = "product_list.php?supplier=Kinghalim">Kinghalim</a></li>
                    <li><a href = "product_list.php?supplier=HWT">HWT</a></li>
                    <li><a href = "product_list.php?supplier=BG">BG</a></li>
                    <li><a href = "product_list.php?supplier=Ayu">Ayu</a></li>
                    <li><a href = "product_list.php?supplier=SDW">SDW</a></li>
                    <li><a href = "product_list.php?supplier=Hala">Hala</a></li>
                    <li><a href = "product_list.php?supplier=Hartadinata">Hartadinata</a></li>
                    <li><a href = "product_list.php?supplier=MT">MT</a></li>
                    <li><a href = "product_list.php?supplier=KAP">KAP</a></li>
                    <li><a href = "product_list.php?supplier=Other">Other</a></li>
                </ul>
            </li>
            <li><a href="#footer">TENTANG KAMI</a></li>
        </ul>
    </nav>


    <div class="icons">
        <a href="product_list.php" id="shop-btn" class="fas fa-store"></a>
        <div id="search-btn" class="fas fa-search"></div>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
        <span class="badge" id="notif"><?=$badge?></span>
        <a href="
                <?php
                if (isset($_SESSION['userid'])){
                  echo 'profile.php';
                }
                else {
                  echo 'login.php';
                }
                ?>" class="fas fa-user"></a>
    </div>

    <?php
    if (isset($_POST['search'])){
        header("Location: product_list.php?search=".$_POST['search']);
        return;
    }
    ?>

    <form method = "post" class="search-form">
        <input type="search" name="search" placeholder="search here..." id="search-box">
        <label for="search-box" class="fas fa-search"></label>
    </form>

</header>
<!-- header section ends -->

<section class="roulette">

    <div class="ticket">
        <span>Jumlah Ticket</span>
        <?php echo("<input type=\"hidden\" value=\"".(string)($ticket)."\" id = \"ticket\">"); ?>
        <h3><i class="fas fa-ticket-alt"></i> <?php echo($ticket) ;  ?>  </h3>
    </div>

		<div id="dwheel" style="display: inline-block; padding: 10px; margin: 0 auto; width: 100%; max-width: 512px; "></div>
		
		<script>
			var game = new Phaser.Game(512, 768, Phaser.AUTO, "dwheel")
			var dwheel = {}
			
			var wheelstarted = false
			
			var randomstops = [
			    { rot : 1400, value : "dodol" },
				{ rot : 500, value : "dodol" },
                { rot : 1405, value : "dodol" },
                { rot : 2300, value : "dodol" },
				{ rot : 725, value : "permen" },
			    { rot : 1700, value : "permen" },
				{ rot : 1650, value : "cokelat" },
				{ rot : 1605, value : "permen" },
				{ rot : 1655, value : "cokelat" },
				{ rot : 745, value : "cokelat" },
			]
			
			
			dwheel.Main = {
				preload : function(){		
					game.load.image("wheel", "images/Wheel.png")
					game.load.image("needle", "images/Needle.png")
					game.load.image("base", "images/Base.png")
					game.stage.backgroundColor = "#000000"
				},
				create : function(){
					
					base = game.add.sprite(game.width/2, game.height/2, "base")
					base.anchor.setTo(.5)
					base.scale.setTo(1.35)
					
					wheel = game.add.sprite(game.width/2, game.height/2-20, "wheel")
					wheel.anchor.setTo(.5)
					wheel.scale.setTo(.5)
					wheel.inputEnabled = true;
					wheel.events.onInputDown.add(rotateTheWheel);
					
					needle = game.add.sprite(game.width/2, game.height/2-185, "needle")
					needle.anchor.setTo(.5)
					needle.scale.setTo(.5)
					
					function rotateTheWheel(){
                        var ticket = document.getElementById('ticket').value;
                        if(ticket>0){
                            if(!wheelstarted){
                                wheelstarted = true
                                var randomnumber = game.rnd.integerInRange(0, randomstops.length-1)
                                game.add.tween(wheel).to( {angle : randomstops[randomnumber].rot }, 5000, "Cubic", true ).onComplete.add(function(){
                                    alert("Selamat Anda mendapatkan "+ randomstops[randomnumber].value +"!")
                                    console.log("Selamat Anda mendapatkan "+ randomstops[randomnumber].value +"!")
                                    document.cookie = "prize=" + randomstops[randomnumber].value + "; SameSite=None; Secure";
                                    wheelstarted = false;
                                    location.reload();
                                })
                            }
                        }
                        else{
                            alert("Tiket tidak cukup!");
                            location.reload();
                        }
					}
					
				},
			}
			
			game.state.add("Main", dwheel.Main)
			game.state.start("Main")

		</script>
		
</section>

<section class="past-redemption" style ="padding-top: 0px; margin-left: auto; margin-right:auto; display: flex; flex-direction:column; align-items: center;">
    
    
    <h1 class="heading">Lucky<span> Draw</span> </h1>

    <?php
    if (!empty($redeems)){
    echo '<div class="box">
        <div class="table">
        <table>
            <tr>
              <th>Tanggal</th>
              <th>Hadiah</th>
              <th>Status</th>
            </tr>';
            
            
            foreach ($redeems as $row) {
                echo ("<tr>");
                echo ("<td>".$row['date']."</td>");
                echo ("<td>".$row['prize']."</td>");
                echo ("<td>".$row['status']."</td>");
                echo ("</tr>");
            }
    echo '        
    </table>
    </div>
    
    </div>';
    }
    else{
        echo ('<div class="cart-empty">') ;
        echo ("<h1 style=\"color: white\"> Belum ada penukaran hadiah </h1>");
        echo ("<a href=\"index.php\" class=\"btn\" style:\"width: auto\">Lanjutkan Belanja</a>");
        echo ('</div>');
    }
    
    
    ?>
    

</section>


</body>
</html>