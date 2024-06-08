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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update-event"])) {
    $id_event = $_POST['id'];
    $eventName = $_POST["eventName"];
    $eventDate = $_POST["eventDate"];
    $linkevent = $_POST["linkevent"];

    if (isset($_FILES["eventImage"]) && $_FILES["eventImage"]["error"] == UPLOAD_ERR_OK) {
        $eventImage = $_FILES["eventImage"]["name"];
        $temp_file = $_FILES["eventImage"]["tmp_name"];
        $target_directory = "../images/";
        $target_file = $target_directory . basename($eventImage);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png");

        if (!in_array($file_type, $allowed_types)) {
            echo "Error: Jenis file yang diunggah tidak didukung.";
            exit();
        }

        $file_size = $_FILES["eventImage"]["size"];
        $max_size = 3 * 1024 * 1024; // 3MB
        if ($file_size > $max_size) {
            echo "Error: Ukuran file terlalu besar. Maksimal 3MB.";
            exit();
        }
        if (move_uploaded_file($temp_file, $target_file)) {
            // Hapus gambar lama jika ada
            $sql_get_old_image = "SELECT GAMBAR_EVENT FROM event WHERE ID_EVENT = ?";
            $stmt_get_old_image = mysqli_prepare($koneksi, $sql_get_old_image);
            mysqli_stmt_bind_param($stmt_get_old_image, "s", $id_event);
            mysqli_stmt_execute($stmt_get_old_image);
            mysqli_stmt_bind_result($stmt_get_old_image, $old_image);
            mysqli_stmt_fetch($stmt_get_old_image);
            mysqli_stmt_close($stmt_get_old_image);

            if ($old_image && file_exists($target_directory . $old_image)) {
                unlink($target_directory . $old_image);
            }

            $sql_update = "UPDATE event SET JUDUL_EVENT = ?, WAKTU_EVENT = ?, GAMBAR_EVENT = ?, LINK_EVENT = ? WHERE ID_EVENT = ?";
            $stmt_update = mysqli_prepare($koneksi, $sql_update);
            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "sssss", $eventName, $eventDate, $eventImage, $linkevent, $id_event);
                if (mysqli_stmt_execute($stmt_update)) {
                    header("Location: event-admin.php");
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
        $sql_update = "UPDATE event SET JUDUL_EVENT = ?, WAKTU_EVENT = ?, LINK_EVENT = ? WHERE ID_EVENT = ?";
        $stmt_update = mysqli_prepare($koneksi, $sql_update);
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "ssss", $eventName, $eventDate, $linkevent, $id_event);
            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: event-admin.php");
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
  <title>Admin Dashboard - Update Event</title>
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
                        <a class="nav-link active" href="event-admin.php">
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
        <div id="updateEvent" class="pt-3">
          <h2>Update Event</h2>
          <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
                    $ID_EVENT = $_GET["id"];

                    // Query untuk mendapatkan data event berdasarkan ID
                    $sql = "SELECT * FROM event WHERE ID_EVENT = ?";
                    $stmt = mysqli_prepare($koneksi, $sql);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "s", $ID_EVENT);
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            if ($row = mysqli_fetch_assoc($result)) {
                ?>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $row['ID_EVENT']; ?>">
                    <div class="form-group">
                        <label for="eventName">Nama Event</label>
                        <input type="text" class="form-control" id="eventName" name="eventName" value="<?php echo htmlspecialchars($row['JUDUL_EVENT'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="eventDate">Tanggal</label>
                        <input type="date" class="form-control" id="eventDate" name="eventDate" value="<?php echo $row['WAKTU_EVENT']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="eventImage">Gambar</label>
                        <input type="file" class="form-control-file" id="eventImage" name="eventImage">
                    </div>
                    <div class="form-group">
                        <label for="eventLocation">Link Event</label>
                        <input type="text" class="form-control" id="eventLocation" name="linkevent" value="<?php echo htmlspecialchars($row['LINK_EVENT'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <button type="submit" name="update-event" class="btn btn-primary">Update Event</button>
                    <a href="event-admin.php" class="btn btn-secondary">Kembali</a>
                </form>
                <?php
                            } else {
                                echo "Data event tidak ditemukan.";
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
