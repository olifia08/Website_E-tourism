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

// Menangani permintaan POST untuk memperbarui berita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update-news"])) {
    $id_berita = $_POST["id"];
    $judul = $_POST["judul"];
    $tanggal = $_POST["tanggal"];
    $penulis = $_POST["penulis"];
    $link = $_POST["link"];

    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == UPLOAD_ERR_OK) {
        $gambar = $_FILES["gambar"]["name"];
        $temp_file = $_FILES["gambar"]["tmp_name"];
        $target_directory = "../images/";
        $target_file = $target_directory . basename($gambar);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png");

        if (!in_array($file_type, $allowed_types)) {
            echo "Error: Jenis file yang diunggah tidak didukung.";
            exit();
        }

        $file_size = $_FILES["gambar"]["size"];
        $max_size = 3 * 1024 * 1024; // 3MB
        if ($file_size > $max_size) {
            echo "Error: Ukuran file terlalu besar. Maksimal 3MB.";
            exit();
        }

        if (move_uploaded_file($temp_file, $target_file)) {
            // Hapus gambar lama jika ada
            $sql_get_old_image = "SELECT gambar_berita FROM news WHERE id_berita = ?";
            $stmt_get_old_image = mysqli_prepare($koneksi, $sql_get_old_image);
            mysqli_stmt_bind_param($stmt_get_old_image, "s", $id_berita);
            mysqli_stmt_execute($stmt_get_old_image);
            mysqli_stmt_bind_result($stmt_get_old_image, $old_image);
            mysqli_stmt_fetch($stmt_get_old_image);
            mysqli_stmt_close($stmt_get_old_image);

            if ($old_image && file_exists($target_directory . $old_image)) {
                unlink($target_directory . $old_image);
            }

            $sql_update = "UPDATE news SET JUDUL_BERITA = ?, tanggal_berita = ?, PENULIS_BERITA = ?, gambar_berita = ?, LINK_BERITA = ? WHERE id_berita = ?";
            $stmt_update = mysqli_prepare($koneksi, $sql_update);
            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "ssssss", $judul, $tanggal, $penulis, $gambar, $link, $id_berita);
                if (mysqli_stmt_execute($stmt_update)) {
                    header("Location: news-admin.php");
                    exit();
                } else {
                    echo "Terjadi kesalahan saat memperbarui data berita: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt_update);
            } else {
                echo "Terjadi kesalahan saat mempersiapkan pernyataan SQL: " . mysqli_error($koneksi);
            }
        } else {
            echo "Error: Gagal mengunggah gambar.";
            exit();
        }
    } else {
        // Jika tidak ada gambar yang dipilih atau ada error, lakukan pembaruan data tanpa mengubah gambar
        $sql_update = "UPDATE news SET JUDUL_BERITA = ?, tanggal_berita = ?, PENULIS_BERITA = ?, LINK_BERITA = ? WHERE id_berita = ?";
        $stmt_update = mysqli_prepare($koneksi, $sql_update);
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sssss", $judul, $tanggal, $penulis, $link, $id_berita);
            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: news-admin.php");
                exit();
            } else {
                echo "Terjadi kesalahan saat memperbarui data berita: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            echo "Terjadi kesalahan saat mempersiapkan pernyataan SQL: " . mysqli_error($koneksi);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Update Berita</title>
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
                <h2>Update Berita</h2>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
                    $ID_BERITA = $_GET["id"];

                    // Query untuk mendapatkan data news berdasarkan ID
                    $sql = "SELECT * FROM news WHERE ID_BERITA = ?";
                    $stmt = mysqli_prepare($koneksi, $sql);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "s", $ID_BERITA);
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            if ($row = mysqli_fetch_assoc($result)) {
                ?>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['ID_BERITA']; ?>">
                    <div class="form-group">
                        <label for="judul">Judul Berita :</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($row['JUDUL_BERITA'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal">Tanggal Berita :</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $row['tanggal_berita']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="penulis">Penulis Berita : (*cth: nama - sumber berita)</label>
                        <input type="text" class="form-control" id="penulis" name="penulis" value="<?php echo htmlspecialchars($row['PENULIS_BERITA'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="link">Link Berita :</label>
                        <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($row['LINK_BERITA'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar Berita :</label>
                        <input type="file" class="form-control-file" id="gambar" name="gambar">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="update-news" value="Simpan">
                        <a class="btn btn-danger" href="news-admin.php">Batal</a>
                    </div>
                </form>
                <?php
                            } else {
                                echo "Data Berita tidak ditemukan.";
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
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>