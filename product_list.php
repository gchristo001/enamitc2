<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}

if (isset($_POST['search'])){
    header("Location: product_list.php?search=".$_POST['search']);
    return;
}

if(!empty($_POST)){
    $url = http_build_query($_POST);
    header("Location: product_list.php?".$url);
    return;
}
$limit = 20; 
if (isset($_GET["page"])) { $page_number  = $_GET["page"]; } else { $page_number=1; };  
$initial_page = ($page_number-1) * $limit; 


$page_sql = "   SELECT 
                COUNT(*)
                FROM items
                LEFT JOIN item_attributes
                ON items.itemid = item_attributes.itemid
                WHERE items.view = 1";

$product_sql = "SELECT 
                items.itemid, 
                items.name,
                items.image,
                GROUP_CONCAT(item_attributes.size) as size, 
                max(item_attributes.weight) as weight, 
                max(item_attributes.price) as price,
                sum(item_attributes.quantity) as quantity
                FROM items
                LEFT JOIN item_attributes
                ON items.itemid = item_attributes.itemid
                WHERE items.view = 1";

$filter_info = "";


if (isset($_GET['Sold Out'])){
    $page_sql = $page_sql." AND item_attributes.quantity = 0";
    $product_sql = $product_sql." AND item_attributes.quantity = 0";
}
else{
    $page_sql = $page_sql." AND item_attributes.quantity != 0";
    $product_sql = $product_sql." AND item_attributes.quantity != 0";
}

if (isset($_GET['category'])){
    if (is_array($_GET['category'])){
        $product_sql = $product_sql." AND items.category in (\"". implode("\",\"", $_GET['category'])."\")";
        $page_sql = $page_sql." AND items.category in (\"". implode("\",\"", $_GET['category'])."\")";
        $filter_info = $filter_info . "<span>Kategori</span> : ". implode(",", $_GET['category']);
    }
    else{
        $product_sql = $product_sql." AND items.category = \"".$_GET['category']."\"";
        $page_sql = $page_sql." AND items.category = \"".$_GET['category']."\"";
    }
}

if (isset($_GET['supplier'])){
    if (is_array($_GET['supplier'])){
        $product_sql = $product_sql." AND items.supplier in (\"". implode("\",\"", $_GET['supplier'])."\")";
        $page_sql = $page_sql." AND items.supplier in (\"". implode("\",\"", $_GET['supplier'])."\")";
        if($filter_info != "")$filter_info = $filter_info . "<br>";
        $filter_info = $filter_info . "<span>Supplier</span> : ". implode(",", $_GET['supplier']);
    }
    else{
        $product_sql = $product_sql." AND items.supplier = \"".$_GET['supplier']."\"";
        $page_sql = $page_sql." AND items.supplier = \"".$_GET['supplier']."\"";
    }
}

if (isset($_GET['color'])){
    $product_sql = $product_sql." AND items.color in (\"". implode("\",\"", $_GET['color'])."\")";
    $page_sql = $page_sql." AND items.color in (\"". implode("\",\"", $_GET['color'])."\")";
    if($filter_info != "")$filter_info = $filter_info . "<br>";
    $filter_info = $filter_info . " <span>Warna</span> : ". implode(",", $_GET['color']);
}

if (isset($_GET['hot'])){
    $page_sql = $page_sql." AND items.hot = 1";
    $product_sql = $product_sql." AND items.hot = 1";
    if($filter_info != "")$filter_info = $filter_info . "<br>";
    $filter_info = $filter_info . " <span>Hot Deal</span> ";

}

if (isset($_GET['event'])){
    $page_sql = $page_sql." AND items.hot = ".$_GET['event'];
    $product_sql = $product_sql." AND items.hot = ".$_GET['event'];
    if($filter_info != "")$filter_info = $filter_info . "<br>";
    $filter_info = $filter_info . " <span>Best Seller</span> ";
}

if (isset($_GET['berat-min']) && isset($_GET['berat-max']) && isset($_GET['harga-min'])&& isset($_GET['harga-max'])){
    if (is_numeric($_GET['berat-min']) && is_numeric($_GET['berat-max'])){
        $page_sql = $page_sql." AND item_attributes.weight BETWEEN ".$_GET['berat-min']." AND ".$_GET['berat-max'] ;
        $product_sql = $product_sql." AND item_attributes.weight BETWEEN ".$_GET['berat-min']." AND ".$_GET['berat-max'] ;
        if($filter_info != "")$filter_info = $filter_info . "<br>";
        $filter_info = $filter_info . " <span>Berat</span> : ".$_GET['berat-min']."-". $_GET['berat-max'] ;
    }
    
    if (is_numeric($_GET['harga-min']) && is_numeric($_GET['harga-max'])){
        $page_sql = $page_sql." AND item_attributes.price BETWEEN ".$_GET['harga-min']." AND ".$_GET['harga-max'] ;
        $product_sql = $product_sql." AND item_attributes.price BETWEEN ".$_GET['harga-min']." AND ".$_GET['harga-max'] ;
        if($filter_info != "")$filter_info = $filter_info . "<br>";
        $filter_info = $filter_info . " <span>Harga</span> : ".$_GET['harga-min']."-". $_GET['harga-max'] ;
    }
}


