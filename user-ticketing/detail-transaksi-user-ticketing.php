<?php
session_start();
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_USER'])) {
    header("Location: login-user-ticketing.php");
    exit();
}

// Mendapatkan ID Transaksi dari URL
if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
} else {
    echo "ID Transaksi tidak ditemukan.";
    exit();
}

// Siapkan pernyataan SQL untuk mengambil detail transaksi
$sql_detail = "SELECT ID_TRANSAKSI, WAKTU_TRANSAKSI, WAKTU_BOOKING, NAMA_PEMESAN, ALAMAT_PEMESAN, JUMLAH_PEMESAN, TOTAL_HARGA, VALIDASI FROM transaksi WHERE ID_TRANSAKSI = ?";
$stmt_detail = mysqli_prepare($koneksi, $sql_detail);

if ($stmt_detail) {
    mysqli_stmt_bind_param($stmt_detail, "s", $id_transaksi); // Mengikat parameter sebagai string
    mysqli_stmt_execute($stmt_detail);
    mysqli_stmt_bind_result($stmt_detail, $id_transaksi, $waktu_transaksi, $waktu_booking, $nama_pemesan, $alamat_pemesan, $jumlah_pemesan, $total_harga, $validasi);
    mysqli_stmt_fetch($stmt_detail);
    mysqli_stmt_close($stmt_detail);
} else {
    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
    exit();
}

// Menutup koneksi
mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detail Transaksi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Alex+Brush" rel="stylesheet">
    <link rel="stylesheet" href="../css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/aos.css">
    <link rel="stylesheet" href="../css/ionicons.min.css">
    <link rel="stylesheet" href="../css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="../css/jquery.timepicker.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/icomoon.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body, html {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
        .card {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        table {
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }h5,card-header{
            text-align:center;
        }
        .card-img-top {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <img src="../images/bg_1.jpg" class="card-img-top">
        <div class="card-body">
        <div class="card-header" style="text-align: center;">
            <?php echo $id_transaksi; ?> 
        </div>
            <h5 class="card-title">Tiket THP Kenjeran</h5>
            <p class="card-text">
                <table>
                    <tr>
                        <th>Waktu Booking</th>
                        <td><?php echo $waktu_booking; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Pemesan</th>
                        <td><?php echo $nama_pemesan; ?>a</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pemesan</th>
                        <td><?php echo $jumlah_pemesan; ?></td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td><?php echo $total_harga; ?></td>
                    </tr>
                </table>
            </p>
            <button onclick="window.print()" class="btn btn-primary no-print"><i class="bi bi-printer"></i> Cetak Transaksi</button>
            <a href="riwayat-ticketing.php" class="btn btn-secondary no-print">Kembali</a>
        </div>
    </div>
</div>
</body>
</html>
