
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>News</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Alex+Brush" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    
  <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="oi oi-menu"></span> Menu
      </button>

      <div class="collapse navbar-collapse" id="ftco-nav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item "><a href="index.php" class="nav-link">Beranda</a></li>
          <li class="nav-item"><a href="news.php" class="nav-link active">News</a></li>
          <li class="nav-item"><a href="event.php" class="nav-link">Event</a></li>
          <li class="nav-item cta"><a href="user-ticketing/login-user-ticketing.php" class="nav-link"><span>Login</span></a></li>
        </ul>
      </div>
    </div>
  </nav>
    <!-- END nav -->
    
    <div class="hero-wrap js-fullheight" style="background-image: url('images/bg_1.jpg');">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center" data-scrollax-parent="true">
          <div class="col-md-9 ftco-animate text-center" data-scrollax=" properties: { translateY: '70%' }">
            <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><span class="mr-2"><a href="index.php">Home</a></span> <span class="mr-2"><a href="nuws.php">News</a></span></p>
            <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">News</h1>
          </div>
        </div>
      </div>
    </div>


    <section class="ftco-section ftco-degree-bg">
      <div class="container">
        <div class="row">
          <div class="col-md-8 ftco-animate">
            <h2 class="mb-3">Penampilan Spesial Reog Sambut Long Weekend di THP Kenjeran, Merapat Yuk!</h2>
            <p>Taman Hiburan Pantai (THP) Kenjeran menyiapkan pertunjukan spesial Reog menyambut libur panjang Nyepi dan awal Ramadan 2024. Ini diharapkan bisa mendongkrak peningkatan wisatawan.</p>

            <p>
              <img src="images/event-3.jpg" alt="" class="img-fluid">
            </p>
            <p>Pertunjukan Reog Singo Sekar Budoyo siap menghibur para pengunjung. Ismet menyebut, penampilan reog ini merupakan bentuk kerjasama dengan Disbudporapar Surabaya spesial untuk memeriahkan perayaan Nyepi oleh Pemkot Surabaya.</p>
            <p>Tak hanya untuk memeriahkan perayaan Nyepi, pagelaran reog ini juga digelar untuk menyambut datangnya bulan Ramadan. Pagelaran ini merupakan acara puncak yang digelar di THP Kenjeran sebelum memasuki bulan Ramadan.</p>
            <h2 class="mb-3 mt-5">Akhir Pekan di THP Kenjeran Yuk, Ada Wisata Naik Perahu</h2>
            <p>Pada akhir pekan dan bahkan selama liburan sekolah, Taman Hiburan Pantai (THP) Kenjeran menjadi ramai dengan pengunjung sejak pagi hari. Selain menjadi destinasi wisata populer di Surabaya, daya tarik utama THP Kenjeran adalah harganya yang terjangkau.</p>
            <p>
              <img src="images/news2.jpeg" alt="" class="img-fluid">
            </p>
            <p>Pengunjung rela menghabiskan waktu naik perahu untuk berfoto dengan latar belakang Jembatan Suroboyo dan pemandangan laut. Jika merasa lelah atau lapar, pengunjung dapat singgah ke berbagai UMKM yang tersedia di sekitar area tersebut. Di samping itu, tersedia juga tempat duduk dengan payung khas di tepi pantai atau area lesehan dengan tikar sebagai alas duduk.</p>
        
            <div class="tag-widget post-tag-container mb-5 mt-5">
              <div class="tagcloud">
                <a href="event.php" class="tag-cloud-link">Event</a>
                <a href="user-ticketing/login-user-ticketing.php" class="tag-cloud-link">Buy Ticket</a>
                <a href="moreInformasi.php" class="tag-cloud-link">FAQ</a>
              </div>
            </div>
          
          

          </div> <!-- .col-md-8 -->
          <div class="col-md-4 sidebar ftco-animate">
          
            <div class="sidebar-box ftco-animate">
              <div class="categories">
                <h3>Categories</h3>
                <li><a href="event.html">Event <span>(3)</span></a></li>
                <li><a href="index.html">Fasilitas<span>(6)</span></a></li>
              </div>
            </div>

            <div class="sidebar-box ftco-animate">
            <h3>More News</h3>
            <?php
            include "connecting.php";
              $sql = "SELECT * FROM news";
              $result = $koneksi->query($sql);
  
              if ($result->num_rows > 0) {
                  // Output data of each row
                  while ($row = $result->fetch_assoc()) {
                echo '<div class="block-21 mb-4 d-flex">';
                echo '<a class="blog-img mr-4" style="background-image: url(\'images/' . htmlspecialchars($row['gambar_berita']) . '\');"></a>';
                echo '<div class="text">';
                echo '<h3 class="heading"><a href="article.php?id=' . htmlspecialchars($row['ID_BERITA']) . '">' . htmlspecialchars($row['JUDUL_BERITA']) . '</a></h3>';
                echo '<div class="meta">';
                echo '<div><a href="#"><span class="icon-calendar"></span> ' . htmlspecialchars($row['tanggal_berita']) . '</a></div>';
                echo '<div><a href="#"><span class="icon-person"></span> ' . htmlspecialchars($row['PENULIS_BERITA']) . '</a></div>';
                echo '</div></div></div>';
              }
            } else {
                echo "0 results";
            }

            // Close database connection
            $koneksi->close();
            ?>
          </div>
           

            <div class="sidebar-box ftco-animate">
              <h3>Lokasi</h3>
              <p><iframe style="width: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.024365105721!2d112.79302617327994!3d-7.238059871085448!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9c4f53b233f%3A0x147a117a35d5f080!2sUPTD%20Taman%20Hiburan%20Pantai%20Kenjeran!5e0!3m2!1sid!2sid!4v1715503482749!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></p>
            </div>
          </div>

        </div>
      </div>
    </section> <!-- .section -->

    <footer class="ftco-footer ftco-bg-dark ftco-section">
  <div class="container">
    <div class="row mb-5">
      <div class="col-md">
        <div class="ftco-footer-widget mb-4">
          <h2 class="ftco-heading-2">Taman Hiburan Pantai Kenjeran</h2>
          <p>Nikmati suasana pesisir Pantai Utara Kota Surabaya dengan latar belakang Jembatan Suramadu!</p>
          <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
            <li class="ftco-animate"><a href="https://www.facebook.com/people/THP-Kenjeran/100066772881520/"><span class="icon-facebook"></span></a></li>
            <li class="ftco-animate"><a href="https://www.instagram.com/uptdobyekwisata/?img_index=1"><span class="icon-instagram"></span></a></li>
          </ul>
        </div>
      </div>
      
      <div class="col-md">
        <div class="ftco-footer-widget mb-4">
          <h2 class="ftco-heading-2">Have a Questions?</h2>
          <div class="block-23 mb-3">
            <ul>
              <li><a href="https://maps.app.goo.gl/GUX7sAYnTEcRKXqd6"><span class="icon icon-map-marker"></span><span class="text">Jl. Pantai Ria Kenjeran, Kenjeran, Kec. Bulak, Surabaya, Jawa Timur 60123</span></a></li>
              <li><a href="https://wa.me/6282338924959" target="_blank"><span class="icon icon-phone"></span><span class="text">+62 823 3892 4959</span></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="js/jquery.timepicker.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
    
  </body>
</html>