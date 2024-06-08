<?php
session_start(); // Start the session

include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_SUPERADMIN'])) {
    header("Location: superadmin-login.php");
    exit();
}

// Mendapatkan username dari sesi
$username_login = $_SESSION['USERNAME_SUPERADMIN'];

// Siapkan pernyataan SQL untuk mengambil USERNAME_SUPERADMIN
$sql = "SELECT USERNAME_SUPERADMIN FROM superadmin WHERE USERNAME_SUPERADMIN = ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username_login);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nama);
    if (mysqli_stmt_fetch($stmt)) {
        // Mengatur kembali sesi USERNAME_SUPERADMIN dengan nama yang diambil dari database
        $_SESSION['USERNAME_SUPERADMIN'] = $nama;
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
  <title>Super Admin Dashboard</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fas fa-user-circle"></i><?php echo $_SESSION['USERNAME_SUPERADMIN']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout-superadmin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
              <a class="nav-link" href="beranda-superadmin.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="admin-management.php">
                <i class="fas fa-users"></i> Admin Management
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="reporting.php">
                <i class="fas fa-chart-bar"></i> Reporting
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="setting-SnK.php">
                <i class="fas fa-cogs"></i> Settings S&K 
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div id="reporting" class="pt-3">
          <h2>Reporting</h2>
          <div class="d-flex justify-content-end mb-3">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
              <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
              <label class="btn btn-outline-primary" for="btnradio1" onclick="loadData('month')">Bulan</label>
              <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
              <label class="btn btn-outline-primary" for="btnradio3" onclick="loadData('year')">Tahun</label>
            </div>
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th id="table-tahun">Tahun</th>
                <th id="table-bulan" style="display:none;">Bulan</th>
                <th>Wisnu</th>
                <th>Wisman</th>
                <th>Qty penjualan tiket</th>
                <th>Total penjualan tiket (Rp)</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="reporting-tbody">
              <!-- Data will be loaded here using JavaScript -->
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    function loadData(type) {
      $.ajax({
        url: 'reporting_data.php',
        type: 'GET',
        data: { type: type },
        success: function(response) {
          const data = JSON.parse(response);
          let tbody = '';
          if (data.length > 0) {
            data.forEach((row, index) => {
              tbody += `
                <tr>
                  <td>${index + 1}</td>
                  <td>${row.tahun}</td>
                  ${type === 'month' ? `<td>${row.bulan}</td>` : '<td style="display:none;"></td>'}
                  <td>${row.total_wisnu}</td>
                  <td>${row.total_wisman}</td>
                  <td>${row.total_penjualan_tiket}</td>
                  <td>${row.total_pemasukan_penjualan}</td>
                  <td>
                    ${type === 'year' ? 
                      `<a href="detail-reportingTahun.php?tahun=${row.tahun}" class="btn btn-success" target="_blank">Cetak Transaksi</a>` :
                      `<a href="detail-reporting.php?tahun=${row.tahun}&bulan=${row.bulan}" class="btn btn-success" target="_blank">Cetak Transaksi</a>`}
                  </td>
                </tr>
              `;
            });
          } else {
            tbody = '<tr><td colspan="8">Tidak ada data transaksi.</td></tr>';
          }
          $('#reporting-tbody').html(tbody);
          if (type === 'year') {
            $('#table-bulan').hide();
          } else {
            $('#table-bulan').show();
          }
        },
        error: function() {
          alert('Error fetching data');
        }
      });
    }

    // Load month data by default on page load
    loadData('month');
  </script>
</body>
</html>
