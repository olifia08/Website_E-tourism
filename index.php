<?php
include 'connecting.php';

// Mengambil data informasi wisata terbaru
$sql_info = "SELECT JAM_BUKA, JAM_TUTUP, JUMLAH_KUOTA, HARGA_TIKET_WEEKEND, HARGA_TIKET_WEEKDAY 
             FROM informasi_wisata 
             ORDER BY id_informasi DESC 
             LIMIT 1";

$result_info = mysqli_query($koneksi, $sql_info);
if ($result_info && mysqli_num_rows($result_info) > 0) {
    $info = mysqli_fetch_assoc($result_info);
    $jam_masuk = $info['JAM_BUKA'];
    $jam_tutup = $info['JAM_TUTUP'];
    $jumlah_kuota = $info['JUMLAH_KUOTA'];
    $tiket_weekend = $info['HARGA_TIKET_WEEKEND'];
    $tiket_weekday = $info['HARGA_TIKET_WEEKDAY'];
}

// Mengambil tahun transaksi
$sql_year = "SELECT DATE_FORMAT(waktu_transaksi, '%Y') AS tahun 
             FROM transaksi 
             GROUP BY tahun 
             ORDER BY tahun DESC 
             LIMIT 1";
$result_year = mysqli_query($koneksi, $sql_year);
$tahun = "Unknown";
if ($result_year && mysqli_num_rows($result_year) > 0) {
    $year_data = mysqli_fetch_assoc($result_year);
    $tahun = $year_data['tahun'];
}
$tanggal_hari_ini = date('Y-m-d');

// Query untuk mendapatkan JUMLAH_KUOTA dari data terbaru di tabel informasi wisata
$sql_kuota = "SELECT JUMLAH_KUOTA FROM informasi_wisata ORDER BY ID_INFORMASI DESC LIMIT 1";
$result_kuota = $koneksi->query($sql_kuota);

$jumlah_kuota = 0;
if ($result_kuota->num_rows > 0) {
    $row_kuota = $result_kuota->fetch_assoc();
    $jumlah_kuota = $row_kuota['JUMLAH_KUOTA'];
}

// Query untuk menghitung total JUMLAH_PEMESAN pada WAKTU_BOOKING hari ini
$sql_pemesanan = "SELECT SUM(JUMLAH_PEMESAN) as total_pemesanan FROM transaksi WHERE DATE(WAKTU_BOOKING) = '$tanggal_hari_ini'";
$result_pemesanan = $koneksi->query($sql_pemesanan);

$total_pemesanan = 0;
if ($result_pemesanan->num_rows > 0) {
    $row_pemesanan = $result_pemesanan->fetch_assoc();
    $total_pemesanan = $row_pemesanan['total_pemesanan'];
}

