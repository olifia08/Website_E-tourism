<?php
include 'connecting.php';

$days = [];
$total_transaksis = [];

// Query untuk mengambil total transaksi per hari dengan rentang 14 hari
$query = "
    SELECT 
        DATE(WAKTU_BOOKING) AS hari,
        SUM(JUMLAH_PEMESAN) AS jumlah_pemesan
    FROM 
        transaksi
    WHERE 
        WAKTU_BOOKING BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE() OR
        WAKTU_BOOKING BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    GROUP BY 
        hari
    ORDER BY 
        hari
";


$result = mysqli_query($koneksi, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $days[] = $row['hari'];
        $total_transaksis[] = (int)$row['jumlah_pemesan'];
    }
} else {
    echo json_encode(["error" => "Terjadi kesalahan: " . mysqli_error($koneksi)]);
    exit;
}

// Menyusun data dalam format yang sesuai untuk JavaScript
$data = [
    'days' => $days,
    'total_transaksi' => $total_transaksis
];

mysqli_close($koneksi);

echo json_encode($data);
?>
