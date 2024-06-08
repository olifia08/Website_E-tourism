<?php
include '../connecting.php';

// Pastikan ID_SK dikirim melalui parameter URL
if (isset($_GET["id"])) {
    // Dapatkan ID_SK dari parameter URL
    $ID_SK = $_GET["id"];

    // Gunakan parameterized query untuk mencegah SQL injection
    $sql = "DELETE FROM s_k WHERE ID_SK = ?";
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($stmt) {
        // Bind parameter
        mysqli_stmt_bind_param($stmt, "s", $ID_SK);
        if (mysqli_stmt_execute($stmt)) {
            // Tutup statement dan koneksi database setelah selesai
            mysqli_stmt_close($stmt);
            mysqli_close($koneksi);
            
            // Setelah menghapus, arahkan kembali ke halaman setting-SnK.php
            header("Location: setting-SnK.php");
            exit();
        } else {
            // Tampilkan pesan kesalahan jika terjadi kesalahan saat eksekusi statement
            mysqli_stmt_close($stmt);
            mysqli_close($koneksi);
            die("Terjadi kesalahan saat menghapus data: " . mysqli_error($koneksi));
        }
    } else {
        // Tampilkan pesan kesalahan jika terjadi kesalahan dalam persiapan statement SQL
        mysqli_close($koneksi);
        die("Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi));
    }
} else {
    // Tampilkan pesan kesalahan jika parameter ID tidak valid atau tidak ditemukan
    echo "Terjadi kesalahan: Parameter ID tidak valid atau tidak ditemukan.";
}
?>
