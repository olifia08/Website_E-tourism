<?php
include '../connecting.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cek apakah parameter ID_TRANSAKSI telah diterima
    if (isset($_POST['id'])) {
        // Escape input untuk mencegah SQL injection
        $id_transaksi = mysqli_real_escape_string($koneksi, $_POST['id']);

        // Update status transaksi menjadi "gagal"
        $query = "UPDATE transaksi SET VALIDASI = 'gagal' WHERE ID_TRANSAKSI = '$id_transaksi'";
        
        if (mysqli_query($koneksi, $query)) {
            // Redirect kembali ke halaman validasi tiket
            header("Location: validasi-ticket.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat membatalkan validasi transaksi: " . mysqli_error($koneksi);
        }
    } else {
        echo "Parameter ID_TRANSAKSI tidak diterima.";
    }
} else {
    echo "Metode permintaan tidak valid.";
}
?>
