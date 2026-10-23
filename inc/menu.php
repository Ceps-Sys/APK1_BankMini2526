<?php 
// Mengambil parameter 'pages' dari URL; jika tidak ada, gunakan default 'dashboard'
$PAGE = $_GET['pages'] ?? 'dashboard';

// Melakukan pengecekan nilai variabel $PAGE menggunakan switch statement
switch ($PAGE) {

    // ==============================================================
    // 1. MODUL MASTER / PENGGUNA (ADMIN)
    // ==============================================================
    
    case 'account_profile':
        $PATH = __DIR__ . '/../pages/master/profile.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'akun_pengguna':
        $PATH = __DIR__ . '/../pages/master/table.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'tambah_user':
        $PATH = __DIR__ . '/../pages/master/tambah.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'prosestambah_admin':
        $PATH = __DIR__ . '/../pages/master/proses_tambah.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'edit_user':
        $PATH = __DIR__ . '/../pages/master/edit.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'proses_edit':
        include "../pages/master/proses_edit.php";
        break;

    case 'hapus_user':
        $PATH = __DIR__ . '/../pages/master/hapus.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'data_nasabah':
        $PATH = __DIR__ . '/../pages/master/table_b.php';
        if (file_exists($PATH)) include $PATH;
        break;


    // ==============================================================
    // 2. MODUL PETUGAS
    // ==============================================================
    
    case 'pengaturan_petugas':
    case 'tampil_petugas':
        $PATH = __DIR__ . '/../pages/petugas/tampil.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'tambah_petugas':
        $PATH = __DIR__ . '/../pages/petugas/tambah.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'prosestambah_petugas':
        $PATH = __DIR__ . '/../pages/petugas/proses_tambah.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'edit_petugas':
        $PATH = __DIR__ . '/../pages/petugas/edit.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'prosesedit_petugas':
        $PATH = __DIR__ . '/../pages/petugas/proses_edit.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'hapus_petugas':
        $PATH = __DIR__ . '/../pages/petugas/hapus.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'rekap_saldo': 
        $PATH = __DIR__ . '/../petugas/rekap_saldo.php';
        if (file_exists($PATH)) include $PATH;
        else echo "<div class='alert alert-danger'>File rekap_saldo.php tidak ditemukan di direktori petugas.</div>";
        break;

    
    // ==============================================================
    // 3. MODUL PENGAWAS
    // ==============================================================
    case 'pengaturan_pengawas':
    case 'tampil_pengawas':
        $PATH = __DIR__ . '/../pages/pengawas/tampil.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'tambah_pengawas':
        $PATH = __DIR__ . '/../pages/pengawas/tambah.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'prosestambah_pengawas':
        $PATH = __DIR__ . '/../pages/pengawas/proses_tambah.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'edit_pengawas':
        $PATH = __DIR__ . '/../pages/pengawas/edit.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'prosesedit_pengawas':
        $PATH = __DIR__ . '/../pages/pengawas/proses_edit.php';
        if (file_exists($PATH)) include $PATH;
        break;

    case 'hapus_pengawas':
        $PATH = __DIR__ . '/../pages/pengawas/hapus.php';
        if (file_exists($PATH)) include $PATH;
        break;



    // ==============================================================
    // 4. MODUL NASABAH
    // ==============================================================
    
    case 'lihat_saldo':
    case 'cek_saldo': 
        $PATH = __DIR__ . '/../pages/nasabah/lihat_saldo.php';
        if (file_exists($PATH)) include $PATH;
        else echo "<div class='alert alert-danger'>File lihat_saldo.php tidak ditemukan di direktori nasabah.</div>";
        break;

    case 'riwayat_transaksi':
        $PATH = __DIR__ . '/../pages/nasabah/riwayat_transaksi.php';
        if (file_exists($PATH)) include $PATH;
        else echo "<div class='alert alert-danger'>File riwayat_transaksi.php tidak ditemukan.</div>";
        break;


    // ==============================================================
    // 5. DASHBOARD & DEFAULT ROUTE
    // ==============================================================
    
    case 'dashboard':
    default:
        $PATH = __DIR__ . '/../pages/dashboard.php';
        if (file_exists($PATH)) include $PATH;
        break;
}
?>