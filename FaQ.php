<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}


$badge = count($_SESSION['cart']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FaQ</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">


    <style>
        .box{
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .box p {
            color: white;
            font-size: 15px;
            text-transform: none;
        }
        .box h2 {
            color: #D8D5CA; 
            font-size: 22px;
        }
        span {
            color:#AE9238;
        }
      

        .list{
            color: white;
            font-size: 14px;
            padding: 5px;
            list-style: none;
        }
        .list li{
            padding-left: 5px;
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

<!-- home section starts  -->

<section class="home">

    <div class="slide active" style="background: url(images/home-wallpaper.png) no-repeat;">
    </div>

    <div class="slide" style="background: url(images/bst.jpeg) no-repeat;">
        <div class="content">
            <span style="color: #000">Celebrate Your Birth Month</span>
            <h3 style="color: #000">Birth Stone</h3>
            <a href="product_list.php?event=1" class="btn">Lihat</a>
        </div>
    </div>

    <div class="slide" style="background: url(images/nm.jpeg) no-repeat;">
        <div class="content">
            <span>Let Your Initial Defines You</span>
            <h3>Personal</h3>
            <a href="product_list.php?event=1" class="btn">Lihat</a>
        </div>
    </div>

    <div id="next-slide" onclick="next()" class="fas fa-angle-right"></div>
    <div id="prev-slide" onclick="prev()" class="fas fa-angle-left"></div>

</section>

<!-- home section ends     -->

<!-- FaQ section starts  -->

<section class="FaQ">

    <h1 class="heading"> List <span>Pertanyaan</span> </h1>

        <div class="box">
            <ul class="list">
                <li><h2>Cara Membuat Akun Member :</h2></li>
                <ol>
                    <li><p>Tekan tombol <i class="fas fa-user" style="color:#AE9238"></i> pada bagian kanan atas layar</p></li>
                    <li><p>Tekan tombol <span>Buat Akun</span></p></li>
                    <li><p>Masukkan nama, email, password, konfirmasi password*, alamat, no WA, dan tanggal lahir pada kolom yang tersedia <br>*(Konfirmasi password harus sama dengan password) </p></li>
                    <li><p>Tekan tombol  <span>Sign Up</span></p></li>
                    <li><p>Apabila ada kesalahan, akan ditampilkan pesan di bawah tombol “Sign Up”</p></li>
                    <li><p>Pendaftaran berhasil ketika halaman “Profil Saya” ditampilkan</p></li>
                </ol>
                <br>
                <li><h2>Login :</h2></li>
                <ol>
                    <li><p>Tekan tombol <i class="fas fa-user" style="color:#AE9238"></i> pada bagian kanan atas layar</p></li>
                    <li><p>Masukkan no wa / no id </p></li>
                    <li><p>Masukkan password</p></li>
                    <li><p>Tekan tombol <span>Login</span></p></li>
                </ol>
                <br>
                <li><h2>Logout :</h2></li>
                <ol>
                    <li><p>Setelah Login, tekan tombol <i class="fas fa-user" style="color:#AE9238"></i> pada bagian kanan atas layar</p></li>
                    <li><p>Tekan tombol <span>Logout</span></p></li>
                </ol>
                <br>
                <li><h2>Konversi gram ke GEMS <span><i class="fas fa-gem"></i></span>:</h2></li>
                <ol>
                    <li><p> <span>1 gram → 1 <i class="fas fa-gem"></i> </span> (pembulatan ke bawah dan akumulatif)</p></li>
                    <li><p> Contoh : Anda membeli barang 2.6 gram akan dapat 2 GEMS, bila Anda membeli 1.7 gram lagi pada transaksi berikutnya, total gems yang Anda miliki akan menjadi 4 (sesuai total akumulatif 4.3 gram)</p></li>
                </ol>
                <br>
                <li><h2>Kumpulkan <span><i class="fas fa-gem"></i></span> melalui pembelian di toko/shopee/tokped/wa : </h2></li>
                <ol>
                    <li><p><span>Daftar menjadi member</span> (buat akun) terlebih dahulu</p></li>
                    <li><p>Saat melakukan transaksi (toko/shopee/tokped/wa) beritahu admin <span>no.Id/no.WA</span> Anda</p></li>
                    <li><p>Admin akan menambahkan transaksi ke dalam web dan Gems akan bertambah sesuai gram beli</p></li>
                    <li><p>Anda dapat lihat <span>riwayat order</span> untuk rekap pembelian melalui channel lain selain web </p></li>
                </ol>
                <br>
                <li><h2>Pembelian lewat WEB :</h2></li>
                <ol>
                    <li><p>Anda dapat memilih2 barang dan <span>lihat barang</span> berdasarkan:</p></li>
                        <ol type="A" style="padding-left: 30px;">
                            <li>Kategori (kalung, cincin, dst)</li>
                            <li>Supplier (Degold, Ayu, dst.)</li>
                            <li>Barang terbaru</li>
                            <li>Hot deal</li>
                            <li>Barang promosi</li>
                            <li>Cari nama barang</li>
                        </ol>
                    <li><p>Tambah barang ke keranjang dengan menekan tombol <span>Beli</span></p></li>
                    <li><p>Lihat keranjang dengan tombol  <span><i class="fas fa-shopping-cart"></i></span></p></li>               
                    <li><p>Di halaman My Cart, <span>pilih size</span> dan buang barang yg tidak ingin di checkout</p></li>
                    <li><p>Setelah final, tekan tombol <span>checkout</span> </p></li>
                    <li><p>Anda akan dialihkan ke halaman “riwayat order” </p></li>
                    <li><p>Anda dapat cancel orderan apabila berubah pikiran </p></li>
                    <li><p><span>Tunggu sampai admin mengontak</span> mengenai order dengan status pending </p></li>
                    <li><p>Lakukan <span>pembayaran</span> (bisa dalam bentuk shopee, tokped, bayar di toko, atau transfer)</p></li>
                    <li><p>Status order akan berubah menjadi <span>Approved</span> dan Gems akan bertambah sesuai jumlah gram beli (akumulatif) *barang harus terlebih dahulu lunas dibayar</p></li>
                </ol>
                <br>
                <li><h2>Penukaran Hadiah :</h2></li>
                <ol>
                    <li><p>Setelah Login, tekan tombol <i class="fas fa-user" style="color:#AE9238"></i> pada bagian kanan atas layar</p></li>
                    <li><p>Scroll ke bawah sampai bagian list hadiah</p></li>
                    <li><p><span>Pilih hadiah</span> yg ingin ditukar dengan gems</p></li>
                    <li><p>Tekan tombol <span>redeem</span> untuk menukar hadiah</p></li>
                    <li><p>Anda akan dialihkan ke halaman “riwayat penukaran hadiah”</p></li>
                    <li><p>Anda dapat cancel penukaran hadiah apabila berubah pikiran</p></li>
                    <li><p>Hadiah akan dikirimkan pada pembelian berikutnya/ ambil di toko</p></li>
                    <li><p>Status hadiah akan berubah menjadi <span>Approved</span> dan Gems akan berkurang sesuai harga hadiah</p></li>

                </ol>
                <br>
                <li><h2>Lupa Password :</h2></li>
                <ol>
                    <li><p><span>Kontak Admin</span></p></li>
                    <li><p>Admin akan reset password dengan tanggal lahir Anda <span>(YYYY-MM-DD)</span> contoh: 1968-05-28</p></li>
                    <li><p>Masukkan No Id/ No Wa dan <span>Login</span> menggunakan password sementara contoh: 1968-05-28</p></li>
                    <li><p>Setelah masuk ke halaman "profil saya" ubah password dengan menekan tombol <span>Edit</span></p></li>
                    <li><p>Password berhasil diganti ketika ada pesan <span>Akun & Password Berhasil Diupdate</span></p></li>
                    <li><p>Coba Logout dan Login lagi untuk memastikan update password berhasil</p></li>
                </ol>
                <br>

            </ul>
            

        </div>



</section>

<!-- FaQ section ends -->



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
                </div>
        </div>

        <div class="box">
            <h3>follow us</h3>
            <a href="https://shopee.co.id/tokomasenamitc2"> <i class="fab fa-shopify"></i> Shopee </a>
            <a href="https://tokopedia.link/ZPcW84MOcib"> <i class="fas fa-shopping-bag"></i> Tokopedia </a>
            <a href="https://www.instagram.com/tokomas_enamitc2/"> <i class="fab fa-instagram"></i> Instagram </a>
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



<!-- custom js file link -->
<script src="script.js"></script>

</body>
</html>