<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pemesanan Tiket - DirEngine</title>
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
<?php
session_start();
include '../connecting.php';

function generateNewID($koneksi) {
    $sql = "SELECT ID_TRANSAKSI FROM transaksi ORDER BY ID_TRANSAKSI DESC LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    $row = mysqli_fetch_assoc($result);
    $lastID = $row ? $row['ID_TRANSAKSI'] : null;

    if ($lastID) {
        $last_num = (int)substr($lastID, 2);
        $new_num = $last_num + 1;
        return 'TR' . $new_num;
    } else {
        return 'TR1';
    }
}

function calculateTicketPrice($waktu_booking) {
    $senin_jumat_price = 10000;
    $sabtu_minggu_price = 15000;
    $day_of_week = date('N', strtotime($waktu_booking));
    return ($day_of_week >= 6) ? $sabtu_minggu_price : $senin_jumat_price;
}

// Fungsi untuk mengambil jumlah kuota dari tabel informasi wisata
function getRemainingQuota($koneksi) {
    $sql_info_wisata = "SELECT JUMLAH_KUOTA FROM informasi_wisata LIMIT 1"; // Mengambil jumlah kuota tanpa pengurutan
    $result_info_wisata = mysqli_query($koneksi, $sql_info_wisata);

    if ($result_info_wisata && mysqli_num_rows($result_info_wisata) > 0) {
        $row_info_wisata = mysqli_fetch_assoc($result_info_wisata);
        return $row_info_wisata['JUMLAH_KUOTA'];
    } else {
        return "Data informasi wisata tidak ditemukan";
    }
}



// Memperoleh waktu booking dari input form
$waktu_booking = ''; // Inisialisasi waktu booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pengaturan waktu booking jika ada data yang dikirimkan melalui POST
    $waktu_booking_raw = $_POST['HariKunjungan'];
    $parts = explode(', ', $waktu_booking_raw);

    if (count($parts) == 2) {
        $waktu_booking = DateTime::createFromFormat('d-m-Y', trim($parts[1]));
        
        if ($waktu_booking !== false) {
          $waktu_booking = $waktu_booking->format('Y-m-d');
        }
      }
    }
    
    // Fungsi untuk menghitung jumlah pemesan pada hari kunjungan yang sama dari tabel transaksi
    function getTotalPurchases($koneksi, $waktu_booking) {
      $sql_jumlah_pemesan = "SELECT SUM(JUMLAH_PEMESAN) AS total_pemesan FROM transaksi WHERE DATE(WAKTU_BOOKING) = DATE(?)";
      $stmt = mysqli_prepare($koneksi, $sql_jumlah_pemesan);
    
      if ($stmt) {
          mysqli_stmt_bind_param($stmt, "s", $waktu_booking);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $total_pemesan);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);
          
          return $total_pemesan !== null ? $total_pemesan : 0;
      } else {
          return "Terjadi kesalahan: " . mysqli_error($koneksi);
      }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['ID_USER'])) {
        $id_transaksi = generateNewID($koneksi);
        $id_user = $_SESSION['ID_USER'];
        $waktu_transaksi = date("Y-m-d H:i:s");
        $nama_pemesan = $_POST['Nama'];
        $jumlah_pemesan = $_POST['JumlahTiket'];
        $alamat_pemesan = $_POST['Alamat'];
        $type = $_POST['Pengunjung'];
        $harga_per_tiket = calculateTicketPrice($waktu_booking);
        $total_harga = $jumlah_pemesan * $harga_per_tiket;
        $validasi = "Belum";
        $bukti_pembayaran = $_FILES["bukti_pembayaran"]["name"];

        if (!empty($bukti_pembayaran)) {
            $temp_file = $_FILES["bukti_pembayaran"]["tmp_name"];
            $target_directory = "../images/BUKTI_PEMBAYARAN/";
            $target_file = $target_directory . basename($bukti_pembayaran);

            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = array("jpg", "jpeg", "png");
            if (!in_array($file_type, $allowed_types)) {
                echo "<script>alert('Error: Jenis file yang diunggah tidak didukung.')</script>";
                exit();
            }

            $file_size = $_FILES["bukti_pembayaran"]["size"];
            $max_size = 3 * 1024 * 1024; // 3MB
            if ($file_size > $max_size) {
                echo "<script>alert('Error: Ukuran file terlalu besar. Maksimal 3MB.')</script>";
                exit();
            }

            if (move_uploaded_file($temp_file, $target_file)) {
                $sql = "INSERT INTO transaksi (ID_TRANSAKSI, ID_USER, WAKTU_TRANSAKSI, WAKTU_BOOKING, NAMA_PEMESAN, JUMLAH_PEMESAN, ALAMAT_PEMESAN, TYPE_USER, TOTAL_HARGA, BUKTI_PEMBAYARAN, VALIDASI) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($koneksi, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssssisisss", $id_transaksi, $id_user, $waktu_transaksi, $waktu_booking, $nama_pemesan, $jumlah_pemesan, $alamat_pemesan, $type, $total_harga, $bukti_pembayaran, $validasi);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        mysqli_close($koneksi);
                        header("Location: riwayat-ticketing.php");
                        exit();
                    } else {
                        echo "<script>alert('Terjadi kesalahan saat menyimpan data: " . mysqli_error($koneksi) . "')</script>";
                    }
                } else {
                    echo "<script>alert('Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi) . "')</script>";
                }
            } else {
                echo "<script>alert('Error: Gagal mengunggah gambar.')</script>";
            }
        } else {
            echo "<script>alert('Error: File gambar event tidak ditemukan.')</script>";
        }
    } else {
        echo "<script>alert('Session ID_USER tidak ditemukan. Pastikan pengguna telah berhasil login.')</script>";
    }
}

