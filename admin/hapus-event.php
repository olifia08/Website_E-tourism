
<?php
include '../connecting.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
$ID_EVENT = $_POST["id"];
// Gunakan parameterized query untuk menghindari SQL injection
$sql = "DELETE FROM event WHERE  ID_EVENT= ?";
$stmt = mysqli_prepare($koneksi, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $ID_EVENT); 
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($koneksi);
        
        // Setelah menghapus, arahkan kembali ke halaman daftar menu
        header("Location: event-admin.php");
        exit();
    } else {
        // Ganti pesan kesalahan dengan pesan yang lebih spesifik jika diperlukan
        die("Terjadi kesalahan saat menghapus menu: " . mysqli_error($koneksi));
    }
    } else {
        // Ganti pesan kesalahan dengan pesan yang lebih spesifik jika diperlukan
        die("Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi));
    }
} else {
    // Tambahkan pesan kesalahan jika parameter tidak valid
    echo "Terjadi kesalahan: Parameter ID tidak valid atau tidak ditemukan.";
}
?>

