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
  <title>Admin Dashboard</title>
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
    th {
      text-align:center;
    }
    img {
      width: 100px;
      height: auto;
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
              <a class="nav-link active" href="validasi-ticket.php">
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
              <a class="nav-link" href="faq-admin.php">
                <i class="fas fa-question-circle"></i> FAQ
              </a>
            </li>
          </ul>
        </div>
      </nav>
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div id="validasiTicket" class="pt-3">
          <h2>Validasi Ticket</h2>
          <!-- Form pencarian -->
          <form method="GET" action="">
            <div class="form-group">
              <label for="searchName">Cari Berdasarkan Nama:</label>
              <input type="text" class="form-control" id="searchName" name="searchName">
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
          </form>
          <br>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID Transaksi</th>
                <th>Waktu Transaksi</th>
                <th>Waktu Booking</th>
                <th>Nama</th>
                <th>Jumlah Tiket</th>
                <th>Total Harga</th>
                <th>Bukti Pembayaran</th>
                <th colspan='2'>Validasi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              include '../connecting.php';

              // Pagination
              $results_per_page = 10;
              $page = isset($_GET['page']) ? $_GET['page'] : 1;
              $start_from = ($page - 1) * $results_per_page;

              // Menyiapkan query pencarian
              $searchName = isset($_GET['searchName']) ? $_GET['searchName'] : '';
              $query = "SELECT * FROM transaksi WHERE NAMA_PEMESAN LIKE '%$searchName%' ORDER BY ID_TRANSAKSI DESC LIMIT $start_from, $results_per_page";

              // Menjalankan query
              $result = mysqli_query($koneksi, $query);

              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>";
                      echo "<td>" . $row['ID_TRANSAKSI'] . "</td>";
                      echo "<td>" . $row['WAKTU_TRANSAKSI'] . "</td>";
                      echo "<td>";
                      echo $row['WAKTU_BOOKING'] != '0000-00-00' ? $row['WAKTU_BOOKING'] : "Belum ada waktu booking";
                      echo "</td>";
                      echo "<td>" . $row['NAMA_PEMESAN'] . "</td>";
                      echo "<td>" . $row['JUMLAH_PEMESAN'] . "</td>";
                      echo "<td>" . $row['TOTAL_HARGA'] . "</td>";
                      echo "<td>";
                      if (!empty($row['BUKTI_PEMBAYARAN'])) {
                          $imagePath = '../images/BUKTI_PEMBAYARAN/' . $row['BUKTI_PEMBAYARAN']; // Menambahkan garis miring "/" setelah direktori
                          echo "<img src='" . $imagePath . "' alt='Bukti Pembayaran' style='max-width: 100px; max-height: 100px;'>";
                      } else {
                          echo "Tidak ada bukti pembayaran";
                      }
                      echo "</td>";                      
                    echo "</td>";
                      echo "</td>";
                      if ($row['VALIDASI'] === 'berhasil') {
                          echo '<td colspan="2"><button class="btn btn-success" disabled>Validasi Berhasil</button></td>';
                      } elseif ($row['VALIDASI'] === 'gagal') {
                          echo '<td colspan="2"><button class="btn btn-danger" disabled>Validasi Gagal</button></td>';
                      } else {
                          echo '<td>';
                          echo '<button class="btn btn-info validate-btn" data-id="' . $row['ID_TRANSAKSI'] . '">Validasi</button>';
                          echo '</td>';
                          echo '<form method="post" action="batal_validasi.php">';
                          echo '<input type="hidden" name="id" value="' . $row['ID_TRANSAKSI'] . '">';
                          echo '<td><button type="submit" class="btn btn-danger">Batal</button></td>';
                          echo '</form>';
                          echo '</tr>';
                        }
                    }} else {
                        echo "<tr><td colspan='8'>Tidak ada data menu.</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
                  
                <!-- Pagination links -->
                <?php
                $sql = "SELECT COUNT(ID_TRANSAKSI) AS total FROM transaksi";
                $result = mysqli_query($koneksi, $sql);
                $row = mysqli_fetch_assoc($result);
                $total_pages = ceil($row["total"] / $results_per_page);
                  
                echo "<ul class='pagination'>";
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item'><a class='page-link' href='validasi-ticket.php?page=" . $i . "'>" . $i . "</a></li>";
                }
                echo "</ul>";
                mysqli_close($koneksi);
                ?>
              </div>
            </main>
          </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
          $(document).ready(function() {
            $('.validate-btn').on('click', function() {
              var idTransaksi = $(this).data('id');
              var row = $(this).closest('tr');
              if (confirm("Apakah Anda yakin memvalidasi transaksi ini?")) {
                $.ajax({
                  url: 'validasi.php',
                  type: 'POST',
                  data: { id: idTransaksi },
                  success: function(response) {
                    if (response === 'success') {
                      alert('Transaksi berhasil divalidasi.');
                      row.find('td').eq(6).html('<button class="btn btn-success" disabled>Validasi Berhasil</button>');
                      row.find('td').eq(7).html('');
                    } else {
                      alert('Terjadi kesalahan, silakan coba lagi.');
                    }
                  }
                });
              }
            });
          });
        </script>
      </body>
      </html>
      