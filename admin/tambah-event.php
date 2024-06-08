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
$sql = "SELECT ID_ADMIN FROM data_admin WHERE USERNAME_ADMIN = ?";
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
  $sql = "SELECT ID_EVENT FROM event ORDER BY ID_EVENT DESC LIMIT 1";
  $result = mysqli_query($koneksi, $sql);
  $lastID = mysqli_fetch_assoc($result)['ID_EVENT'];

  if ($lastID) {
      $num = (int)substr($lastID, 2) + 1;
      $newID = 'EV' . str_pad($num, 3, '0', STR_PAD_LEFT);

      // Periksa apakah ID baru sudah ada di database
      while (true) {
          $checkSql = "SELECT ID_EVENT FROM event WHERE ID_EVENT = ?";
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
          $newID = 'EV' . str_pad($num, 3, '0', STR_PAD_LEFT);
      }

      return $newID;
  } else {
      return 'EV001';
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tambah-event"])) {
    $id_event = generateNewID($koneksi);
    $id_admin = $_SESSION['ID_ADMIN'];
    $eventName = $_POST["eventName"];
    $eventDate = $_POST["eventDate"];
    $linkevent = $_POST["linkevent"];

    if (!empty($_FILES["gambarevent"]["name"])) {
        $gambarevent = $_FILES["gambarevent"]["name"];
        $temp_file = $_FILES["gambarevent"]["tmp_name"];
        $target_directory = "../images/";

        $file_type = strtolower(pathinfo($target_directory . $gambarevent, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png");
        if (!in_array($file_type, $allowed_types)) {
            echo "Error: Jenis file yang diunggah tidak didukung.";
            exit();
        }

        $file_size = $_FILES["gambarevent"]["size"];
        $max_size = 3 * 1024 * 1024; // 3MB
        if ($file_size > $max_size) {
            echo "Error: Ukuran file terlalu besar. Maksimal 3MB.";
            exit();
        }

        if (move_uploaded_file($temp_file, $target_directory . $gambarevent)) {
            $sql = "INSERT INTO event (ID_EVENT, ID_ADMIN, JUDUL_EVENT, WAKTU_EVENT, gambar_event, LINK_EVENT) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssss", $id_event, $id_admin, $eventName, $eventDate, $gambarevent, $linkevent);
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: event-admin.php");
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
        $sql = "INSERT INTO event (ID_EVENT, ID_ADMIN, JUDUL_EVENT, WAKTU_EVENT, LINK_EVENT) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $id_event, $id_admin, $eventName, $eventDate, $linkevent);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: event-admin.php");
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
  <title>Admin Dashboard - Tambah Event</title>
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
              <a class="nav-link event" href="event-admin.php">
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
        <div id="tambahEvent" class="pt-3">
          <h2>Tambah Event</h2>
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="eventName">Nama Event</label>
              <input type="text" class="form-control" id="eventName" name="eventName" placeholder="Masukkan nama event" required>
            </div>
            <div class="form-group">
              <label for="eventDate">Tanggal Pelaksanaan</label>
              <input type="date" class="form-control" id="eventDate" name="eventDate" required>
            </div>
            <div class="form-group">
              <label for="gambarevent">Gambar</label>
              <input type="file" class="form-control-file" id="gambarevent" name="gambarevent" accept="image/*">
            </div>
            <div class="form-group">
              <label for="linkevent">Link Event</label>
              <input type="text" class="form-control" id="linkevent" name="linkevent" required>
            </div>
            <button type="submit" name="tambah-event" class="btn btn-primary">Simpan Event</button>
            <a href="event-admin.php" class="btn btn-secondary">Kembali</a>
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

