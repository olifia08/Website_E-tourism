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

mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - FAQ</title>
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
    th{
      text-align:center;
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
        <div id="faqAdmin" class="pt-3">
          <h2>FAQ Admin</h2>
          <a href="tambah-faq.php" class="btn btn-primary mb-3">Tambah FAQ</a>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Pertanyaan</th>
                <th>Jawaban</th>
                <th colspan='2'>Aksi</th>
              </tr>
            </thead>
            <tbody>
            <?php
                include '../connecting.php';
                $result = mysqli_query($koneksi, "SELECT * FROM f_q");
                if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['PERTANYAAN_FQ'] . "</td>";
                echo "<td>" . $row['JAWABAN_FQ'] . "</td>";
                echo '<td>';
                echo '<a href="edit-faq.php?id=' . $row['ID_FQ'] . '" class="btn btn-info">Edit</a>';
                echo '<form method="post" action="hapus-faq.php">';
                echo '<input type="hidden" name="id" value="' . $row['ID_FQ'] . '">';
                echo '</td><td><button type="submit" class="btn btn-danger">Hapus</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
                }
                } else {
                echo "<tr><td colspan='5'>Tidak ada data berita.</td></tr>";
                }
                mysqli_close($koneksi);
                ?>
              <!-- Tambahkan baris FAQ lainnya sesuai kebutuhan -->
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
