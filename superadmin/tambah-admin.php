<?php
session_start();
include '../connecting.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai-nilai dari formulir
    $adminNama = $_POST["adminNama"];
    $adminNomorTelepon = $_POST["adminNomorTelepon"];
    $adminAlamat = $_POST["adminAlamat"];
    $adminJenisKelamin = $_POST["adminJenisKelamin"];
    $adminEmail = $_POST["adminEmail"];
    $adminUsername = $_POST["adminUsername"];
    $adminPassword = md5($_POST["adminPassword"]); // Ubah password menjadi hash MD5

    // Ambil ID Superadmin dari sesi atau data pengguna yang login
    $idSuperadmin = $_SESSION['ID_SUPERADMIN'];

    // Buat query untuk mengambil jumlah admin yang sudah ada
    $query = "SELECT COUNT(*) AS total_admin FROM data_admin";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        // Ambil hasil query
        $row = mysqli_fetch_assoc($result);
        $totalAdmin = $row['total_admin'];

        // Hitung ID_ADMIN baru
        $newAdminId = $totalAdmin + 1;

        // Format ID_ADMIN menjadi dua digit dengan LPAD
        $formattedAdminId = str_pad($newAdminId, 2, '0', STR_PAD_LEFT);

        // Tentukan ID_DATA_ADMIN dengan format SKPL-DA
        $idDataAdmin = 'SKPL-DA' . $formattedAdminId;

        // Tentukan ID_ADMIN dengan format SKPL-A
        $idAdmin = 'SKPL-A' . $formattedAdminId;

        // Insert into the admin table first
        $queryInsertAdmin = "INSERT INTO admin (ID_ADMIN) VALUES ('$idAdmin')";
        
        if (mysqli_query($koneksi, $queryInsertAdmin)) {
            // Buat query untuk memasukkan data admin ke dalam database
            $queryInsert = "INSERT INTO data_admin (ID_DATA_ADMIN, ID_SUPERADMIN, ID_ADMIN, NAMA_ADMIN, NOMER_TELPON_ADMIN, ALAMAT_ADMIN, JENIS_KELAMIN_ADMIN, EMAIL_ADMIN, USERNAME_ADMIN, PASSWORD_ADMIN) VALUES ('$idDataAdmin', '$idSuperadmin', '$idAdmin', '$adminNama', '$adminNomorTelepon', '$adminAlamat', '$adminJenisKelamin', '$adminEmail', '$adminUsername', '$adminPassword')";

            // Jalankan query insert
            if (mysqli_query($koneksi, $queryInsert)) {
                // Redirect ke halaman admin-management.php jika berhasil
                header("Location: admin-management.php");
                exit();
            } else {
                // Tampilkan pesan kesalahan jika query gagal
                echo "Error: " . $queryInsert . "<br>" . mysqli_error($koneksi);
            }
        } else {
            // Tampilkan pesan kesalahan jika query gagal
            echo "Error: " . $queryInsertAdmin . "<br>" . mysqli_error($koneksi);
        }
    } else {
        // Tampilkan pesan kesalahan jika query tidak berhasil
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Admin</title>
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
<body><nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
        <a class="nav-link" href="#"><i class="fas fa-user-circle"></i><?php echo $_SESSION['USERNAME_SUPERADMIN']; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
              <a class="nav-link active" href="admin-management.php">
                <i class="fas fa-users"></i> Admin Management
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="reporting.php">
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
      <section class="ftco-section contact-section ftco-degree-bg">
        <div class="container">
            <div class="row block-9">
                <div class="col-md-12">
                <h2>Add Admin</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="adminNama">Nama Admin</label>
                    <input type="text" class="form-control" id="adminNama" name="adminNama" required>
                  </div>
                  <div class="form-group">
                    <label for="adminNomorTelepon">Nomor Telepon Admin</label>
                    <input type="text" class="form-control" id="adminNomorTelepon" name="adminNomorTelepon" required>
                  </div>
                  <div class="form-group">
                    <label for="adminAlamat">Alamat Admin</label>
                    <input type="text" class="form-control" id="adminAlamat" name="adminAlamat" required>
                  </div>
                  <div class="form-group">
                    <label for="adminJenisKelamin">Jenis Kelamin Admin</label>
                    <select class="form-control" id="adminJenisKelamin" name="adminJenisKelamin" required>
                      <option value="Laki-laki">Laki-laki</option>
                      <option value="Perempuan">Perempuan</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="adminEmail">Email Admin</label>
                    <input type="email" class="form-control" id="adminEmail" name="adminEmail" required>
                  </div>
                  <div class="form-group">
                    <label for="adminUsername">Username Admin</label>
                    <input type="text" class="form-control" id="adminUsername" name="adminUsername" required>
                  </div>
                  <div class="form-group">
                    <label for="adminPassword">Password Admin</label>
                    <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Tambah Admin</button>
                  <a href="admin-management.php" class="btn btn-secondary">Kembali</a>
                </form>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