if (isset($_GET['size'])){
    $page_sql = $page_sql." AND item_attributes.size LIKE \"%".$_GET['size']."%\"";
    $product_sql = $product_sql." AND item_attributes.size LIKE \"%".$_GET['size']."%\"";
    if(!empty($_GET['size'])){
        if($filter_info != "")$filter_info = $filter_info . "<br>";
        $filter_info = $filter_info . " <span>Size</span> : ".$_GET['size'];
    }
}

if (isset($_GET['search'])){
    $page_sql = $page_sql." AND lower(items.name) LIKE \"%".$_GET['search']."%\"";
    $product_sql = $product_sql." AND lower(items.name) LIKE \"%".$_GET['search']."%\"";
}

if (isset($_GET['id'])){
    $page_sql = $page_sql." AND items.itemid = ".$_GET['id'];
    $product_sql = $product_sql." AND items.itemid = ".$_GET['id'];
}

if (!empty($_GET)){
    $product_sql = $product_sql." GROUP BY 1,2,3";
}

if (isset($_GET['sort'])){
    switch ($_GET['sort']) {
        case "price_up":
            $page_sql = $page_sql." ORDER BY price";
            $product_sql = $product_sql." ORDER BY price";
            $product_sql = $product_sql." LIMIT ".(int)$limit." OFFSET ".(int)$initial_page;
            break;
        case "price_down":
            $page_sql = $page_sql." ORDER BY price DESC";
            $product_sql = $product_sql." ORDER BY price DESC";
            $product_sql = $product_sql." LIMIT ".(int)$limit." OFFSET ".(int)$initial_page;
            break;
        case "weight_up":
            $page_sql = $page_sql." ORDER BY weight";
            $product_sql = $product_sql." ORDER BY weight";
            $product_sql = $product_sql." LIMIT ".(int)$limit." OFFSET ".(int)$initial_page;
            break;
        case "weight_down":
            $page_sql = $page_sql." ORDER BY weight DESC";
            $product_sql = $product_sql." ORDER BY weight DESC";
            $product_sql = $product_sql." LIMIT ".(int)$limit." OFFSET ".(int)$initial_page;
            break;
        default:
            $page_sql = $page_sql." ORDER BY items.itemid DESC";
            $product_sql = $product_sql." ORDER BY items.itemid DESC";
            $product_sql = $product_sql." LIMIT ".(int)$limit." OFFSET ".(int)$initial_page;
      }
}
else{
    $page_sql = $page_sql." ORDER BY items.itemid DESC";
    $product_sql = $product_sql." ORDER BY items.itemid DESC";
    $product_sql = $product_sql." LIMIT ".(int)$limit." OFFSET ".(int)$initial_page;
}

if (!empty($_GET)){
    $stmt = $pdo->query($page_sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_rows = $row["COUNT(*)"];
    $total_pages = ceil($total_rows / $limit)-1; 

    $stmt = $pdo->query($product_sql);
    $displayitems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['add'])){
    if ( !in_array ($_POST['itemid'],$_SESSION['cart'])){
        $_SESSION['cart'][] = $_POST['itemid'];
    }  
}

