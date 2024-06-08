<?php
include '../connecting.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'month';

if ($type === 'year') {
    $query = "
        SELECT 
            DATE_FORMAT(waktu_transaksi, '%Y') AS tahun,
            SUM(CASE WHEN TYPE_USER = 'wisnu' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisnu,
            SUM(CASE WHEN TYPE_USER = 'wisman' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisman,
            SUM(JUMLAH_PEMESAN) AS total_penjualan_tiket,
            SUM(TOTAL_HARGA) AS total_pemasukan_penjualan
        FROM transaksi
        GROUP BY tahun
        ORDER BY tahun
    ";
} else {
    $query = "
        SELECT 
            DATE_FORMAT(waktu_transaksi, '%Y') AS tahun, 
            DATE_FORMAT(waktu_transaksi, '%M') AS bulan,
            SUM(CASE WHEN TYPE_USER = 'wisnu' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisnu,
            SUM(CASE WHEN TYPE_USER = 'wisman' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisman,
            SUM(JUMLAH_PEMESAN) AS total_penjualan_tiket,
            SUM(TOTAL_HARGA) AS total_pemasukan_penjualan
        FROM transaksi
        GROUP BY tahun, bulan
        ORDER BY tahun, FIELD(bulan, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')
    ";
}

$result = mysqli_query($koneksi, $query);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

mysqli_close($koneksi);

echo json_encode($data);
?>
