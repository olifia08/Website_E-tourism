<?php
include '../connecting.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $idTransaksi = $_POST['id'];

    // Perbarui status validasi transaksi
    $sql = "UPDATE transaksi SET VALIDASI = 'berhasil' WHERE ID_TRANSAKSI = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $idTransaksi);
        if (mysqli_stmt_execute($stmt)) {
            echo 'success';
        } else {
            echo 'error';
        }
        mysqli_stmt_close($stmt);
    } else {
        echo 'error';
    }

    mysqli_close($koneksi);
} else {
    echo 'invalid request';
}
?>

