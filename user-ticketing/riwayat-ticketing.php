<?php
session_start();
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_USER'])) {
    header("Location: login-user.php");
    exit();
}

// Mendapatkan username dari sesi
$username_login = $_SESSION['USERNAME_USER'];

// Siapkan pernyataan SQL untuk mengambil ID_USER
$sql_user = "SELECT ID_USER FROM user WHERE USERNAME_USER = ?";
$stmt_user = mysqli_prepare($koneksi, $sql_user);

if ($stmt_user) {
    mysqli_stmt_bind_param($stmt_user, "s", $username_login);
    mysqli_stmt_execute($stmt_user);
    mysqli_stmt_bind_result($stmt_user, $id_user);
    if (mysqli_stmt_fetch($stmt_user)) {
        // Mengatur kembali sesi ID_USER dengan id yang diambil dari database
        $_SESSION['ID_USER'] = $id_user;
    } else {
        echo "Terjadi kesalahan: Data user tidak ditemukan.";
        exit();
    }
    mysqli_stmt_close($stmt_user);
} else {
    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Riwayat -  Ticketing</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Alex+Brush" rel="stylesheet">
    <link rel="stylesheet" href="../css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../css/ionicons.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../css/jquery.timepicker.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/icomoon.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="oi oi-menu"></span> Menu
        </button>
        <div class="collapse navbar-collapse" id="ftco-nav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item "><a href="beranda-user-ticketing.php" class="nav-link">Beranda</a></li>
                <li class="nav-item"><a href="news.php" class="nav-link">News</a></li>
                <li class="nav-item"><a href="event.php" class="nav-link">Event</a></li>
                <li class="nav-item active"><a href="riwayat-ticketing.php" class="nav-link">Riwayat</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['USERNAME_USER']; ?></a>
                </li>
                <li class="nav-item cta"><a href="logout-user-ticketing.php" class="nav-link"><span>Log Out</span></a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-wrap js-fullheight" style="background-image: url('../images/bg_1.jpg');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center" data-scrollax-parent="true">
            <div class="col-md-9 ftco-animate text-center" data-scrollax="properties: { translateY: '70%' }">
                <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><span class="mr-2"><a href="../index.php">Home</a></span> <span>Riwayat Transaksi</span></p>
                <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Riwayat Transaksi</h1>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row d-flex">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Waktu Transaksi</th>
                    <th>Waktu Booking</th>
                    <th>Nama</th>
                    <th>Alamat Pemesan</th>
                    <th>Jumlah Tiket</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Mengambil ID_USER dari sesi
                $id_user = $_SESSION['ID_USER'];

                // Siapkan pernyataan SQL untuk mengambil riwayat transaksi pengguna
                $sql_transaksi = "SELECT ID_TRANSAKSI, WAKTU_TRANSAKSI, WAKTU_BOOKING, NAMA_PEMESAN, ALAMAT_PEMESAN, JUMLAH_PEMESAN, TOTAL_HARGA, VALIDASI FROM transaksi WHERE ID_USER = ? ORDER BY ID_TRANSAKSI DESC";
                $stmt_transaksi = mysqli_prepare($koneksi, $sql_transaksi);

                if ($stmt_transaksi) {
                    mysqli_stmt_bind_param($stmt_transaksi, "s", $id_user); // Mengikat parameter sebagai integer
                    mysqli_stmt_execute($stmt_transaksi);
                    mysqli_stmt_bind_result($stmt_transaksi, $id_transaksi, $waktu_transaksi, $waktu_booking, $nama_pemesan, $alamat_pemesan, $jumlah_pemesan, $total_harga, $validasi);

                    while (mysqli_stmt_fetch($stmt_transaksi)) {
                        echo "<tr>";
                        echo "<td>$id_transaksi</td>";
                        echo "<td>$waktu_transaksi</td>";
                        echo "<td>$waktu_booking</td>";
                        echo "<td>$nama_pemesan</td>";
                        echo "<td>$alamat_pemesan</td>";
                        echo "<td>$jumlah_pemesan</td>";
                        echo "<td>$total_harga</td>";
                        if ($validasi == NULL) {
                            echo "<td>proses</td>";
                        } else {
                            echo "<td>$validasi</td>";
                        }
                        echo "</td>";
                        echo "<td>";
                        if ($validasi == 'berhasil') {
                            echo "<a href='detail-transaksi-user-ticketing.php?id=$id_transaksi' class='btn btn-success' target='_blank'>Cetak Transaksi</a>";
                        } elseif ($validasi == "gagal") {
                            echo "<a href='booking.php' class='btn btn-warning'>Pesan Kembali</a>";
                        } else {
                            echo "proses";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    mysqli_stmt_close($stmt_transaksi);
                } else {
                    echo "<tr><td colspan='9'>Terjadi kesalahan: " . mysqli_error($koneksi) . "</td></tr>";
                }

                // Menutup koneksi
                mysqli_close($koneksi);
                ?>
                </tbody>
            </table>
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

<!-- loader -->
<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>

`      <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-migrate-3.0.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.easing.1.3.js"></script>
    <script src="../js/jquery.waypoints.min.js"></script>
    <script src="../js/jquery.stellar.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/jquery.magnific-popup.min.js"></script>
    <script src="../js/aos.js"></script>
    <script src="../js/jquery.animateNumber.min.js"></script>
    <script src="../js/bootstrap-datepicker.js"></script>
    <script src="../js/jquery.timepicker.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&sensor=false"></script>
    <script src="../js/scrollax.min.js"></script>
    <script src="../js/main.js"></script>

</body>
</html>
