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

// Fungsi untuk menghasilkan ID Berita baru
function generateNewID($koneksi) {
  $sql = "SELECT ID_FQ FROM f_q ORDER BY ID_FQ DESC LIMIT 1";
  $result = mysqli_query($koneksi, $sql);
  $lastID = mysqli_fetch_assoc($result)['ID_FQ'];

  if ($lastID) {
      $num = (int)substr($lastID, 2) + 1;
      $newID = 'FQ' . str_pad($num, 3, '0', STR_PAD_LEFT);

      // Periksa apakah ID baru sudah ada di database
      while (true) {
          $checkSql = "SELECT ID_FQ FROM f_q WHERE ID_FQ = ?";
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
          $newID = 'FQ' . str_pad($num, 3, '0', STR_PAD_LEFT);
      }

      return $newID;
  } else {
      return 'FQ001';
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tambah-faq"])) {
    $id_fq = generateNewID($koneksi);
    $id_admin = $_SESSION['ID_ADMIN'];
    $waktu_fq = date('Y-m-d');
    $pertanyaan = htmlspecialchars($_POST['PERTANYAAN_FQ']);
    $jawaban = htmlspecialchars($_POST['JAWABAN_FQ']);

    $sql = 'INSERT INTO f_q (ID_FQ, ID_ADMIN, WAKTU_FQ, PERTANYAAN_FQ, JAWABAN_FQ) VALUES (?, ?, ?, ?, ?)';
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $id_fq, $id_admin, $waktu_fq, $pertanyaan, $jawaban);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($koneksi);
            header("Location: faq-admin.php");
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
  <title>Tambah FAQ - Admin Dashboard</title>
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
              <a class="nav-link" href="informasi_wisata.php">
                <i class="bi bi-info-circle-fill"></i> Informasi Wisata
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="event-admin.php">
                <i class="fas fa-calendar-alt"></i> Event
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="faq-admin.php">
                <i class="fas fa-question-circle"></i> FAQ
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div id="tambahFaq" class="pt-3">
          <h2>Tambah FAQ</h2>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="tambah-faq" value="true">
            <div class="form-group">
              <label for="PERTANYAAN_FQ">Pertanyaan</label>
              <input type="text" class="form-control" id="PERTANYAAN_FQ" name="PERTANYAAN_FQ" placeholder="Masukkan pertanyaan" required>
            </div>
            <div class="form-group">
              <label for="JAWABAN_FQ">Jawaban</label>
              <textarea class="form-control" id="JAWABAN_FQ" name="JAWABAN_FQ" rows="4" placeholder="Masukkan jawaban" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="faq-admin.php" class="btn btn-secondary">Batal</a>
          </form>
        </div>
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
