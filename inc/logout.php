<?php
// Memulai koneksi ke sesi aktif yang sedang berjalan di browser pengguna
session_start();

// Menghapus variabel sesi spesifik sesuai aturan "Key session" (Depan ada Sess)
unset($_SESSION['Sess_Email']);
unset($_SESSION['Sess_Level']);

// Mengosongkan seluruh data variabel array sesi sebagai langkah pengamanan ekstra
$_SESSION = [];

// Menghapus seluruh variabel sesi yang terdaftar
session_unset();

// Menghancurkan secara permanen data sesi yang tersimpan di server
session_destroy();

// Mengarahkan (redirect) navigasi peramban pengguna kembali ke halaman login
header("location:../inc/login.php");

// Menghentikan seluruh eksekusi skrip agar kode bawahnya tidak dijalankan
exit;
?>