// Menghitung sisa kuota
$sisa_kuota = $jumlah_kuota - $total_pemesanan;

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <title>homepage</title>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  </head>
  <body>
    
   <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="oi oi-menu"></span> Menu
      </button>

      <div class="collapse navbar-collapse" id="ftco-nav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active"><a href="index.php" class="nav-link">Beranda</a></li>
          <li class="nav-item"><a href="news.php" class="nav-link">News</a></li>
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
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-start" data-scrollax-parent="true">
          <div class="col-md-9 ftco-animate" data-scrollax=" properties: { translateY: '70%' }">
            <h1 class="mb-4" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><strong>THP <br></strong> Taman Hiburan Pantai Kenjeran </h1>
            <p data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Nikmati suasana pesisir Pantai Utara Kota Surabaya dengan latar belakang Jembatan Suramadu!</p>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section services-section bg-light">
      <div class="container">
        <div class="row d-flex">
        <div class="row">
          <div class="col-md-3 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services d-block text-center">
              <div class="d-flex justify-content-center">
                <div class="icon"><span class="bi bi-clock-history"></span></div>
              </div>
              <div class="media-body p-2 mt-2">
                <h3 class="heading mb-3">JAM OPERASIONAL</h3>
                <br>
                <p><?php echo $jam_masuk . ' - ' . $jam_tutup; ?> WIB</p>
              </div>
            </div>
          </div>

          <div class="col-md-3 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services d-block text-center">
              <a href="#tiket" class="d-flex justify-content-center">
                <div class="icon"><span class="bi bi-cash-coin"></span></div>
              </a>
              <div class="media-body p-2 mt-2">
                <h3 class="heading mb-3">HARGA TIKET WISATA</h3>
                <br>
                <p>Tiket Masuk</p>
                <p>Tiket Parkir</p>
                <a href="booking.php" class="btn btn-outline-light" style="color:black;">PESAN SEKARANG</a>
              </div>
            </div>
          </div>

          <div class="col-md-3 d-flex align-self-stretch ftco-animate">
            <a href="moreInformasi.php" class="heading mb-3">
              <div class="media block-6 services d-block text-center">
                <div class="d-flex justify-content-center">
                  <div class="icon"><span class="flaticon-detective"></span></div>
                </div>
                <div class="media-body p-2 mt-2">
                  <h3>INFORMASI PENGUNJUNG</h3>
                  <br>
                  <p style="color:gray">S&K</p>
                  <p style="color:gray">FAQ</p>
                </div>
              </div>
            </a>
          </div>

          <div class="col-md-3 d-flex align-self-stretch ftco-animate">
            <a href="moreInformasi.php" class="heading mb-3">
              <div class="media block-6 services d-block text-center">
                <div class="d-flex justify-content-center">
                  <div class="icon"><span class="bi bi-ticket-perforated"></span></div>
                </div>
                <div class="media-body p-2 mt-2">
                  <h3>Kuota Tiket pada <?php echo date('d-m-Y'); ?></h3>
                  <p>Sisa Kuota Tiket: <?php echo $sisa_kuota."/". $jumlah_kuota ?></p>
                  <br>
                </div>
              </div>
            </a>
          </div>
        </div>
        </div>
      </div>
    </section>

    

    <section class="ftco-section ftco-destination">
    	<div class="container">
    		<div class="row justify-content-start mb-5 pb-3">
          <div class="col-md-7 heading-section ftco-animate">
          	<span class="subheading">Fasilitas</span>
            <h2 class="mb-4"><strong>Fasilitas</strong> Destination</h2>
          </div>
        </div>
    		<div class="row">
    			<div class="col-md-12">
    				<div class="destination-slider owl-carousel ftco-animate">
    					<div class="item">
		    				<div class="destination">
		    					<a href="#" class="img d-flex justify-content-center align-items-center" style="background-image: url(images/destination-1.jpg);">
		    						<div class="icon d-flex justify-content-center align-items-center">
		    							<span class="icon-search2"></span>
		    						</div>
		    					</a>
		    					<div class="text p-3">
		    						<h3><a href="#">Anjungan</a></h3>
		    					</div>
		    				</div>
	    				</div>
	    				<div class="item">
		    				<div class="destination">
		    					<a href="#" class="img d-flex justify-content-center align-items-center" style="background-image: url(images/destination-2.jpg);">
		    						<div class="icon d-flex justify-content-center align-items-center">
		    							<span class="icon-search2"></span>
		    						</div>
		    					</a>
		    					<div class="text p-3">
		    						<h3><a href="#">Mushalla</a></h3>
		    					</div>
		    				</div>
	    				</div>
	    				<div class="item">
		    				<div class="destination">
		    					<a href="#" class="img d-flex justify-content-center align-items-center" style="background-image: url(images/destination-3.jpg);">
		    						<div class="icon d-flex justify-content-center align-items-center">
		    							<span class="icon-search2"></span>
		    						</div>
		    					</a>
		    					<div class="text p-3">
		    						<h3><a href="#">Panggung Hiburan</a></h3>
		    					</div>
		    				</div>
	    				</div>
	    				<div class="item">
		    				<div class="destination">
		    					<a href="#" class="img d-flex justify-content-center align-items-center" style="background-image: url(images/destination-4.jpg);">
		    						<div class="icon d-flex justify-content-center align-items-center">
		    							<span class="icon-search2"></span>
		    						</div>
		    					</a>
		    					<div class="text p-3">
		    						<h3><a href="#">Gazebo</a></h3>
		    					</div>
		    				</div>
	    				</div>
	    				<div class="item">
		    				<div class="destination">
		    					<a href="#" class="img d-flex justify-content-center align-items-center" style="background-image: url(images/destination-5.jpg);">
		    						<div class="icon d-flex justify-content-center align-items-center">
		    							<span class="icon-search2"></span>
		    						</div>
		    					</a>
		    					<div class="text p-3">
		    						<h3><a href="#">Depot</a></h3>
		    					</div>
		    				</div>
	    				</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </section>

       <section class="ftco-section">
    	<div class="container">
    		<div class="row d-md-flex">
	    		<div class="col-md-6 ftco-animate img about-image" style="background-image: url(images/tiket.jpg);">
	    		</div>
	    		<div class="col-md-6 ftco-animate p-md-5">
		    		<div class="row">
		          <div class="col-md-12 nav-link-wrap mb-5">
		            <div class="nav ftco-animate nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
		            	<a class="nav-link" id="v-pills-mission-tab" data-toggle="pill" href="#v-pills-mission" role="tab" aria-controls="v-pills-mission" aria-selected="false">Tiket Parkir</a>

		              <a class="nav-link active" id="v-pills-whatwedo-tab" data-toggle="pill" href="#v-pills-whatwedo" role="tab" aria-controls="v-pills-whatwedo" aria-selected="true">Tiket Wisata</a>
		            </div>
		          </div>
		          <div class="col-md-12 d-flex align-items-center" id="tiket">
		            
		            <div class="tab-content ftco-animate" id="v-pills-tabContent">

		              <div class="tab-pane fade show active" id="v-pills-whatwedo" role="tabpanel" aria-labelledby="v-pills-whatwedo-tab">
		              	<div>
                    <div>
			                <h2 class="mb-4">Tiket Wisata</h2>
                      <p>Harga Tiket Senin - Jum'at = Rp.<?php echo $tiket_weekday?></p>
                      <p>Harga Tiket Sabtu - Minggu = Rp.<?php echo $tiket_weekend?></p>
			              	<p>
			              		<p>Tiket ini berlaku untuk pengunjung anak-anak dengan tinggi lebih dari 85 centimeter. Jika kurang dari itu, maka tidak perlu membayar tiket masuk.</p>
			              	</p>
				            </div>
				            </div>
		              </div>

		              <div class="tab-pane fade" id="v-pills-mission" role="tabpanel" aria-labelledby="v-pills-mission-tab">
		                <div>
			                <h2 class="mb-4">Tiket Parkir</h2>
			              	<img src="images/parkir.jpg" style="height:130px;">
			
				            </div>
		              </div>

		              <div class="tab-pane fade" id="v-pills-goal" role="tabpanel" aria-labelledby="v-pills-goal-tab">
		                <div>
			                <h2 class="mb-4">Help Our Customer</h2>
			              	<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.</p>
			                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt voluptate, quibusdam sunt iste dolores consequatur</p>
				            </div>
		              </div>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
    	</div>
    </section>

    <section class="ftco-section ftco-counter img" id="section-counter" style="background-image: url('images/bg_1.jpg');">
    <div class="container">
        <div class="row justify-content-center mb-5 pb-3">
            <div class="col-md-7 text-center heading-section heading-section-white ftco-animate">
                <h2 class="mb-4">JUMLAH WISATAWAN THP KENJERAN <?php echo date('Y'); ?></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <canvas id="visitorChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('visitorChart').getContext('2d');
            var visitorChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Jumlah Pengunjung',
                        data: [],
                        backgroundColor: 'rgba(250,0,0,0.9)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            function fetchData() {
                $.ajax({
                    url: 'fetch_data.php',
                    method: 'GET',
                    success: function(data) {
                        console.log(data); // Tambahkan log ini untuk melihat data yang diterima
                        var chartData = JSON.parse(data);
                        visitorChart.data.labels = chartData.days;
                        visitorChart.data.datasets[0].data = chartData.total_transaksi;
                        visitorChart.update();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching data:', textStatus, errorThrown);
                    }
                });
            }

            // Fetch data initially
            fetchData();

            // Set interval to fetch data every 30 seconds
            setInterval(fetchData, 30000);
        });
    </script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
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
              <li><a href="https://wa.me/6282338924959" target="_blank"><span class="icon icon-phone"></span><span class="text">+62 823 3892 4959</span></a></li>
              <li><a href="https://maps.app.goo.gl/GUX7sAYnTEcRKXqd6"><span class="icon icon-map-marker"></span><span class="text">Jl. Pantai Ria Kenjeran, Kenjeran, Kec. Bulak, Surabaya, Jawa Timur 60123</span></a></li>
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