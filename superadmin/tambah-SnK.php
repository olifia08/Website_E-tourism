<?php
session_start();
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_SUPERADMIN'])) {
    header("Location: superadmin-login.php");
    exit();
}

// Mendapatkan ID_SK terbaru dari database dan menghitung ID_SK baru
function generateID_SK($koneksi) {
    $sql = "SELECT ID_SK FROM s_k ORDER BY ID_SK DESC LIMIT 1";
    $result = mysqli_query($koneksi, $sql);
    $lastID = "SK"; // Default value if no ID is found
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastID = $row['ID_SK'];
    }

    $lastNumber = intval(substr($lastID, 2));
    $newNumber = $lastNumber + 1;
    return 'SK' . $newNumber;
}


// Memproses form ketika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_sk = generateID_SK($koneksi);
    $waktu_sk = $_POST['waktu_sk'];
    $poin_sk = $_POST['poin_sk'];

    // Menyimpan data ke database
    $sql = "INSERT INTO s_k (ID_SK, WAKTU_SK, POIN_SK) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $id_sk, $waktu_sk, $poin_sk);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: setting-SnK.php");
            exit();
        } else {
            $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
    mysqli_close($koneksi);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah S&K</title>
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
            <h2>Tambah S&K Baru</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="form-group">
        <label for="waktu_sk">Waktu S&K</label>
        <input type="text" class="form-control" id="waktu_sk" name="waktu_sk" readonly required>
      </div>
      <div class="form-group">
        <label for="poin_sk">Poin S&K</label>
        <input type="text" class="form-control" id="poin_sk" name="poin_sk" required>
      </div>
      <button type="submit" class="btn btn-primary">Tambah</button>
      <a href="setting-SnK.php" class="btn btn-secondary">Kembali</a>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    function setCurrentTime() {
      var now = new Date();
      var formattedTime = now.getFullYear() + '-' +
                          ('0' + (now.getMonth() + 1)).slice(-2) + '-' +
                          ('0' + now.getDate()).slice(-2) + ' ' +
                          ('0' + now.getHours()).slice(-2) + ':' +
                          ('0' + now.getMinutes()).slice(-2) + ':' +
                          ('0' + now.getSeconds()).slice(-2);
      document.getElementById('waktu_sk').value = formattedTime;
    }

    // Set current time on page load
    window.onload = setCurrentTime;
  </script>
</body>
</html>
