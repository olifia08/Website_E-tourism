<?php
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
session_start();
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
  <title>Superadmin Dashboard - S&K</title>
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
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

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
            <a class="nav-link" href="reporting.php">
              <i class="fas fa-chart-bar"></i> Reporting
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="setting-SnK.php">
              <i class="fas fa-cogs"></i> Settings S&K
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
      <div id="faqAdmin" class="pt-3">
        <h2>S&K Superadmin</h2>
        <a href="tambah-SnK.php" class="btn btn-primary mb-3">Tambah S&K</a>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID_SK</th>
              <th>WAKTU_SK</th>
              <th>POIN_SK</th>
              <th colspan='2'>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            include '../connecting.php';
            $result = mysqli_query($koneksi, "SELECT * FROM s_k");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['ID_SK'] . "</td>";
                    echo "<td>" . $row['WAKTU_SK'] . "</td>";
                    echo "<td>" . $row['POIN_SK'] . "</td>"; // Menampilkan data POIN_SK tanpa pengecekan
                    echo '<td>';
                    echo '<a href="edit-SnK.php?id=' . $row['ID_SK'] . '" class="btn btn-info">Edit</a>';
                    echo '</td><td>';
                    echo "<a href='hapus-SnK.php?id=" . $row['ID_SK'] . "' class='btn btn-danger' onclick='return confirm(\"Apakah Anda yakin ingin menghapus S&K ini?\")'>Hapus</a>"; // Tombol Hapus
                }
            } else {
                echo "<tr><td colspan='5'>Tidak ada data S&K.</td></tr>";
            }
            mysqli_close($koneksi);
            ?>
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