$badge = count($_SESSION['cart']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <script src="https://kit.fontawesome.com/67318e12e0.js" crossorigin="anonymous"></script>
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css?<?=filemtime('style.css');?>">


    <style>

        ul.pagination{
            display: flex;
            justify-content: center;
            margin-top: 22px;
            margin-bottom: 22px;
        }

        .pagination li{
        display: inline-block;
        list-style-type: none;
        margin: 3px;
        }

        .pagination a {
        color: #AE9238;
        float: left;
        font-size: 1.5rem;
        padding: 2px 6px;
        text-decoration: none;
        border-style: solid;
        border-width: 1px;'
        border-color: #AE9238;
        border-radius: 8px;
        }

        .titik{
        color: #AE9238;
        font-size: 2rem;
        }

        a.active {   
        color: #000000;
        background-color: #AE9238;   
        }   

        .pagination a:hover:not(.active) {   
        background-color: #87ceeb;   
        }
        

        h2 span{
            color: #AE9238;
        }


        @media (max-width: 450px) {
            .header .navbar {
                display: none!important;
                visibility: none;
            }
            html {
                font-size: 50%;
            }
            .home .slide .content h3 {
                font-size: 4rem;
            }
            .shopping-cart .box-container .box {
                -webkit-box-orient: vertical;
                -webkit-box-direction: normal;
                    -ms-flex-flow: row;
                        flex-flow: row;
            }
            .menu .box-container{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr));
            gap:1.5rem;
            }
            .menu .box-container .box img{
            height: 15rem;
            width: 15rem;
            }
        }

           /* Style the Image Used to Trigger the Modal */
        #myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
        }

        #myImg:hover {opacity: 0.7;}

        /* The Modal (background) */
        .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 200px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
        }

        /* Modal Content (Image) */
        .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        }

        /* Caption of Modal Image (Image Text) - Same Width as the Image */
        #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
        }

        /* Add Animation - Zoom in the Modal */
        .modal-content, .filter-content, #caption {
        animation-name: zoom;
        animation-duration: 0.6s;
        }

        @keyframes zoom {
        from {transform:scale(0)}
        to {transform:scale(1)}
        }

        /* The Close Button */
        .close {
        position: absolute;
        top: 100px;
        right: 20px;
        color: #f1f1f1;
        font-size: 80px;
        font-weight: bold;
        transition: 0.3s;
        }

        .close:hover,
        .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
        }

        /* The MODAL for Filter */

        /* The Modal (background) */
        .FilterModal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .filter-contents {
        display: flex;
        flex-direction: column;
        background-color: black;
        color: white;
        margin: 30% auto; /* 15% from the top and centered */
        padding: 10px;
        border: 1px solid #888;
        width: 90%; /* Could be more or less, depending on screen size */
        }

        /* The Close Button */
        .close1 {
        align-self: flex-end;
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        }

        .close1:hover, .close1:focus {
        color: white;
        text-decoration: none;
        cursor: pointer;
        }
        
        .filter-sort{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        margin-top: 5%;
        position: -webkit-sticky; /* Safari */
        position: sticky;
        top:100px; 
        }

        .filterbtn, .sort{
        background-color: black;
        color: #AE9238;
        border-style: solid;
        border-width: 1px;'
        border-color: #AE9238;
        border-radius: 8px;
        padding: 5px;
        width: 150px;
        cursor: pointer;
        outline: none;
        font-size: 15px;
        }

        .formbtn{
        display: flex;
        justify-content: center;
        background-color: black;
        color: #AE9238;
        border-style: solid;
        border-width: 1px;'
        border-color: #AE9238;
        border-radius: 8px;
        padding: 5px;
        margin: 10px;
        cursor: pointer;
        text-align: center;
        outline: none;
        font-size: 15px;
        }


        .filterbtn:hover, .formbtn:hover {
        background-color: #AE9238;
        color: black;
        }

        /* Style the button that is used to open and close the collapsible content */
        .collapsible {
        background-color: black;
        color: #AE9238;
        cursor: pointer;
        padding: 10px;
        width: 100%;
        border-bottom: 1px solid #AE9238;
        text-align: left;
        outline: none;
        font-size: 15px;
        }

        /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
        .filter-active, .collapsible:hover {
        background-color: #AE9238;
        color: black;
        }

        /* Style the collapsible content. Note: hidden by default */
        .content {
        display: none;
        padding: 10px 10px;
        background-color: black;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.2s ease-out;
        }

        .input-grid{
        color: #AE9238;
        font-size: 1.6rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        text-align: left;
        }
        
        input./*checkbox class name*/ {
        width : /*desired width*/;
        height : /*desired height*/;
        }
        

        .input{
        display: flex;
        padding: 3px 5px;
        }

        label{
        padding: 3px 8px;
        }

        .collapsible:after {
        content: '+'; /* Unicode character for "plus" sign (+) */
        font-size: 13px;
        color: #AE9238;
        float: right;
        margin-left: 5px;
        font-size: 2rem;
        font-weight: bold;
        }

        .collapsible:hover:after{
        color: black;
        }

        .filter-active:after {
        content: "-";
        color: black;
        }


        /* Slider Effect */
        .slider {
        -webkit-appearance: none;
        margin: 10px 0;
        width: 100%;
        height: 7px;
        border-radius: 5px;  
        background: white;
        outline: none;
        opacity: 0.7;
        -webkit-transition: .2s;
        transition: opacity .2s;
        }

        .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 15px;
        height: 15px;
        border-radius: 50%; 
        background: #AE9238;
        cursor: pointer;
        }

        .slider::-moz-range-thumb {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #AE9238;
        cursor: pointer;
        }
        


        /*Slider text box and label*/
        .label-range{
        font-size:1.5rem;
        color: #AE9238;
        }
        
        .range-text{
        width: 6rem;
        color: white;
        background: none;
        border: 1px solid #AE9238;
        padding: 2px 5px;
        border-radius: 5px;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px){
        .modal-content {
            width: 100%;
        }
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
            <li><a>BELANJA</a>                
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

    <form method = "post" class="search-form">
        <input type="search" name="search" placeholder="search here..." id="search-box">
        <label for="search-box" class="fas fa-search"></label>
    </form>

</header>
<!-- header section ends -->


<!-- menu section starts  -->

<section class="menu" id="menu">
<form method="post"  id="filter-input" enctype="multipart/form-data">

    <!-- Trigger/Open The Modal -->
    <!-- The Modal -->
    <div id="FilterModal" class="FilterModal">
        <!-- Modal content -->
        <div class="filter-contents">
            <span class="close1">&times;</span>
            <button type="button" class="collapsible">Kategori</button>
                <div class="content">
                    <div class="input-grid">
                        <div class="input">
                            <input type="checkbox" id="Kalung" name="category[]" value="Necklace">
                            <label for="Kalung">Kalung</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Gelondong" name="category[]" value="Bangle">
                            <label for="Gelondong">Gelondong</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Gelang" name="category[]" value="Bracelet">
                            <label for="Gelang">Gelang</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Cincin" name="category[]" value="Ring">
                            <label for="Cincin">Cincin</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox" id="Anting" name="category[]" value="Earings">
                            <label for="Anting">Anting</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Liontin" name="category[]" value="Pendant">
                            <label for="Liontin">Liontin</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Anak" name="category[]" value="Kids">
                            <label for="Anak">Anak</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Dubai" name="category[]" value="Dubai gold">
                            <label for="Dubai">Dubai</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Emas Batang" name="category[]" value="Gold bar">
                            <label for="Emas Batang">Emas Batang</label><br>
                        </div>
                    </div>
                </div>
            <button type="button" class="collapsible">Supplier</button>
                <div class="content">
                    <div class="input-grid">
                        <div class="input">
                            <input type="checkbox" id="DeGold" name="supplier[]" value="DeGold">
                            <label for="DeGold">DeGold</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="UBS" name="supplier[]" value="UBS">
                            <label for="UBS">UBS</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Citra" name="supplier[]" value="Citra">
                            <label for="Citra">Citra</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Lotus" name="supplier[]" value="Lotus">
                            <label for="Lotus">Lotus</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox" id="YT" name="supplier[]" value="YT">
                            <label for="YT">YT</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Kinghalim" name="supplier[]" value="Kinghalim">
                            <label for="Kinghalim">Kinghalim</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="HWT" name="supplier[]" value="HWT">
                            <label for="HWT">HWT</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="BG" name="supplier[]" value="BG">
                            <label for="BG">BG</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Ayu" name="supplier[]" value="Ayu">
                            <label for="Ayu">Ayu</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="SDW" name="supplier[]" value="SDW">
                            <label for="SDW">SDW</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Hala" name="supplier[]" value="Hala">
                            <label for="Hala">Hala</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Hartadinata" name="supplier[]" value="Hartadinata">
                            <label for="Hartadinata">Hartadinata</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="MT" name="supplier[]" value="MT">
                            <label for="MT">MT</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="KAP" name="supplier[]" value="KAP">
                            <label for="KAP">KAP</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Other" name="supplier[]" value="Other">
                            <label for="Other">Other</label><br>
                        </div>
                    </div>
                </div>
            <button type="button" class="collapsible">Warna</button>
                <div class="content">
                    <div class="input-grid">
                        <div class="input">
                            <input type="checkbox" id="Rosegold" name="color[]" value="Rosegold">
                            <label for="Rosegold">Rosegold</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Kuning" name="color[]" value="Kuning">
                            <label for="Kuning">Kuning</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Putih" name="color[]" value="Putih">
                            <label for="Putih">Putih</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Blackgold" name="color[]" value="Blackgold">
                            <label for="Blackgold">Blackgold</label><br>
                        </div>
                    </div>
                </div>
            <button type="button" class="collapsible">Berat</button>
                <div class="content">
                    <div class="input">
                        <label for="berat-min" class="label-range">Berat Min :</label>
                        <input type="number" name="berat-min" id="berat-min" class="range-text" class= "input">	
                    </div>         
                    <input type="range" min="0" max="100" value="0" class="slider" id="berat-min-slider">
                    <div class="input">
                        <label for="berat-max" class="label-range">Berat Max :</label>
                        <input type="number" name="berat-max" id="berat-max" class="range-text" class= "input">	
                    </div>         
                    <input type="range" min="0" max="100" value="100" class="slider" id="berat-max-slider">        
                </div>
            <button type="button" class="collapsible">Harga</button>
                <div class="content">
                    <div class="input">
                        <label for="harga-min" class="label-range">Harga Min (K):</label>
                        <input type="number" name="harga-min" id="harga-min" class="range-text" class= "input"  style="width: 10rem;">	
                    </div>         
                    <input type="range" min="0" max="100000" value="0" class="slider" id="harga-min-slider">
                    <div class="input">
                        <label for="harga-max" class="label-range">Harga Max (K):</label>
                        <input type="number" name="harga-max" id="harga-max" class="range-text" class= "input" style="width: 10rem;">	
                    </div>         
                    <input type="range" min="0" max="100000" value="100000" class="slider" id="harga-max-slider">        
                </div>
            <button type="button" class="collapsible">Size</button> 
                <div class="content">
                    <div class="input">
                        <label for="size" class="label-range">Size :</label>
                        <input type="number" name="size"  id="size" class="range-text" class= "input"  >	
                    </div>  
                </div>
            <button type="button" class="collapsible">Keterangan</button> 
                <div class="content">
                    <div class="input-grid">
                        <div class="input">
                            <input type="checkbox" id="Sold Out" name="Sold Out" value="Sold Out">
                            <label for="Sold Out">Sold Out</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="hot" name="hot" value="hot">
                            <label for="hot">Hot Deal</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="new" name="new" value="new">
                            <label for="new">New Items</label><br>
                        </div>
                        <div class="input">
                            <input type="checkbox"  id="Best Seller" name="event" value="3">
                            <label for="Best Seller">Best Seller</label><br>
                        </div>
                    </div>
                </div>
            <div class="input-grid">
                <input type="submit" name="filter" value="Filter" class="formbtn" >
                <input type="reset" class="formbtn">
            </div>
        </div>
    </div>

    <div class="filter-sort">
        <div class="filter">
            <button type="button" id="filterbtn" class="filterbtn">Filter</button>        
        </div>
        <select name="sort" class="sort" onchange="to_url_sort(sort.value)">
            <option value="0">Sort :</option>
            <option value="price_up"<?php if(isset($_GET['sort']))if($_GET['sort'] === 'price_up'){echo("selected = \"selected\"");}?>>Harga &#8593;</option>
            <option value="price_down"<?php if(isset($_GET['sort']))if($_GET['sort'] === 'price_down'){echo("selected = \"selected\"");}?>>Harga &#8595;</option>
            <option value="weight_up"<?php if(isset($_GET['sort']))if($_GET['sort'] === 'weight_up'){echo("selected = \"selected\"");}?>>Gram &#8593;</option>
            <option value="weight_down"<?php if(isset($_GET['sort']))if($_GET['sort'] === 'weight_down'){echo("selected = \"selected\"");}?>>Gram &#8595;</option>     
        </select>
    </div>
    </form>


    <script>

    // Slider Function
    var berat_min_slider = document.getElementById("berat-min-slider");
    var berat_min = document.getElementById("berat-min");
    berat_min.value = berat_min_slider.value; // Display the default slider value

    // Update the current slider value (each time you drag the slider handle)
    berat_min_slider.oninput = function() {
        berat_min.value = this.value;
    }
    
    berat_min.oninput = function() {
        berat_min_slider.value = this.value;
    }

    var berat_max_slider = document.getElementById("berat-max-slider");
    var berat_max = document.getElementById("berat-max");
    berat_max.value = berat_max_slider.value; // Display the default slider value

    // Update the current slider value (each time you drag the slider handle)
    berat_max_slider.oninput = function() {
        berat_max.value = this.value;
    }
    
    berat_max.oninput = function() {
        berat_max_slider.value = this.value;
    }


    var harga_min_slider = document.getElementById("harga-min-slider");
    var harga_min = document.getElementById("harga-min");
    harga_min.value = harga_min_slider.value ; // Display the default slider value

    // Update the current slider value (each time you drag the slider handle)
    harga_min_slider.oninput = function() {
        harga_min.value = this.value ;
    }
    
    harga_min.oninput = function() {
        harga_min_slider.value = this.value ;
    }


    var harga_max_slider = document.getElementById("harga-max-slider");
    var harga_max = document.getElementById("harga-max");
    harga_max.value = harga_max_slider.value ; // Display the default slider value

    // Update the current slider value (each time you drag the slider handle)
    harga_max_slider.oninput = function() {
        harga_max.value = this.value ;
    }
    
    harga_max.oninput = function() {
        harga_max_slider.value = this.value ;
    }

    // End of Slide function


    // Get the modal
    var FilterModal = document.getElementById("FilterModal");

    // Get the button that opens the modal
    var filterbtn = document.getElementById("filterbtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close1")[0];

    // When the user clicks on the button, open the modal
    filterbtn.onclick = function() {
    FilterModal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
    FilterModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
    if (event.target == modal) {
        FilterModal.style.display = "none";
    }
    }
    </script>

    <script>
        var coll = document.getElementsByClassName("collapsible");
        var i;

        for (i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("filter-active");
            var content = this.nextElementSibling;
            if (content.style.display === "flex") {
            content.style.display = "none";
            content.style.flexDirection  = "column";
            content.style.maxHeight = null;
            } else {
            content.style.display = "flex";
            content.style.flexDirection  = "column";
            content.style.maxHeight = content.scrollHeight + "px";
            }
        });
        }
    </script>

    <?php
    if(isset($_GET['filter'])){
        echo("<h1 class=\"heading\"> Hasil <span> Filter </span> </h1>");
        echo("<h2 style=\"color: white;\">");
        echo($filter_info);
        echo "</h2>";
    }
    else{
        if(isset($_GET['category'])){
            echo("<h1 class=\"heading\"> Category <span>". $_GET['category']."  </span> </h1>");
        }

        if(isset($_GET['supplier'])){
            echo("<h1 class=\"heading\"> Supplier <span>". $_GET['supplier']." </span> </h1>");
        }

        if(isset($_GET['new'])){
            echo("<h1 class=\"heading\"> Barang <span> Terbaru </span> </h1>");
        }

        if(isset($_GET['hot'])){
            echo("<h1 class=\"heading\"> Hot <span> Deal </span> </h1>");
        }

        if(isset($_GET['event'])){
            echo("<h1 class=\"heading\"> Event <span> Items </span> </h1>");
        }

        if(isset($_GET['search'])){
            echo("<h1 class=\"heading\"> Hasil <span> Pencarian </span> </h1>");
        }

        if(isset($_GET['id'])){
            echo("<h1 class=\"heading\"> Cek <span> Produk </span> </h1>");
        }
        if(isset($_GET['Sold_Out'])){
            echo("<h1 class=\"heading\"> Sold <span> Out </span> </h1>");
        }
    }
    

   
    
    
    ?>

