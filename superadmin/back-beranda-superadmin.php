<?php
include '../connecting.php';

// Query untuk mengambil data dari database
$query = "
    SELECT 
        DATE_FORMAT(WAKTU_TRANSAKSI, '%Y-%m') AS month,
        SUM(CASE WHEN TYPE_USER = 'wisnu' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisnu,
        SUM(CASE WHEN TYPE_USER = 'wisman' THEN JUMLAH_PEMESAN ELSE 0 END) AS total_wisman,
        SUM(JUMLAH_PEMESAN) AS total_penjualan_tiket
    FROM transaksi
    GROUP BY month
    ORDER BY month
";

$result = mysqli_query($koneksi, $query);

$months = [];
$wisnu = [];
$wisman = [];
$total = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $months[] = $row['month'];
        $wisnu[] = $row['total_wisnu'];
        $wisman[] = $row['total_wisman'];
        $total[] = $row['total_penjualan_tiket'];
    }
}

$data = [
    'months' => $months,
    'wisnu' => $wisnu,
    'wisman' => $wisman,
    'total' => $total
];

mysqli_close($koneksi);

echo json_encode($data);
?>
