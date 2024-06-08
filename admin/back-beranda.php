<?php
include '../connecting.php';

header('Content-Type: application/json');

// Assuming you have columns: month, wisnu, wisman, total in your 'transaksi' table
$query = "SELECT month, SUM(wisnu) as wisnu, SUM(wisman) as wisman, SUM(wisnu + wisman) as total FROM transaksi GROUP BY month";
$result = mysqli_query($koneksi, $query);

$data = [
    'months' => [],
    'wisnu' => [],
    'wisman' => [],
    'total' => []
];

while ($row = mysqli_fetch_assoc($result)) {
    $data['months'][] = $row['month'];
    $data['wisnu'][] = (int)$row['wisnu'];
    $data['wisman'][] = (int)$row['wisman'];
    $data['total'][] = (int)$row['total'];
}

echo json_encode($data);

mysqli_close($koneksi);
?>
