<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Mas Enam ITC 2</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">


</head>
<body>

<!-- header section starts  -->

<header class="header">

    <a href="index.php" class="logo"> <img src="images/Logo.png"> </a>

    <nav class="navbar">
        <ul>
            <li><a href="index.php">HOME</a></li>
            <li><a>SHOP CATEGORIES</a>                
                <ul>
                    <li><a href = "product_list.php?category=Necklace">Necklace</a></li>
                    <li><a href = "product_list.php?category=Bangle">Bangle</a></li>
                    <li><a href = "product_list.php?category=Bracelet">Bracelet</a></li>
                    <li><a href = "product_list.php?category=Ring">Ring</a></li>
                    <li><a href = "product_list.php?category=Earings">Earings</a></li>
                    <li><a href = "product_list.php?category=Pendant">Pendant</a></li>
                    <li><a href = "product_list.php?category=Kids">Kids</a></li>
                    <li><a href = "product_list.php?category=Dubai gold">Dubai</a></li>
                    <li><a href = "product_list.php?category=Gold bar">Gold bar</a></li>
                </ul>
            </li>
            <li><a>COLLECTION</a>
                <ul>
                    <li><a href = "product_list.php?supplier=DeGold">DeGold</a></li>
                    <li><a href = "product_list.php?supplier=UBS">UBS</a></li>
                    <li><a href = "product_list.php?supplier=Citra">Citra</a></li>
                    <li><a href = "product_list.php?supplier=Lotus">Lotus</a></li>
                    <li><a href = "product_list.php?supplier=YT">YT</a></li>
                    <li><a href = "product_list.php?supplier=Kinghalim">Kinghalim</a></li>
                    <li><a href = "product_list.php?supplier=HWT">HWT</a></li>
                    <li><a href = "product_list.php?supplier=Bulgari">Bulgari</a></li>
                    <li><a href = "product_list.php?supplier=Ayu">Ayu</a></li>
                    <li><a href = "product_list.php?supplier=SJW">SJW</a></li>
                    <li><a href = "product_list.php?supplier=Hala">Hala</a></li>
                    <li><a href = "product_list.php?supplier=Amero">Amero</a></li>
                    <li><a href = "product_list.php?supplier=MT">MT</a></li>
                </ul>
            </li>
            <li><a href="index.php">ABOUT US</a></li>
        </ul>
    </nav>

    <div class="icons">
        <div id="menu-btn" class="fas fa-bars"></div>
        <div id="search-btn" class="fas fa-search"></div>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
        <a href="profile.php" class="fas fa-user"></a>
    </div>

    <form action="" class="search-form">
        <input type="search" name="" placeholder="search here..." id="search-box">
        <label for="search-box" class="fas fa-search"></label>
    </form>

</header>
<!-- header section ends -->

<!-- home section starts      -->

<section class="home">

    <div class="slide active" style="background: url(images/banner1.jpg) no-repeat;">
        <div class="content">
            <span>Celebrate Your Birth Month</span>
            <h3>Birth Stone</h3>
            <a href="#" class="btn">shop now</a>
        </div>
    </div>

    <div class="slide" style="background: url(images/banner2.jpg) no-repeat;">
        <div class="content">
            <span>Beutify Yourself</span>
            <h3>DeGold Collection</h3>
            <a href="#" class="btn">shop now</a>
        </div>
    </div>

    <div class="slide" style="background: url(images/banner66.jpg) no-repeat;">
        <div class="content">
            <span>Promotion</span>
            <h3>upto 50% off</h3>
            <a href="#" class="btn">shop now</a>
        </div>
    </div>

    <div id="next-slide" onclick="next()" class="fas fa-angle-right"></div>
    <div id="prev-slide" onclick="prev()" class="fas fa-angle-left"></div>

</section>

<!-- home section ends     -->

<!-- category section starts  -->

<section class="category">

    <h1 class="heading"> shop by <span>category</span> </h1>

    <div class="box-container">

        <div class="box">
            <img src="images/collection1.PNG" alt="">
            <div class="content">
                <span>Fancy</span>
                <h3>Rings</h3>
                <a href="#" class="btn">shop now</a>
            </div>
        </div>

        <div class="box">
            <img src="images/collection2.PNG" alt="">
            <div class="content">
                <span>Hottest</span>
                <h3>Bracelet</h3>
                <a href="#" class="btn">shop now</a>
            </div>
        </div>
        
    </div>

</section>

<!-- category section ends -->

