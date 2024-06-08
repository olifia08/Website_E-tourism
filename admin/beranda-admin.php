<?php
session_start();
include '../connecting.php';

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
        <canvas id="visitorChart" width="400" height="200"></canvas>
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var ctx = document.getElementById('visitorChart').getContext('2d');
      var visitorChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [],
          datasets: [
            {
              label: 'Wisnu',
              data: [],
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 1
            },
            {
              label: 'Wisman',
              data: [],
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
            },
            {
              label: 'Total',
              data: [],
              backgroundColor: 'rgba(255, 206, 86, 0.2)',
              borderColor: 'rgba(255, 206, 86, 1)',
              borderWidth: 1
            }
          ]
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
          url: '../superadmin/back-beranda-superadmin.php',
          method: 'GET',
          success: function(data) {
            console.log(data); // Tambahkan log ini untuk melihat data yang diterima
            var chartData = JSON.parse(data);
            visitorChart.data.labels = chartData.months;
            visitorChart.data.datasets[0].data = chartData.wisnu;
            visitorChart.data.datasets[1].data = chartData.wisman;
            visitorChart.data.datasets[2].data = chartData.total;
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
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
