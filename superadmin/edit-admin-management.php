<?php
session_start(); // Mulai sesi

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

// Proses penyimpanan data setelah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit-admin-management"])) {
    $ID_DATA_ADMIN = $_POST['id']; // Menyimpan ID_DATA_ADMIN dari formulir
    $nama_admin = $_POST['nama_admin'];
    $email_admin = $_POST['email_admin'];
    $nomor_telepon_admin = $_POST['nomor_telepon_admin'];
    $alamat_admin = $_POST['alamat_admin'];
    $jenis_kelamin_admin = $_POST['jenis_kelamin_admin'];
    $username_admin = $_POST['username_admin'];
    
    // Cek apakah password baru diisi
    if(!empty($_POST['password_admin'])) {
        $password_admin = md5($_POST['password_admin']); // Enkripsi password dengan MD5
        // Query untuk update data admin ke database dengan password baru
        $sql_update = "UPDATE data_admin SET NAMA_ADMIN=?, EMAIL_ADMIN=?, NOMER_TELPON_ADMIN=?, ALAMAT_ADMIN=?, JENIS_KELAMIN_ADMIN=?, USERNAME_ADMIN=?, PASSWORD_ADMIN=? WHERE ID_DATA_ADMIN=?";
        $stmt_update = mysqli_prepare($koneksi, $sql_update);
        if ($stmt_update) {
            // Bind parameter
            mysqli_stmt_bind_param($stmt_update, "ssssssss", $nama_admin, $email_admin, $nomor_telepon_admin, $alamat_admin, $jenis_kelamin_admin, $username_admin, $password_admin, $ID_DATA_ADMIN); 
            // Eksekusi statement
            if(mysqli_stmt_execute($stmt_update)) {
                echo "<div class='alert alert-success' role='alert'>Data admin berhasil diperbarui.</div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>Gagal memperbarui data admin.</div>";
            }
            // Tutup statement
            mysqli_stmt_close($stmt_update);
        } else {
            // Tampilkan pesan kesalahan jika terjadi kesalahan dalam persiapan statement SQL
            echo "Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi);
        }
    } else {
        // Jika password tidak diubah, gunakan password lama
        $password_admin = $adminData['PASSWORD_ADMIN'];
        // Query untuk update data admin ke database tanpa mengubah password
        $sql_update = "UPDATE data_admin SET NAMA_ADMIN=?, EMAIL_ADMIN=?, NOMER_TELPON_ADMIN=?, ALAMAT_ADMIN=?, JENIS_KELAMIN_ADMIN=?, USERNAME_ADMIN=? WHERE ID_DATA_ADMIN=?";
        $stmt_update = mysqli_prepare($koneksi, $sql_update);
        if ($stmt_update) {
            // Bind parameter
            mysqli_stmt_bind_param($stmt_update, "sssssss", $nama_admin, $email_admin, $nomor_telepon_admin, $alamat_admin, $jenis_kelamin_admin, $username_admin, $ID_DATA_ADMIN); 
            // Eksekusi statement
            if(mysqli_stmt_execute($stmt_update)) {
                echo "<div class='alert alert-success' role='alert'>Data admin berhasil diperbarui.</div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>Gagal memperbarui data admin.</div>";
            }
            // Tutup statement
            mysqli_stmt_close($stmt_update);
        } else {
            // Tampilkan pesan kesalahan jika terjadi kesalahan dalam persiapan statement SQL
            echo "Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                            <h2>Edit Data Admin</h2>
                            <?php
                            if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
                                $ID_DATA_ADMIN = $_GET["id"];
                                $sql = "SELECT * FROM data_admin WHERE ID_DATA_ADMIN = ?";
                                $stmt = mysqli_prepare($koneksi, $sql);
                                if ($stmt) {
                                    mysqli_stmt_bind_param($stmt, "s", $ID_DATA_ADMIN);
                                    if (mysqli_stmt_execute($stmt)) {
                                        $result = mysqli_stmt_get_result($stmt);
                                        if ($adminData = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $adminData['ID_DATA_ADMIN']; ?>">
                                                <div class="form-group">
                                                    <label for="nama_admin">Nama Admin:</label>
                                                    <input type="text" class="form-control" id="nama_admin" name="nama_admin" value="<?php echo $adminData['NAMA_ADMIN']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email_admin">Email Admin:</label>
                                                    <input type="email" class="form-control" id="email_admin" name="email_admin" value="<?php echo $adminData['EMAIL_ADMIN']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="nomor_telepon_admin">Nomor Telepon Admin:</label>
                                                    <input type="text" class="form-control" id="nomor_telepon_admin" name="nomor_telepon_admin" value="<?php echo $adminData['NOMER_TELPON_ADMIN']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="alamat_admin">Alamat Admin:</label>
                                                    <textarea class="form-control" id="alamat_admin" name="alamat_admin"><?php echo $adminData['ALAMAT_ADMIN']; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="jenis_kelamin_admin">Jenis Kelamin Admin:</label>
                                                    <select class="form-control" id="jenis_kelamin_admin" name="jenis_kelamin_admin">
                                                        <option value="L" <?php if ($adminData['JENIS_KELAMIN_ADMIN'] == 'L') echo 'selected'; ?>>Laki-laki</option>
                                                        <option value="P" <?php if ($adminData['JENIS_KELAMIN_ADMIN'] == 'P') echo 'selected'; ?>>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="username_admin">Username Admin:</label>
                                                    <input type="text" class="form-control" id="username_admin" name="username_admin" value="<?php echo $adminData['USERNAME_ADMIN']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="password_admin">Password Baru Admin:</label>
                                                    <input type="password" class="form-control" id="password_admin" name="password_admin">
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="edit-admin-management">Simpan Perubahan</button>
                                                <a href="admin-management.php" class="btn btn-secondary">Batal</a>
                                            </form>
                                            <?php
                                        } else {
                                            echo "Data admin tidak ditemukan.";
                                        }
                                    } else {
                                        echo "Terjadi kesalahan: " . mysqli_error($koneksi);
                                    }
                                    mysqli_stmt_close($stmt);
                                } else {
                                    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
                                }                                
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>
</body>
</html>