<!-- deal section starts  -->

<section class="deal" id="deal">

    <h1 class="heading"> special <span>deal</span> </h1>

    <div class="row">

        <div class="content">
            <span class="discount">upto 50% off</span>
            <h3 class="text">deal of the day</h3>
            <div class="count-down">
                <div class="box">
                    <h3 id="days">00</h3>
                    <span>days</span>
                </div>
                <div class="box">
                    <h3 id="hours">00</h3>
                    <span>hours</span>
                </div>
                <div class="box">
                    <h3 id="minutes">00</h3>
                    <span>minutes</span>
                </div>
                <div class="box">
                    <h3 id="seconds">00</h3>
                    <span>seconds</span>
                </div>
            </div>
            <a href="#" class="btn">shop now</a>
        </div>

        <div class="image">
            <img src="images/banner1.jpg" alt="">
        </div>

    </div>

</section>

<!-- deal section ends -->


<!-- custom js file link -->
<script src="script.js"></script>


<!-- footer section starts  -->

<section class="footer">

    <div class="box-container">

        <div class="box">
            <h3>Shop Categories</h3>
            <a href = "product_list.php?category=Necklace"><i class="fas fa-angle-right"></i>Necklace</a>
            <a href = "product_list.php?category=Bangle"><i class="fas fa-angle-right"></i>Bangle</a>
            <a href = "product_list.php?category=Bracelet"><i class="fas fa-angle-right"></i>Bracelet</a>
            <a href = "product_list.php?category=Ring"><i class="fas fa-angle-right"></i>Ring</a>
            <a href = "product_list.php?category=Earings"><i class="fas fa-angle-right"></i>Earings</a>
            <a href = "product_list.php?category=Pendant"><i class="fas fa-angle-right"></i>Pendant</a>
            <a href = "product_list.php?category=Kids"><i class="fas fa-angle-right"></i>Kids</a>
            <a href = "product_list.php?category=Dubai gold"><i class="fas fa-angle-right"></i>Dubai</a>
            <a href = "product_list.php?category=Gold bar"><i class="fas fa-angle-right"></i>Gold bar</a>
        </div>

        <div class="box">
            <h3>Collection</h3>
                <div class = "cat-links">
                <a href = "product_list.php?supplier=DeGold"><i class="fas fa-angle-right">DeGold</a>
                <a href = "product_list.php?supplier=UBS"><i class="fas fa-angle-right">UBS</a>
                <a href = "product_list.php?supplier=Citra"><i class="fas fa-angle-right">Citra</a>
                <a href = "product_list.php?supplier=Lotus"><i class="fas fa-angle-right">Lotus</a>
                <a href = "product_list.php?supplier=YT"><i class="fas fa-angle-right">YT</a>
                <a href = "product_list.php?supplier=Kinghalim"><i class="fas fa-angle-right">Kinghalim</a>
                <a href = "product_list.php?supplier=HWT"><i class="fas fa-angle-right">HWT</a>
                <a href = "product_list.php?supplier=Bulgari"><i class="fas fa-angle-right">Bulgari</a>
                <a href = "product_list.php?supplier=Ayu"><i class="fas fa-angle-right">Ayu</a>
                <a href = "product_list.php?supplier=SJW"><i class="fas fa-angle-right">SJW</a>
                <a href = "product_list.php?supplier=Hala"><i class="fas fa-angle-right">Hala</a>
                <a href = "product_list.php?supplier=Amero"><i class="fas fa-angle-right">Amero</a>
                <a href = "product_list.php?supplier=MT"><i class="fas fa-angle-right">MT</a>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="#"> <i class="fab fa-facebook-f"></i> facebook </a>
            <a href="#"> <i class="fab fa-twitter"></i> twitter </a>
            <a href="#"> <i class="fab fa-instagram"></i> instagram </a>
            <a href="#"> <i class="fab fa-pinterest"></i> pinterest </a>
            <a href="#"> <i class="fab fa-linkedin"></i> linkedin </a>
            <a href="#"> <i class="fab fa-github"></i> github </a>
        </div>

        <div class="box">
            <h3>newsletter</h3>
            <p>subscribe for latest updates</p>
            <form action="">
                <input type="email" placeholder="enter your emal">
                <input type="submit" value="subscribe" class="btn">
            </form>
        </div>

    </div>

    <div class="credit"> created by <span>GC</span> | all rights reserved </div>

</section>
<div class="div">ini bobi bola</div>
<!-- footer section ends -->

</body>
</html>