<!DOCTYPE html>
<html lang="en">
<head>
  <title>More Information</title>
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

  <style>
    .syarat {
      text-align: center;
      font-size: 50px;
    }
    .tubuh {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    .container {
      width: 100%;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .faq-section {
      width: 100%;
    }
    .faq-item {
      margin: 20px 0;
    }
    .faq-question {
      font-weight: bold;
      cursor: pointer;
      margin: 0;
      padding: 15px;
      background-color: #ff9999;
      color: white;
      border-radius: 5px;
    }
    .faq-answer {
      display: none;
      margin: 0;
      padding: 15px;
      background-color: #f9f9f9;
      border-left: 5px solid #007BFF;
      border-radius: 0 0 5px 5px;
    }
    .item {
      font-weight: bold;
      color: black;
      padding: 3px;
      border-radius: 5px;
    }
  </style>
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
          <li class="nav-item"><a href="news.php" class="nav-link">News</a></li>
          <li class="nav-item"><a href="event.php" class="nav-link">Event</a></li>
          <li class="nav-item cta"><a href="user-ticketing/login-user-ticketing.html" class="nav-link"><span>Login</span></a></li>
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
        <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><span class="mr-2"><a href="index.html">Home</a></span> <span>Information</span></p>
        <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">More Information</h1>
      </div>
    </div>
  </div>
</div>

<section class="ftco-section contact-section ftco-degree-bg">
  <div class="container">
    <h3>Syarat & Ketentuan</h3>
  <div class="faq-section">
  <?php
          include "connecting.php";
          $sql = "SELECT * FROM s_k";
          $result = $koneksi->query($sql);

          if ($result->num_rows > 0) {
              // Output data of each row
              while ($row = $result->fetch_assoc()) {
                  echo '<ul>';
                  echo '<li class="item">' . $row["POIN_SK"] . '</li>';
                  echo '</ul>';
              }
          } else {
              echo "0 results";
          }
          ?>
          
          
  </div>
  
  </div>
  

  <div class="tubuh">
    <div class="container">
      <h3>F&Q</h3>
      <div class="faq-section">
      <?php
            include "connecting.php";
            $sql = "SELECT * FROM f_q";
            $result = $koneksi->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="faq-item">
                        <h6 class="faq-question"><?php echo $row["PERTANYAAN_FQ"]; ?></h6>
                        <div class="faq-answer">
                            <p><?php echo $row["JAWABAN_FQ"]; ?></p>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "Tidak ada pertanyaan yang tersedia.";
            }
            ?>
      </div>

      <script>
        document.querySelectorAll('.faq-question').forEach(item => {
          item.addEventListener('click', () => {
            const answer = item.nextElementSibling;
            const allAnswers = document.querySelectorAll('.faq-answer');
            allAnswers.forEach(ans => {
              if (ans !== answer) {
                ans.style.display = 'none';
              }
            });
            answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
          });
        });
      </script>
    </div>
  </div>
</section>

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

<div id="ftco-loader" class="show fullscreen">
  <svg class="circular" width="48px" height="48px">
    <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/>
    <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/>
  </svg>
</div>

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
