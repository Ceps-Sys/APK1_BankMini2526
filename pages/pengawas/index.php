<?php
session_start();
// Waktu timeout diatur 240 detik (4 menit). 
$timeout = 240; 

// 1. Cek Idle Timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    echo "<script>
        alert('Sesi Anda telah berakhir karena tidak ada aktivitas selama beberapa saat. Silakan login kembali.');
        window.location.href = '../../inc/login.php'; 
    </script>";
    exit();
}

// 2. Cek Akun (Apakah sudah login?)
// Disesuaikan dengan penamaan variabel di login.php
if (!isset($_SESSION['Sess_Email'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../../inc/login.php'; 
    </script>";
    exit();
}

// 3. Cek Level Pengawas
// Disesuaikan dengan penamaan variabel di login.php
$level_sesi = strtolower($_SESSION['Sess_Level']);

// Mengecek apakah levelnya 'pengawas' atau ID rolenya '2' (atau '3' jika ada perubahan di database)
if ($level_sesi !== 'pengawas' && $_SESSION['Sess_Level'] !== '2' && $_SESSION['Sess_Level'] !== '3') {
    echo "<script>
        alert('Akses Ditolak! Halaman ini khusus untuk Pengawas.');
        window.location.href = '../../inc/login.php'; 
    </script>";
    exit();
}

// 4. Perbarui waktu aktivitas terakhir agar tidak terhitung idle jika sedang digunakan
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Mini - Dashboard Pengawas</title>
    <link rel="stylesheet" href="../../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../../assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="../../assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="../../assets/compiled/css/table-datatable.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="index.php"><img src="https://zuramai.github.io/mazer/demo/assets/static/images/logo/logo.svg" alt="Logo"></a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">MONITORING NASABAH</li>
                        <li class="sidebar-item">
                            <a href="index.php?pages=data_nasabah" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Data Nasabah</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="index.php?pages=tidak_menabung" class='sidebar-link'>
                                <i class="bi bi-question-circle"></i>
                                <span>Tidak Menabung</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="index.php?pages=lihat_saldo" class='sidebar-link'>
                                <i class="bi bi-credit-card"></i>
                                <span>Lihat Saldo Nasabah</span>
                            </a>
                        </li>

                        <li class="sidebar-title">REKAP TRANSAKSI</li>
                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-journal-bookmark-fill"></i>
                                <span>Rekap Tabungan</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="index.php?pages=rekap_harian" class="submenu-link">Rekap Harian</a></li>
                                <li class="submenu-item"><a href="index.php?pages=rekap_bulanan" class="submenu-link">Rekap Bulanan</a></li>
                            </ul>
                        </li>
                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-journal-text"></i>
                                <span>Rekap Pembayaran</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="index.php?pages=rekap_pembayaran" class="submenu-link">Data Pembayaran</a></li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a href="index.php?pages=laporan_peringkat" class='sidebar-link'>
                                <i class="bi bi-people"></i>
                                <span>Laporan Peringkat</span>
                            </a>
                        </li>

                        <li class="sidebar-title">AKUN</li>
                        <li class="sidebar-item">
                            <a href="index.php?pages=account_profile" class='sidebar-link'>
                                <i class="bi bi-person"></i>
                                <span>Profil</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="../../inc/logout.php" class='sidebar-link'>
                                <i class="bi bi-lock-fill"></i>
                                <span>Keluar</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="main">
            <header class="mb-3 d-flex justify-content-between align-items-center">
                <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
                <div class="ms-auto dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex align-items-center text-dark">
                        <i class="bi bi-person-circle fs-3 me-2"></i>
                        <i class="bi bi-three-dots-vertical"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="index.php?pages=account_profile"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../inc/logout.php"><i class="bi bi-lock me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </header>
            
            <div id="content" class="main-content">
                <div class="container-fluid">
                    <?php include '../../inc/menu.php'; ?>
                </div>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted px-4">
                    <div class="float-start"><p>2026 &copy; Bank Mini Mazer</p></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="../../assets/static/js/components/dark.js"></script>
    <script src="../../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../assets/compiled/js/app.js"></script>
    <script src="../../assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
</body>
</html>