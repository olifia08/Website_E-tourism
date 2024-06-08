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

// Menangani permintaan POST untuk memperbarui FAQ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit-fq"])) {
    $id_fq = $_POST['id'];
    $pertanyaan = $_POST['pertanyaan'];
    $jawaban = $_POST['jawaban'];
    $tanggal = date('Y-m-d');

    $sql = 'UPDATE f_q SET PERTANYAAN_FQ=?, JAWABAN_FQ=?, WAKTU_FQ=? WHERE ID_FQ=?';
    $stmt_update = mysqli_prepare($koneksi, $sql);
    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "ssss", $pertanyaan, $jawaban, $tanggal, $id_fq);
        if (mysqli_stmt_execute($stmt_update)) {
            header("Location: faq-admin.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat memperbarui data FAQ: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt_update);
    } else {
        echo "Terjadi kesalahan saat mempersiapkan pernyataan SQL: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit FAQ - Admin Dashboard</title>
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
            <div id="editFaq" class="pt-3">
                <h2>Edit FAQ</h2>
                <b>
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
                    $ID_FQ = $_GET["id"];
                    $sql = "SELECT * FROM f_q WHERE ID_FQ = ?";
                    $stmt = mysqli_prepare($koneksi, $sql);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "s", $ID_FQ);
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            if ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $row['ID_FQ']; ?>">
                                    <div class="form-group">
                                        <label for="pertanyaan"><h4>Pertanyaan</h4></label>
                                        <textarea class="form-control" id="pertanyaan" style="height: 120px; width: 100%;"
                                                  placeholder="Masukkan jawaban" name="pertanyaan"><?php echo htmlspecialchars($row['PERTANYAAN_FQ'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="jawaban"><h4>Jawaban</h4></label>
                                        <textarea class="form-control" id="jawaban" style="height: 120px; width: 100%;"
                                                  placeholder="Masukkan jawaban" name="jawaban"><?php echo htmlspecialchars($row['JAWABAN_FQ'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="edit-fq">Simpan Perubahan</button>
                                    <a href="faq-admin.php" class="btn btn-secondary">Batal</a>
                                </form>
                                <?php
                            } else {
                                echo "Data FAQ tidak ditemukan.";
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
