<?php
$server = "localhost"; // Gantilah dengan nama server database Anda
$username = "root"; // Gantilah dengan nama pengguna database Anda
$password = ""; // Gantilah dengan kata sandi database Anda
$database = "ticekting"; // Gantilah dengan nama database Anda

// Buat koneksi
$koneksi = mysqli_connect($server, $username, $password, $database);

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
