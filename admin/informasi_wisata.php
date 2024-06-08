<?php
session_start();
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_ADMIN'])) {
    header("Location: login-admin.php");
    exit();
}

// Mendapatkan username dari sesi
$username_login = $_SESSION['USERNAME_ADMIN'];

// Siapkan pernyataan SQL untuk mengambil USERNAME_ADMIN
$sql = "SELECT USERNAME_ADMIN FROM data_admin WHERE USERNAME_ADMIN = ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username_login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nama);
    if (mysqli_stmt_fetch($stmt)) {
        // Mengatur kembali sesi USERNAME_ADMIN dengan nama yang diambil dari database
        $_SESSION['USERNAME_ADMIN'] = $nama;
    } else {
        echo "Terjadi kesalahan: Data admin tidak ditemukan.";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
}

function generateNewID($koneksi) {
    $sql = "SELECT ID_INFORMASI FROM informasi_wisata ORDER BY ID_INFORMASI DESC LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    $lastID = mysqli_fetch_assoc($result)['ID_INFORMASI'];

    if ($lastID) {
        $num = (int)substr($lastID, 2) + 1;
        $newID = 'IF' . str_pad($num, 3, '0', STR_PAD_LEFT);

        // Periksa apakah ID baru sudah ada di database
        while (true) {
            $checkSql = "SELECT ID_INFORMASI FROM informasi_wisata WHERE ID_INFORMASI = ?";
            $checkStmt = mysqli_prepare($koneksi, $checkSql);
            mysqli_stmt_bind_param($checkStmt, "s", $newID);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_store_result($checkStmt);

            if (mysqli_stmt_num_rows($checkStmt) == 0) {
                mysqli_stmt_close($checkStmt);
                break;
            }

            mysqli_stmt_close($checkStmt);
            $num++;
            $newID = 'IF' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        return $newID;
    } else {
        return 'IF001';
    }
}

$jam_masuk = '';
$jam_tutup = '';
$jumlah_kuota = '';
$tiket_weekend = '';
$tiket_weekday = '';

// Asumsikan kolom 'id' adalah primary key
$sql = "SELECT JAM_BUKA, JAM_TUTUP, JUMLAH_KUOTA, HARGA_TIKET_WEEKEND, HARGA_TIKET_WEEKDAY 
        FROM informasi_wisata 
        ORDER BY id_informasi DESC 
        LIMIT 1";

$result = mysqli_query($koneksi, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $jam_masuk = $row['JAM_BUKA'];
    $jam_tutup = $row['JAM_TUTUP'];
    $jumlah_kuota = $row['JUMLAH_KUOTA'];
    $tiket_weekend = $row['HARGA_TIKET_WEEKEND'];
    $tiket_weekday = $row['HARGA_TIKET_WEEKDAY'];
}

// Memproses form ketika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_informasi = generateNewID($koneksi);
    $jam_masuk = $_POST['JAM_BUKA'];
    $jam_tutup = $_POST['JAM_TUTUP'];
    $jumlah_kuota = $_POST['JUMLAH_KUOTA'];
    $tiket_weekend = $_POST['HARGA_TIKET_WEEKEND'];
    $tiket_weekday = $_POST['HARGA_TIKET_WEEKDAY'];
    $id_admin = $_SESSION['ID_ADMIN'];
    $tanggal_perubahan = date('Y-m-d');

    // Update data ke dalam database
    $sql = "INSERT INTO informasi_wisata (ID_INFORMASI, ID_ADMIN, tanggal_perubahan, JAM_BUKA, JAM_TUTUP, JUMLAH_KUOTA, HARGA_TIKET_WEEKEND, HARGA_TIKET_WEEKDAY) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssss", $id_informasi, $id_admin, $tanggal_perubahan, $jam_masuk, $jam_tutup, $jumlah_kuota, $tiket_weekend, $tiket_weekday);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($koneksi);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Terjadi kesalahan saat memperbarui data: " . mysqli_error($koneksi);
        }
    } else {
        echo "Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi);
    }
}
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Jam Operasional</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-size: 0.875rem;
    }

    .sidebar {
      position: fixed;
      top: 0;
      bottom: 0;
      left: 0;
      z-index: 100;
      padding: 48px 0 0;
    }

    .sidebar-sticky {
      position: relative;
      top: 0;
      height: calc(100vh - 48px);
      padding-top: .5rem;
      overflow-x: hidden;
      overflow-y: auto;
    }

    .nav-link {
      font-weight: 500;
      color: #c0392b;
    }

    .nav-link .fas {
      margin-right: 8px;
    }

    .nav-link:hover {
      color: #e74c3c;
    }

    .nav-link.active {
      color: #e74c3c;
    }

    .navbar-brand {
      color: #c0392b !important;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Dashboard Admin</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> <?php echo $_SESSION['USERNAME_ADMIN']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="beranda-admin.php">
                <i class="fas fa-chart-line"></i> Dashboard Admin
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="validasi-ticket.php">
                <i class="fas fa-check-circle"></i> Validasi Ticket
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="news-admin.php">
                <i class="fas fa-newspaper"></i> News Admin
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="jam-operasional.php">
                <i class="bi bi-info-circle-fill"></i> Informasi Wisata
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="event-admin.php">
                <i class="fas fa-calendar-alt"></i> Event
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="faq-admin.php">
                <i class="fas fa-question-circle"></i> FAQ
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div id="jamOperasional" class="pt-3">
          <h2>Jam Operasional</h2>
          <?php if ($_SERVER["REQUEST_METHOD"] != "POST"): ?>
          <div class="form-group">
            <label for="jamOperasional"></label>
            <input type="text" class="form-control" id="jamOperasional" value="<?php echo $jam_masuk . ' - ' . $jam_tutup; ?> WIB" readonly>
            <small class="form-text text-muted">Format: Jam:menit:detik - Jam:menit:detik WIB</small>
          </div>
          <div class="form-group">
            <label for="jumlah_kuota"><h2>Jumlah Kuota</h2></label>
            <input type="text" class="form-control" id="jumlah_kuota" value="<?php echo $jumlah_kuota ?> orang" readonly>
            <small class="form-text text-muted">NB: Jumlah kuota perhari </small>
          </div>
          <div class="form-group">
            <label for="hargatiket"><h2>Harga Tiket</h2></label>
            <input type="text" class="form-control" id="hargatiket" value="<?php echo 'Sabtu-Minggu : '.$tiket_weekend . ' Senin-Jumat : ' . $tiket_weekday; ?> WIB" readonly>
          </div>
          <a href="#" class="btn btn-primary mb-3" id="editButton">Edit</a>
          <?php else: ?>
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
              <label for="JAM_BUKA">Jam Masuk:</label>
              <input type="text" class="form-control" id="JAM_BUKA" name="JAM_BUKA" value="<?php echo $jam_masuk; ?>" required>
            </div>
            <div class="form-group">
              <label for="JAM_TUTUP">Jam Tutup:</label>
              <input type="text" class="form-control" id="JAM_TUTUP" name="JAM_TUTUP" value="<?php echo $jam_tutup; ?>" required>
            </div>
            <div class="form-group">
              <label for="JUMLAH_KUOTA">Jumlah Kuota : </label>
              <input type="text" class="form-control" id="JUMLAH_KUOTA" name="JUMLAH_KUOTA" value="<?php echo $jumlah_kuota; ?>" required>
            </div>
            <div class="form-group">
              <label for="HARGA_TIKET_WEEKEND">Harga Tiket (sabtu-minggu) : </label>
              <input type="text" class="form-control" id="HARGA_TIKET_WEEKEND" name="HARGA_TIKET_WEEKEND" value="<?php echo $tiket_weekend; ?>" required>
            </div>
            <div class="form-group">
              <label for="HARGA_TIKET_WEEKDAY">Harga Tiket (senin-jumat) : </label>
              <input type="text" class="form-control" id="HARGA_TIKET_WEEKDAY" name="HARGA_TIKET_WEEKDAY" value="<?php echo $tiket_weekday; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </form>
          <?php endif; ?>
        </div>
      </main>
        </div>
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    document.getElementById('editButton').addEventListener('click', function() {
      document.getElementById('jamOperasional').innerHTML = `
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <div class="form-group">
            <label for="JAM_BUKA">Jam Masuk:</label>
            <input type="text" class="form-control" id="JAM_BUKA" name="JAM_BUKA" value="<?php echo $jam_masuk; ?>" required>
          </div>
          <div class="form-group">
            <label for="JAM_TUTUP">Jam Tutup:</label>
            <input type="text" class="form-control" id="JAM_TUTUP" name="JAM_TUTUP" value="<?php echo $jam_tutup; ?>" required>
          </div>
          <div class="form-group">
              <label for="JUMLAH_KUOTA">Jumlah Kuota : </label>
              <input type="text" class="form-control" id="JUMLAH_KUOTA" name="JUMLAH_KUOTA" value="<?php echo $jumlah_kuota; ?>" required>
            </div>
            <div class="form-group">
              <label for="HARGA_TIKET_WEEKEND">Harga Tiket (sabtu-minggu) : </label>
              <input type="text" class="form-control" id="HARGA_TIKET_WEEKEND" name="HARGA_TIKET_WEEKEND" value="<?php echo $tiket_weekend; ?>" required>
            </div>
            <div class="form-group">
              <label for="HARGA_TIKET_WEEKDAY">Harga Tiket (senin-jumat) : </label>
              <input type="text" class="form-control" id="HARGA_TIKET_WEEKDAY" name="HARGA_TIKET_WEEKDAY" value="<?php echo $tiket_weekday; ?>" required>
            </div>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
      `;
    });
  </script>
</body>
</html>