// Menghitung sisa kuota
$jumlah_kuota = getRemainingQuota($koneksi);
$total_pemesan = getTotalPurchases($koneksi, $waktu_booking);
// echo $total_pemesan;
// echo $jumlah_kuota; // Pastikan $waktu_booking sudah diambil dari formulir atau dari sesuatu yang sesuai dengan kebutuhan Anda
$sisa_kuota = $jumlah_kuota - $total_pemesan;

// // Lanjutkan dengan menampilkan sisa kuota
// echo "Sisa Kuota: $sisa_kuota";

// Selanjutnya, Anda dapat melanjutkan dengan menampilkan form atau informasi lain yang diperlukan.
?>



<!-- Your HTML form -->


    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="oi oi-menu"></span> Menu
      </button>

      <div class="collapse navbar-collapse" id="ftco-nav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item "><a href="../index.php" class="nav-link">Beranda</a></li>
          <li class="nav-item"><a href="news.php" class="nav-link">News</a></li>
          <li class="nav-item"><a href="event.php" class="nav-link">Event</a></li>
          <li class="nav-item"><a href="riwayat-ticketing.php" class="nav-link">Riwayat</a></li>
          <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['USERNAME_USER']; ?></a>
                </li>
          <li class="nav-item cta"><a href="logout-user-ticketing.php" class="nav-link"><span>Log Out</span></a></li>
        </ul>
      </div>
    </div>
  </nav>
    <!-- END nav -->
    
    <div class="hero-wrap js-fullheight" style="background-image: url('../images/bg_1.jpg');">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-center" data-scrollax-parent="true">
          <div class="col-md-9 ftco-animate text-center" data-scrollax="properties: { translateY: '70%' }">
            
            <h1 class="mb-3 bread" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }">Pemesanan Tiket</h1>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section contact-section ftco-degree-bg">
      <div class="container">
        <div class="row block-9">
          <div class="col-md-6 pr-md-5">
            <!-- Validasi untuk memastikan hari kunjungan terpilih -->
            <form name="myTiket" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="tiketForm" onsubmit="return validateForm()" enctype="multipart/form-data">



              <div class="form-group">
                <label for="HariKunjungan">Hari Kunjungan</label>
              <select class="form-control" id="HariKunjungan" name="HariKunjungan">
                <option value="" disabled selected style="display:none;">Hari Kunjungan</option>
                <?php
                // Membuat pilihan hari dalam bahasa Indonesia
                $today = time();
                $day_names = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');

                for ($i = 0; $i < 7; $i++) {
                    $current_day = strtotime("+$i days", $today);
                    $day_name = $day_names[date('w', $current_day)];
                    $date_formatted = date('d-m-Y', $current_day);
                    echo "<option value=\"$day_name, $date_formatted\">$day_name, $date_formatted</option>";
                }
                ?>
              </select>
              </div>
              <div class="form-group">
                <label for="Nama">Nama</label>
                <input type="text" class="form-control" placeholder="Nama" id="Nama" name="Nama" required>
              </div>
              
              <div class="form-group">
                <label for="Alamat">Alamat</label>
                <input type="text" name="Alamat" id="Alamat" cols="30" rows="7" class="form-control" placeholder="Alamat" required>
              </div>
              <div class="form-group">
                 <label for="Pengujung">Pengunjung</label>
                  <select class="form-control" id="Pengunjung" name="Pengunjung" required>
                      <option value="" disabled selected style="display:none;">Pengunjung</option>
                      <option value="wisnu">wisnu</option>
                      <option value="wisman">wisman</option>
                  </select>
                  <small class="form-text text-muted">NB: wisnu untuk pengunjung dari Indonesia <br>wisman untuk pengunjung dari luar negeri </small>
              </div>
              <div class="form-group">
                <label for="JumlahTiket">Jumlah Tiket</label>
                <input type="number" class="form-control" placeholder="Jumlah Tiket" id="JumlahTiket" name="JumlahTiket" min="1" required>
                <small class="form-text text-muted">Sisa kuota hari ini: <?php echo $sisa_kuota; ?> tiket</small>
              </div>
              <div class="form-group">
                <label>Total Harga:</label>
                <input type="text" id="totalHarga" class="form-control" readonly>
            </div>
            <p>Pembayaran Dana :  <a href=" https://link.dana.id/qr/8kkruuqj">Klik disini</a></p>
            <small class="form-text text-muted">
            Mohon transfer sesuai dengan total harga tiket yang tertera di atas.</small>
            <div class="form-group">
              <label for="bukti_pembayaran">Bukti Transfer</label>
              <input type="file" class="form-control-file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/BUKTI_PEMBAYARAN/*">
            </div>
              <div class="form-group">
                <input type="submit" value="PESAN" class="btn btn-primary py-3 px-5">
              </div>
            </form>
          </div>
          <div class="col-md-6" id="">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.024365105715!2d112.79302617357078!3d-7.2380598710860715!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9c4f53b233f%3A0x147a117a35d5f080!2sUPTD%20Taman%20Hiburan%20Pantai%20Kenjeran!5e0!3m2!1sid!2sid!4v1715595853762!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
        </div>
      </div>
    </section>

    <footer class="ftco-footer ftco-bg-dark ftco-section">
      <div class="container">
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">DirEngine</h2>
              <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-5">
              <h2 class="ftco-heading-2">Information</h2>
              <ul class="list-unstyled">
                <li><a href="#" class="py-2 d-block">About</a></li>
                <li><a href="#" class="py-2 d-block">Service</a></li>
                <li><a href="#" class="py-2 d-block">Terms and Conditions</a></li>
                <li><a href="#" class="py-2 d-block">Best Price Guarantee</a></li>
                <li><a href="#" class="py-2 d-block">Privacy &amp; Cookies Policy</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Customer Support</h2>
              <ul class="list-unstyled">
                <li><a href="#" class="py-2 d-block">FAQ</a></li>
                <li><a href="#" class="py-2 d-block">Payment Option</a></li>
                <li><a href="#" class="py-2 d-block">Booking Tips</a></li>
                <li><a href="#" class="py-2 d-block">How it works</a></li>
                <li><a href="#" class="py-2 d-block">Contact Us</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
                <h2 class="ftco-heading-2">Have a Questions?</h2>
                <div class="block-23 mb-3">
                  <ul>
                    <li><span class="icon icon-map-marker"></span><span class="text">203 Fake St. Mountain View, San Francisco, California, USA</span></li>
                    <li><a href="#"><span class="icon icon-phone"></span><span class="text">+2 392 3929 210</span></a></li>
                    <li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@yourdomain.com</span></a></li>
                  </ul>
                </div>
              </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">

            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
  Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart color-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib.com</a>
  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
          </div>
        </div>
      </div>
    </footer>
    
    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#F96D00"/></svg></div>

    <script src="../js/jquery.min.js"></script>
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
    <script src="../js/scrollax.min.js"></script>
    <script src="../js/main.js"></script>
    <script>
      // Menonaktifkan input lainnya saat halaman dimuat
      window.onload = function() {
          disableInputs();
      };

      // Mengaktifkan atau menonaktifkan input nama, jumlah tiket, alamat, dan jenis pengunjung
      function disableInputs() {
          document.getElementById("Nama").disabled = true;
          document.getElementById("JumlahTiket").disabled = true;
          document.getElementById("Alamat").disabled = true;
          document.getElementById("Pengunjung").disabled = true;
          document.getElementById("uploadForm").style.display = "none"; // Sembunyikan form unggah
          document.getElementById("fileToUpload").disabled = true; // Menonaktifkan input unggah
          document.getElementById("tombolPesan").disabled = true; // Menonaktifkan tombol pesan
      }

      // Mengaktifkan input nama, jumlah tiket, alamat, dan jenis pengunjung setelah hari kunjungan dipilih
      function enableInputs() {
          document.getElementById("Nama").disabled = false;
          document.getElementById("JumlahTiket").disabled = false;
          document.getElementById("Alamat").disabled = false;
          document.getElementById("Pengunjung").disabled = false;
          document.getElementById("uploadForm").style.display = "block"; // Tampilkan form unggah
          document.getElementById("fileToUpload").disabled = false; // Aktifkan input unggah
          document.getElementById("tombolPesan").disabled = false; // Aktifkan tombol pesan
      }

        // Hitung total harga berdasarkan hari kunjungan
        function updateTotalHarga() {
    var jumlahTiket = document.getElementById("JumlahTiket").value;
    var hariKunjungan = document.getElementById("HariKunjungan").value;
    var parts = hariKunjungan.split(', ');
    var dayName = parts[0];

    var hargaPerTiket = (dayName == "Sabtu" || dayName == "Minggu") ? 15000 : 10000;
    var totalHarga = jumlahTiket * hargaPerTiket;

    document.getElementById("totalHarga").value = "Rp " + totalHarga.toLocaleString();
}

      document.getElementById("JumlahTiket").addEventListener("input", updateTotalHarga);
      document.getElementById("HariKunjungan").addEventListener("change", updateTotalHarga);


        // Event listener untuk memantau perubahan pada dropdown pilihan hari kunjungan
        document.getElementById("HariKunjungan").addEventListener("change", function() {
            enableInputs(); // Mengaktifkan input lainnya setelah hari kunjungan dipilih
        });

        // Fungsi untuk memvalidasi pengisian kolom
        function validateForm() {
            var hariKunjungan = document.getElementById("HariKunjungan").value;
            if (hariKunjungan === "") {
                alert("Mohon pilih hari kunjungan terlebih dahulu.");
                return false; // Jangan kirim formulir jika hari kunjungan belum dipilih
            }
            return true; // Kirim formulir jika semua validasi terpenuhi
        }

      </script>

</body>
</html>
