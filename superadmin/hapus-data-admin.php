<?php
include '../connecting.php';

// Pastikan ID_DATA_ADMIN dikirim melalui parameter URL
if (isset($_GET["id"])) {
    // Dapatkan ID_DATA_ADMIN dari parameter URL
    $ID_DATA_ADMIN = $_GET["id"];

    // Gunakan parameterized query untuk mencegah SQL injection
    // Pertama, kita harus mendapatkan ID_ADMIN berdasarkan ID_DATA_ADMIN
    $sqlSelect = "SELECT ID_ADMIN FROM data_admin WHERE ID_DATA_ADMIN = ?";
    $stmtSelect = mysqli_prepare($koneksi, $sqlSelect);

    if ($stmtSelect) {
        // Bind parameter
        mysqli_stmt_bind_param($stmtSelect, "s", $ID_DATA_ADMIN);
        mysqli_stmt_execute($stmtSelect);
        mysqli_stmt_bind_result($stmtSelect, $ID_ADMIN);
        mysqli_stmt_fetch($stmtSelect);
        mysqli_stmt_close($stmtSelect);

        // Jika ID_ADMIN ditemukan, lanjutkan dengan penghapusan
        if ($ID_ADMIN) {
            // Mulai transaksi untuk memastikan kedua operasi DELETE terjadi secara atomik
            mysqli_begin_transaction($koneksi);

            // Query untuk menghapus dari data_admin
            $sqlDeleteDataAdmin = "DELETE FROM data_admin WHERE ID_DATA_ADMIN = ?";
            $stmtDeleteDataAdmin = mysqli_prepare($koneksi, $sqlDeleteDataAdmin);

            // Query untuk menghapus dari admin
            $sqlDeleteAdmin = "DELETE FROM admin WHERE ID_ADMIN = ?";
            $stmtDeleteAdmin = mysqli_prepare($koneksi, $sqlDeleteAdmin);

            if ($stmtDeleteDataAdmin && $stmtDeleteAdmin) {
                // Bind parameter dan eksekusi statement
                mysqli_stmt_bind_param($stmtDeleteDataAdmin, "s", $ID_DATA_ADMIN);
                mysqli_stmt_bind_param($stmtDeleteAdmin, "s", $ID_ADMIN);

                $successDataAdmin = mysqli_stmt_execute($stmtDeleteDataAdmin);
                $successAdmin = mysqli_stmt_execute($stmtDeleteAdmin);

                if ($successDataAdmin && $successAdmin) {
                    // Commit transaksi jika kedua operasi DELETE berhasil
                    mysqli_commit($koneksi);

                    // Tutup statement
                    mysqli_stmt_close($stmtDeleteDataAdmin);
                    mysqli_stmt_close($stmtDeleteAdmin);

                    // Tutup koneksi database setelah selesai
                    mysqli_close($koneksi);

                    // Setelah menghapus, arahkan kembali ke halaman daftar admin
                    header("Location: admin-management.php");
                    exit();
                } else {
                    // Rollback transaksi jika ada kesalahan
                    mysqli_rollback($koneksi);

                    // Tampilkan pesan kesalahan jika terjadi kesalahan saat eksekusi statement
                    die("Terjadi kesalahan saat menghapus data admin: " . mysqli_error($koneksi));
                }
            } else {
                // Tampilkan pesan kesalahan jika terjadi kesalahan dalam persiapan statement SQL
                die("Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi));
            }
        } else {
            // Tampilkan pesan kesalahan jika ID_ADMIN tidak ditemukan
            echo "Terjadi kesalahan: ID_ADMIN tidak ditemukan untuk ID_DATA_ADMIN yang diberikan.";
        }
    } else {
        // Tampilkan pesan kesalahan jika terjadi kesalahan dalam persiapan statement SQL
        die("Terjadi kesalahan dalam persiapan statement SQL: " . mysqli_error($koneksi));
    }
} else {
    // Tampilkan pesan kesalahan jika parameter ID tidak valid atau tidak ditemukan
    echo "Terjadi kesalahan: Parameter ID tidak valid atau tidak ditemukan.";
}
?>
