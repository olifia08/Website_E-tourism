<?php
// Masukkan koneksi database
include '../connecting.php';

// Pastikan tanggal dikirimkan melalui parameter GET
if (isset($_GET['tanggal'])) {
    // Ambil tanggal dari parameter GET
    $tanggal = $_GET['tanggal'];

    // Buat query untuk mengambil jumlah pemesan pada tanggal yang diberikan
    $sql = "SELECT SUM(JUMLAH_PEMESAN) AS total_pemesan FROM transaksi WHERE WAKTU_BOOKING = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $tanggal);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total_pemesan);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Jika ada pemesan pada tanggal tersebut, kirim jumlahnya, jika tidak kirim 0
    $jumlah_pemesan = $total_pemesan ? $total_pemesan : 0;
    echo $jumlah_pemesan;
} else {
    // Jika parameter tanggal tidak diberikan, kirimkan pesan kesalahan
    echo "Error: Tanggal tidak diberikan.";
}
?>
