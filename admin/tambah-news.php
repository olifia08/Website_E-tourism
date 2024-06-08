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

// Siapkan pernyataan SQL untuk mengambil USERNAME_ADMIN dan ID_ADMIN
$sql = "SELECT ID_ADMIN FROM data_admin WHERE USERNAME_ADMIN = ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username_login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_admin);
    if (mysqli_stmt_fetch($stmt)) {
        // Mengatur kembali sesi ID_ADMIN dengan ID yang diambil dari database
        $_SESSION['ID_ADMIN'] = $id_admin;
    } else {
        echo "Terjadi kesalahan: Data admin tidak ditemukan.";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
}

// Fungsi untuk menghasilkan ID Berita baru
function generateNewID($koneksi) {
  $sql = "SELECT ID_BERITA FROM news ORDER BY ID_BERITA DESC LIMIT 1";
  $result = mysqli_query($koneksi, $sql);
  $lastID = mysqli_fetch_assoc($result)['ID_BERITA'];

  if ($lastID) {
      $num = (int)substr($lastID, 2) + 1;
      $newID = 'BR' . str_pad($num, 3, '0', STR_PAD_LEFT);

      // Periksa apakah ID baru sudah ada di database
      while (true) {
          $checkSql = "SELECT ID_BERITA FROM news WHERE ID_BERITA = ?";
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
          $newID = 'BR' . str_pad($num, 3, '0', STR_PAD_LEFT);
      }

      return $newID;
  } else {
      return 'BR001';
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tambah-news"])) {
    $id_berita = generateNewID($koneksi);
    $judulBerita = $_POST["judulBerita"];
    $tanggalBerita = $_POST["tanggalBerita"];
    $penulisBerita = $_POST["penulisBerita"];
    $linkBerita = $_POST["linkBerita"];
    $id_admin = $_SESSION['ID_ADMIN'];

    if (!empty($_FILES["gambarBerita"]["name"])) {
        $gambarBerita = $_FILES["gambarBerita"]["name"];
        $temp_file = $_FILES["gambarBerita"]["tmp_name"];
        $target_directory = "../images/";

        $file_type = strtolower(pathinfo($target_directory . $gambarBerita, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png");
        if (!in_array($file_type, $allowed_types)) {
            echo "Error: Jenis file yang diunggah tidak didukung.";
            exit();
        }

        $file_size = $_FILES["gambarBerita"]["size"];
        $max_size = 3 * 1024 * 1024;
        if ($file_size > $max_size) {
            echo "Error: Ukuran file terlalu besar. Maksimal 3MB.";
            exit();
        }

        if (move_uploaded_file($temp_file, $target_directory . $gambarBerita)) {
            $sql = "INSERT INTO news (ID_BERITA, ID_ADMIN, JUDUL_BERITA, tanggal_berita, PENULIS_BERITA, gambar_berita, LINK_BERITA) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssss", $id_berita, $id_admin, $judulBerita, $tanggalBerita, $penulisBerita, $gambarBerita, $linkBerita);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: news-admin.php");
                    exit();
                } else {
                    echo "Terjadi kesalahan saat memperbarui data berita: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Terjadi kesalahan saat mempersiapkan pernyataan SQL: " . mysqli_error($koneksi);
            }
        } else {
            echo "Error: Gagal mengunggah gambar.";
            exit();
        }
    } else {
        $sql = "INSERT INTO news (ID_BERITA, ID_ADMIN, JUDUL_BERITA, tanggal_berita, PENULIS_BERITA, LINK_BERITA) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $id_berita, $id_admin, $judulBerita, $tanggalBerita, $penulisBerita, $linkBerita);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: news-admin.php");
                exit();
            } else {
                echo "Terjadi kesalahan saat memperbarui data berita: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Terjadi kesalahan saat mempersiapkan pernyataan SQL: " . mysqli_error($koneksi);
        }
    }
}

mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Tambah Berita</title>
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
      color: #c0392b; /* Red color */
    }

    .nav-link .fas {
      margin-right: 8px;
    }

    .nav-link:hover {
      color: #e74c3c; /* Lighter red on hover */
    }

    .nav-link.active {
      color: #e74c3c; /* Red for active link */
    }

    .navbar-brand {
      color: #c0392b !important; /* Red for brand */
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
              <a class="nav-link active" href="news-admin.php">
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
              <a class="nav-link" href="faq-admin.php">
                <i class="fas fa-question-circle"></i> FAQ
              </a>
            </li>
          </ul>
        </div>
      </nav>
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div id="newsAdmin" class="pt-3">
          <h2>Tambah Berita</h2>
          <form method="POST" action="tambah-news.php" enctype="multipart/form-data">
              <div class="form-group">
                  <label for="judulBerita">Judul Berita</label>
                  <input type="text" class="form-control" id="judulBerita" name="judulBerita" placeholder="Masukkan Judul Berita" required>
              </div>
              <div class="form-group">
                  <label for="tanggalBerita">Tanggal</label>
                  <input type="date" class="form-control" id="tanggalBerita" name="tanggalBerita" required>
              </div>
              <div class="form-group">
                  <label for="penulisBerita">Penulis (*cth: nama - sumber berita)</label>
                  <input type="text" class="form-control" id="penulisBerita" name="penulisBerita" placeholder="Masukkan Nama Penulis" required>
              </div>
              <div class="form-group">
                  <label for="linkBerita">Link</label>
                  <input type="text" class="form-control" id="linkBerita" name="linkBerita" placeholder="Masukkan Link Berita" required>
              </div>
              <div class="form-group">
                  <label for="gambarBerita">Gambar Berita</label>
                  <input type="file" class="form-control-file" id="gambarBerita" name="gambarBerita" accept="image/*">
              </div>
              <div class="form-group">
                  <input type="submit" name="tambah-news" class="btn btn-primary" value="Simpan">
                  <a href="news-admin.php" class="btn btn-secondary">Kembali</a>
              </div>
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