<?php 
// Mengambil parameter 'pages' dari URL; jika tidak ada, gunakan default 'dashboard'
$PAGE = $_GET['pages'] ?? 'dashboard';

// Melakukan pengecekan nilai variabel $PAGE menggunakan switch statement
switch ($PAGE) {
    // Rute untuk profil akun pengguna
    case 'account_profile':
        // Menentukan lokasi fisik berkas profile.php
        $PATH = __DIR__ . '/../pages/master/profile.php';
        // Memeriksa apakah file tersebut benar-benar ada di direktori
        if (file_exists($PATH)) {
            // Memuat dan menjalankan file profil jika ditemukan
            include $PATH;
        }
        break;

    // Rute untuk menampilkan data nasabah
    case 'data_nasabah':
        // Menentukan path direktori file tabel nasabah
        $PATH = __DIR__ . '/../pages/master/table_b.php';
        // Memeriksa keberadaan berkas sebelum dimasukkan
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;

    // Rute untuk menampilkan daftar akun pengguna
    case 'akun_pengguna':
        // Menentukan lokasi berkas tabel pengguna
        $PATH = __DIR__ . '/../pages/master/table.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;

    // Rute untuk form penambahan user baru
    case 'tambah_user':
        // Menentukan path lokasi form tambah
        $PATH = __DIR__ . '/../pages/master/tambah.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;

    // Rute untuk memproses pembuatan akun admin
    case 'prosestambah_admin':
        // Menentukan lokasi file pemroses data admin
        $PATH = __DIR__ . '/../pages/master/proses_tambah.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;

    case 'proses_edit':
        include "../pages/master/proses_edit.php";
        break;

    // Rute untuk form perbaikan/edit data pengguna
    case 'edit_user':
        // Menentukan path file edit
        $PATH = __DIR__ . '/../pages/master/edit.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;

    // Rute untuk memproses penghapusan pengguna
    case 'hapus_user':
        // Menentukan lokasi file proses hapus
        $PATH = __DIR__ . '/../pages/master/hapus.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;

    // Rute ganda untuk pemeriksaan saldo nasabah
    case 'lihat_saldo':
    case 'cek_saldo': // Mendukung alias tautan cek_saldo
        // Menentukan lokasi file lihat saldo
        $PATH = __DIR__ . '/../pages/nasabah/lihat_saldo.php';
        if (file_exists($PATH)) {
            include $PATH;
        } else {
            // Menampilkan pesan peringatan jika berkas tidak ditemukan
            echo "<div class='alert alert-danger'>File lihat_saldo.php tidak ditemukan di direktori nasabah.</div>";
        }
        break;

     // Rute untuk modul rekapitulasi saldo oleh petugas
     case 'rekap_saldo': 
        // Menentukan path berkas rekap saldo petugas
        $PATH = __DIR__ . '/../petugas/rekap_saldo.php';
        if (file_exists($PATH)) {
            include $PATH;
        } else {
            // Menampilkan pesan kesalahan jika file tidak ditemukan
            echo "<div class='alert alert-danger'>File rekap_saldo.php tidak ditemukan di direktori petugas.</div>";
        }
        break;

    // Rute untuk melihat riwayat transaksi nasabah
    case 'riwayat_transaksi':
        // Menentukan path berkas riwayat transaksi
        $PATH = __DIR__ . '/../pages/nasabah/riwayat_transaksi.php';
        if (file_exists($PATH)) {
            include $PATH;
        } else {
            echo "<div class='alert alert-danger'>File riwayat_transaksi.php tidak ditemukan.</div>";
        }
        break;

    // Rute bawaan (default) jika nilai $PAGE adalah 'dashboard' atau tidak cocok dengan case mana pun
    case 'dashboard':
    default:
        // Menentukan path ke halaman utama dashboard
        $PATH = __DIR__ . '/../pages/dashboard.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;
        case 'pengaturan_petugas':
        // Menentukan path lokasi form tambah
        $PATH = __DIR__ . '/../pages/petugas/pengguna_petugas.php';
        if (file_exists($PATH)) {
            include $PATH;
        }
        break;
         case 'tampil_petugas':
        // Menentukan path lokasi form tambah
        $PATH = __DIR__ . '/../pages/petugas/tampil.php';
        break;
}
?>