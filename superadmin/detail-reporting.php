<?php
session_start();
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_SUPERADMIN'])) {
    header("Location: superadmin-login.php");
    exit();
}

// Mendapatkan tahun dan bulan dari URL
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

// Pastikan tahun dan bulan tidak kosong
if (empty($tahun) || empty($bulan)) {
    echo "Tahun dan bulan harus diberikan.";
    exit();
}

// Konversi nama bulan ke angka
$month_num = date('m', strtotime("1 $bulan")); // Using the first day of the month

// Siapkan pernyataan SQL untuk mengambil detail transaksi
$sql = "
    SELECT
        ID_TRANSAKSI,
        NAMA_PEMESAN, 
        JUMLAH_PEMESAN,
        TOTAL_HARGA,
        TYPE_USER,
        DATE_FORMAT(WAKTU_TRANSAKSI, '%d-%m-%Y %H:%i:%s') AS WAKTU_TRANSAKSI
    FROM transaksi
    WHERE YEAR(WAKTU_TRANSAKSI) = ? AND MONTH(WAKTU_TRANSAKSI) = ?
";

$stmt = mysqli_prepare($koneksi, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $tahun, $month_num);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_transaksi, $pemesan, $jumlah_pemesan, $total_harga, $type, $waktu_transaksi);
} else {
    echo "Terjadi kesalahan: " . mysqli_error($koneksi);
    exit();
}

$total_pemasukan_penjualan = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Reporting - <?php echo htmlspecialchars("$bulan $tahun"); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        body {
            font-size: 0.875rem;
        }
        .navbar-brand {
            color: #c0392b !important;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" id="myTable">
                <thead><tr>
                    <th colspan="7" style="color: black; text-align: center;">
                        <h2 class="mt-4">Laporan Transaksi Penjualan Tiket THP Kenjeran di bulan- <?php echo htmlspecialchars("$bulan $tahun"); ?></h2><br>
                    </th></tr></thead>
                <tr style="text-align: center;">
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Nama Pemesan</th>
                    <th>Waktu Transaksi</th>
                    <th>Type</th>
                    <th>Jumlah Pemesan</th>
                    <th>Total Harga</th>
                </tr>
                <tbody style="text-align: center;">
                <?php
                $no = 1;
                while (mysqli_stmt_fetch($stmt)) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . htmlspecialchars($id_transaksi) . "</td>";
                    echo "<td>" . htmlspecialchars($pemesan) . "</td>";
                    echo "<td>" . htmlspecialchars($waktu_transaksi) . "</td>";
                    echo "<td>" . htmlspecialchars($type) . "</td>";
                    echo "<td>" . htmlspecialchars($jumlah_pemesan) . "</td>";
                    echo "<td>" . number_format($total_harga, 2) . "</td>";
                    echo "</tr>";
                    $total_pemasukan_penjualan += $total_harga;
                    $no++;
                }
                if ($no == 1) {
                    echo "<tr><td colspan='7'>Tidak ada data transaksi untuk bulan ini.</td></tr>";
                }
                mysqli_stmt_close($stmt);
                mysqli_close($koneksi);
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Total Pemasukan:</strong></td>
                    <td><?php echo number_format($total_pemasukan_penjualan, 2); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <button onclick="window.print()" class="btn btn-primary no-print"><i class="bi bi-printer"></i> Cetak Transaksi</button>
    <button class="btn btn-success no-print" onclick="ExportToExcel('xlsx')"><i class="bi bi-file-earmark-spreadsheet"></i>
        Excel
    </button>
    <a href="reporting.php" class="btn btn-secondary no-print">Kembali</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
    function ExportToExcel(type, fn, dl) {
        var elt = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
        return dl ?
            XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
            XLSX.writeFile(wb, fn || ('Laporan Transaksi THP Kenjeran <?php echo htmlspecialchars("$bulan $tahun"); ?>.' + (type || 'xlsx')));
    }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