<script>
    var sort_str = "&sort=";
    var page_str = "&page=";

    function to_url(page){
        var url = String(window.location.href);
        
        if (url.includes(page_str)){
            var array = url.split(page_str);
            url = array [0] + page_str + page;
            window.location.href = url;
        }
        else{
            window.location.href = url + page_str + page;
        }

    }

    function to_url_sort(sort){
        var url = String(window.location.href);
        if (url.includes(sort_str)){
            var array = url.split(sort_str);
            url = array [0] + sort_str + sort;
            window.location.href = url;   
        }
        else if (url.includes(page_str)){
            var array = url.split(page_str);
            url = array [0] + sort_str + sort;
            window.location.href = url;
        }
        else if (url.includes("?")){
            window.location.href = url + sort_str + sort;
        }

    }

</script>


    <div class = "page">
        <ul style="color: white;" class="pagination">
            <?php 
                if(!empty($total_pages) && $total_pages>=5){
                    if(!isset($_GET['page']) || $_GET['page'] == 1){
                        $_GET['page'] = 1;
                    }
                    else{
                        $i = $_GET['page']-1;
            ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><</a></li>
            <?php
                    }
                    if($_GET['page'] == 1 || $_GET['page'] == 2 || $_GET['page'] == 3){
                        for($i=1; $i<=5; $i++){
                            if($i == $_GET['page']){
            ?>
                            <li class="pageitem" id="<?php echo $i;?>"><a  class = "active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                            }
                            else{
            ?>
                            <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php      
                            }
                        }
                        $i = $total_pages;
            ?>
                        <li class="titik"> ... </li>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php
                    }
                    else if ($_GET['page'] == $total_pages-2 || $_GET['page'] == $total_pages-1 || $_GET['page'] == $total_pages){
                        $i = 1;
            ?>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                        <li class="titik"> ... </li>
            <?php
                        
                        for($i=$total_pages-4; $i<= $total_pages; $i++){
                            if($i == $_GET['page']){
            ?>  
                                <li class="pageitem" id="<?php echo $i;?>"><a  class = "active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php   
                            }
                            else{
            ?>  
                                <li class="pageitem" id="<?php echo $i;?>"><a onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                            }
                        }
                    }
                    else{
                        $i = 1;
            ?>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                        <li class="titik"> ... </li>
            <?php
                    for($i=$_GET['page']-2; $i<= $_GET['page']+2; $i++){
                        if($i == $_GET['page']){
            ?>  
                            <li class="pageitem" id="<?php echo $i;?>"><a  class = "active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                        }
                        else{
            ?>  
                            <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                        }
                    }
                        $i = $total_pages;
            ?>
                        <li class="titik"> ... </li>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            
            <?php
                    }
                    if($_GET['page'] != $total_pages){
                        $i = $_GET['page']+1;
            ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    >></a></li>
            <?php
                    }  
                }
                else if (!empty($total_pages)){
                    if(!isset($_GET['page']) || $_GET['page'] == 1){
                        $_GET['page'] = 1;
                    }
                    for($i=1; $i<= $total_pages; $i++){
                        if($i == $_GET['page']){
                ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a class="active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                <?php
                        }
                        else{
                ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                <?php
                        }
                    }
                }  
            ?>
        </ul>
    </div>


    <div class="box-container" id="display">

    <?php
    if (!isset($_GET['Sold_Out'])){
        foreach ( $displayitems as $item ) {
            echo("<div class=\"box\">");
            $filestr = explode("." ,$item['image']);
            $filepath = "./image-data/" . $filestr[0] . ".txt";

            if(file_exists($filepath)){
                $file = file_get_contents($filepath, true);
                echo ("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"".$file." \"loading=\"lazy\">");
            }
            else{
                echo("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"item-image/".($item['image'])." \">");
            }
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"weight-size\">".number_format((float)$item['weight'], 2, '.', '')." gr");
            if($item['size']>0){
              echo (" | size: ".$item['size']."</div>");
            }
            else{
              echo("</div>");
            }
            echo("<div class=\"weight-size\"> Stok : ".$item['quantity']);
            echo(" | <button style = \"background: transparent; color: orange;\" class = \"share\" id = \"".$item['itemid']."+".$item['name']."+".$item['weight']."+".$item['price']."+".$item['size']."\"> Share <i class=\"fa-solid fa-arrow-up-from-bracket\"></i> </button> </div>");
            echo("<div class=\"price\">".$item['price']." k </div>");
            echo("<form id=\"user-form\" onsubmit = \"return ajaxgo(".$item['itemid'].")\">");
            echo("<input type=\"hidden\" value=\"".$item['itemid']."\" id = \"itemid\">");
            echo("<input type=\"submit\" class=\"btn\" value = \"Beli\" name = \"add\">");
            echo("</form>");
            echo("</div>");
        }  
    }
    else {     
        foreach ( $displayitems as $item ) {
            echo("<div class=\"box\">");
            $filestr = explode("." ,$item['image']);
            $filepath = "./image-data/" . $filestr[0] . ".txt";
    
            if(file_exists($filepath)){
                $file = file_get_contents($filepath, true);
                echo ("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"".$file." \"style=\"opacity: 0.6;
                filter: alpha(opacity=60);\"loading=\"lazy\">");
                }
            else{
                echo("<img class=\"myImages\" id=\"".$item['itemid']."\" src=\"item-image/".($item['image'])." \"style=\"opacity: 0.6;
                filter: alpha(opacity=60);\">");
            }
                
            echo("<h3>".$item['name']."</h3>");
            echo("<div class=\"weight-size\">".number_format((float)$item['weight'], 2, '.', '')." gr");
            if($item['size']>0){
                echo (" | size: ".$item['size']."</div>");
            }
            else{
                echo("</div>");
            }
            echo("<div class=\"weight-size\"> Id: ".$item['itemid']);
            echo("</div>");
            echo("<div class=\"price\">".$item['price']." k </div>");
            echo("</div>");
        }   
    }
    ?>

    </div>

    <div class = "page">
        <ul style="color: white;" class="pagination">
            <?php 
                if(!empty($total_pages) && $total_pages>=5){
                    if(!isset($_GET['page']) || $_GET['page'] == 1){
                        $_GET['page'] = 1;
                    }
                    else{
                        $i = $_GET['page']-1;
            ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><</a></li>
            <?php
                    }
                    if($_GET['page'] == 1 || $_GET['page'] == 2 || $_GET['page'] == 3){
                        for($i=1; $i<=5; $i++){
                            if($i == $_GET['page']){
            ?>
                            <li class="pageitem" id="<?php echo $i;?>"><a  class = "active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                            }
                            else{
            ?>
                            <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php      
                            }
                        }
                        $i = $total_pages;
            ?>
                        <li class="titik"> ... </li>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php
                    }
                    else if ($_GET['page'] == $total_pages-2 || $_GET['page'] == $total_pages-1 || $_GET['page'] == $total_pages){
                        $i = 1;
            ?>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                        <li class="titik"> ... </li>
            <?php
                        
                        for($i=$total_pages-4; $i<= $total_pages; $i++){
                            if($i == $_GET['page']){
            ?>  
                                <li class="pageitem" id="<?php echo $i;?>"><a  class = "active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php   
                            }
                            else{
            ?>  
                                <li class="pageitem" id="<?php echo $i;?>"><a onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                            }
                        }
                    }
                    else{
                        $i = 1;
            ?>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                        <li class="titik"> ... </li>
            <?php
                    for($i=$_GET['page']-2; $i<= $_GET['page']+2; $i++){
                        if($i == $_GET['page']){
            ?>  
                            <li class="pageitem" id="<?php echo $i;?>"><a  class = "active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                        }
                        else{
            ?>  
                            <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            <?php 
                        }
                    }
                        $i = $total_pages;
            ?>
                        <li class="titik"> ... </li>
                        <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
            
            <?php
                    }
                    if($_GET['page'] != $total_pages){
                        $i = $_GET['page']+1;
            ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    >></a></li>
            <?php
                    }  
                }
                else if (!empty($total_pages)){
                    for($i=1; $i<= $total_pages; $i++){
                        if($i == $_GET['page']){
                ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a class="active" onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                <?php
                        }
                        else{
                ?>
                    <li class="pageitem" id="<?php echo $i;?>"><a  onclick="to_url(<?php echo $i; ?>);"    ><?php echo $i;?></a></li>
                <?php
                        }
                    }
                }  
            ?>
        </ul>
    </div>

</section>

<!-- menu section ends -->


<script>

    function getDataUrl(img) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = img.width;
    canvas.height = img.height;
    ctx.drawImage(img, 0, 0);
    return canvas.toDataURL('image/jpeg');
    }
    function dataURLtoBlob(dataurl) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
    }
    

  var share = document.getElementsByClassName('share');
  for (var i = 0; i < share.length; i++) {
  const btn = share[i];
  btn.addEventListener('click', async () => {
  var info = btn.id;
  const myArray = info.split("+");
  const id = myArray[0];
  const nama = myArray[1];
  const berat = myArray[2];
  const harga = myArray[3];
  const size = myArray[4];
  const gambar = myArray[5];
  
  const imgsrc = document.getElementById(id).src;
  var img = document.createElement("img");
  img.src = imgsrc

  const dataUrl = getDataUrl(img);
  
  var blob = dataURLtoBlob(dataUrl);
  var file = new File([blob], "picture.jpg", {type: 'image/jpeg'});
  var filesArray = [file];
  
  const shareData = {
    title: nama + " | " + berat + "gr | sz:" + size + " | "+ harga + "k" ,
    text: 'Coba cek ini, deh: ' + nama + " | " + berat + "gr | sz:" + size + " | "+ harga + "k" + ' di website toko mas enam itc 2',
    url: 'https://www.enamitc2.com/product_list.php?id=' + id,
    files: filesArray
  }
   navigator.share(shareData) });
  }
</script>


<!-- The Modal -->
<div id="myModal" class="modal">

<!-- The Close Button -->
<span class="close">&times;</span>

<!-- Modal Content (The Image) -->
<img class="modal-content" id="img01">

<!-- Modal Caption (Image Text) -->
<div id="caption"></div>

</div>

<script>
   // create references to the modal...
    var modal = document.getElementById('myModal');
    // to all images -- note I'm using a class!
    var images = document.getElementsByClassName('myImages');
    // the image in the modal
    var modalImg = document.getElementById("img01");
    // and the caption in the modal
    var captionText = document.getElementById("caption");

// Go through all of the images with our custom class
for (var i = 0; i < images.length; i++) {
  var img = images[i];
  // and attach our click listener for this image.
  img.onclick = function(evt) {
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
  }
}

var span = document.getElementsByClassName("close")[0];

span.onclick = function() {
  modal.style.display = "none";
}
</script>



<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">

        <div class="box">
            <h3>Kategori</h3>
            <a href = "product_list.php?category=Necklace"><i class="fas fa-angle-right"></i>Kalung</a>
            <a href = "product_list.php?category=Bangle"><i class="fas fa-angle-right"></i>Gelondong</a>
            <a href = "product_list.php?category=Bracelet"><i class="fas fa-angle-right"></i>Gelang</a>
            <a href = "product_list.php?category=Ring"><i class="fas fa-angle-right"></i>Cincin</a>
            <a href = "product_list.php?category=Earings"><i class="fas fa-angle-right"></i>Anting</a>
            <a href = "product_list.php?category=Pendant"><i class="fas fa-angle-right"></i>Liontin</a>
            <a href = "product_list.php?category=Kids"><i class="fas fa-angle-right"></i>Anak</a>
            <a href = "product_list.php?category=Dubai gold"><i class="fas fa-angle-right"></i>Dubai</a>
            <a href = "product_list.php?category=Gold bar"><i class="fas fa-angle-right"></i>Emas Batang</a>
            <a href = "product_list.php?Sold_Out=Sold out"><i class="fas fa-angle-right"></i>Sold Out</a>
        </div>

        <div class="box">
            <h3>Koleksi</h3>
                <div class="footer-link">
                <a href = "product_list.php?supplier=DeGold"><i class="fas fa-angle-right"></i>DeGold</a>
                <a href = "product_list.php?supplier=UBS"><i class="fas fa-angle-right"></i>UBS</a>
                <a href = "product_list.php?supplier=Citra"><i class="fas fa-angle-right"></i>Citra</a>
                <a href = "product_list.php?supplier=Lotus"><i class="fas fa-angle-right"></i>Lotus</a>
                <a href = "product_list.php?supplier=YT"><i class="fas fa-angle-right"></i>YT</a>
                <a href = "product_list.php?supplier=Kinghalim"><i class="fas fa-angle-right"></i>Kinghalim</a>
                <a href = "product_list.php?supplier=HWT"><i class="fas fa-angle-right"></i>HWT</a>
                <a href = "product_list.php?supplier=BG"><i class="fas fa-angle-right"></i>BG</a>
                <a href = "product_list.php?supplier=Ayu"><i class="fas fa-angle-right"></i>Ayu</a>
                <a href = "product_list.php?supplier=SDW"><i class="fas fa-angle-right"></i>SDW</a>
                <a href = "product_list.php?supplier=Hala"><i class="fas fa-angle-right"></i>Hala</a>
                <a href = "product_list.php?supplier=Hartadinata"><i class="fas fa-angle-right"></i>Hartadinata</a>
                <a href = "product_list.php?supplier=MT"><i class="fas fa-angle-right"></i>MT</a>
                <a href = "product_list.php?supplier=KAP"><i class="fas fa-angle-right"></i>KAP</a>
                <a href = "product_list.php?supplier=Other"><i class="fas fa-angle-right"></i>Other</a>
                </div>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="https://shopee.co.id/tokomasenamitc2"> <i class="fab fa-shopify"></i> Shopee </a>
            <a href="https://tokopedia.link/ZPcW84MOcib"> <i class="fas fa-shopping-bag"></i> Tokopedia </a>
            <a href="https://www.instagram.com/tokomas_6itc2/"> <i class="fab fa-instagram"></i>Main Instagram </a>
            <a href="https://www.instagram.com/tokomas_enamitc2.new/"> <i class="fab fa-instagram"></i>Second Instagram </a>
            <a href="https://wa.me/62818188266"> <i class="fab fa-whatsapp"></i> Whatsapp</a>
        </div>

        <div class="box" id="footer">
            <h3>Tentang Kami</h3>
            <p>Berdiri sejak 2004,
            Toko Mas 6 ITC 2 bagian dari toko mas 6 group.
            Menyediakan perhiasan model terbaru dengan kadar 70 - 100 % (mas tua kadar internasional)
            Kami terus menyediakan layanan terbaik bagi pelanggan kami dengan harga yang bersaing, tanpa ongkos.
            Perhiasan dapat dijual kembali dengan potongan super ekonomis.
            Kami juga menerima layanan servis perhiasan seperti cuci, patri dan pesanan perhiasan dengan kustomisasi khusus.
            Kami percaya anda dapat tampil modis selagi berinvestasi<br><br></p>
           <p><i class="fas fa-map-marker-alt"></i>  ITC Kebun Kelapa Lantai SB 01-03, Jl. Moh. Toha, Pungkur, Regol, Bandung City, West Java 40252, Indonesia </p>
           <p><i class="far fa-clock"></i>  Senin - Sabtu 09:00 - 16:00 </p>
        </div>

    </div>

    <div class="credit"> created by <span>GC</span> | all rights reserved </div>

</section>
<!-- footer section ends -->

<?php
if (empty($_GET)){
    echo '<script>
    var x = document.getElementById("display");
    x.style.display = "none";
    </script>';
}
?>

<!-- custom js file link -->
<script src="script.js"></script>

</body>
</html>