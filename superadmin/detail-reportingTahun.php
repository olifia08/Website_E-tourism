<?php
session_start();
include '../connecting.php';

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['USERNAME_SUPERADMIN'])) {
    header("Location: superadmin-login.php");
    exit();
}

// Mendapatkan tahun dari URL
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Pastikan tahun tidak kosong
if (empty($tahun)) {
    echo "Tahun harus diberikan.";
    exit();
}

// Siapkan pernyataan SQL untuk mengambil detail transaksi
$sql = "
    SELECT
        MONTHNAME(WAKTU_TRANSAKSI) AS bulan,
        SUM(CASE WHEN TYPE_USER = 'wisnu' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisnu,
        SUM(CASE WHEN TYPE_USER = 'wisman' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisman,
        SUM(JUMLAH_PEMESAN) AS jumlah_pemesan,
        SUM(TOTAL_HARGA) AS total_harga
    FROM
        transaksi
    WHERE
        YEAR(WAKTU_TRANSAKSI) = ?
    GROUP BY
        bulan
    ORDER BY
        FIELD(bulan, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')
";

$stmt = mysqli_prepare($koneksi, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $tahun);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $bulan, $total_wisnu, $total_wisman, $jumlah_pemesan, $total_harga);
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
  <title>Detail Reporting - <?php echo htmlspecialchars($tahun); ?></title>
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
            <th colspan="7" style="color: black; text-align: center;"><h2 class="mt-4">Laporan Transaksi Penjualan Tiket THP Kenjeran - <?php echo htmlspecialchars("$tahun"); ?></h2><br>
    </th></tr></thead>
            <tr style="text-align: center;">
              <th>No</th>
              <th>Bulan</th>
              <th>Wisnu</th>
              <th>Wisman</th>
              <th>Jumlah Pemesan</th>
              <th>Total Harga</th>
            </tr>
          <tbody style="text-align: center;">
            <?php
              $no = 1;
              while (mysqli_stmt_fetch($stmt)) {
                  echo "<tr>";
                  echo "<td>" . $no . "</td>";
                  echo "<td>" . htmlspecialchars($bulan) . "</td>";
                  echo "<td>" . htmlspecialchars($total_wisnu) . "</td>";
                  echo "<td>" . htmlspecialchars($total_wisman) . "</td>";
                  echo "<td>" . htmlspecialchars($jumlah_pemesan) . "</td>";
                  echo "<td>" . number_format($total_harga, 2) . "</td>";
                  echo "</tr>";
                  $total_pemasukan_penjualan += $total_harga;
                  $no++;
              }
              if ($no == 1) {
                  echo "<tr><td colspan='6'>Tidak ada data transaksi untuk tahun ini.</td></tr>";
              }
              mysqli_stmt_close($stmt);
              mysqli_close($koneksi);
            ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" style="text-align: right;"><strong>Total Pemasukan:</strong></td>
              <td><?php echo number_format($total_pemasukan_penjualan, 2); ?></td>
            </tr>
          </tfoot>
        </table>
        <button onclick="window.print()" class="btn btn-primary no-print"><i class="bi bi-printer"></i> Cetak Transaksi</button>
        <button class="btn btn-success no-print" onclick="ExportToExcel('xlsx')"><i class="bi bi-file-earmark-spreadsheet"></i>
            Excel
        </button>
        <a href="reporting.php" class="btn btn-secondary no-print">Kembali</a>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script>
    function ExportToExcel(type, fn, dl) {
        var elt = document.getElementById('myTable');
        var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
        return dl ?
            XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
            XLSX.writeFile(wb, fn || ('Laporan Transaksi THP Kenjeran <?php echo htmlspecialchars($tahun); ?>.' + (type || 'xlsx')));
    }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

