<?php
session_start();
$timeout = 240; // 240 detik (4 menit)

// 1. Cek Idle Timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    echo "<script>
        alert('Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
        window.location.href = '../inc/login.php'; 
    </script>";
    exit();
}

// 2. Cek apakah user sudah login
if (!isset($_SESSION['Sess_Email'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../inc/login.php'; 
    </script>";
    exit();
}

// 3. Cek apakah level user BENAR-BENAR Admin (atau ID Role 1)
$level_sesi = strtolower($_SESSION['Sess_Level']);
if ($level_sesi !== 'admin' && $_SESSION['Sess_Level'] !== '1') {
    echo "<script>
        alert('Akses Ditolak! Halaman ini khusus untuk Admin.');
        window.location.href = '../inc/login.php'; 
    </script>";
    exit();
}

// 4. Perbarui waktu aktivitas terakhir
$_SESSION['last_activity'] = time();


// 5. AMBIL DATA USER LOGIN UNTUK DITAMPILKAN DI SIDEBAR/HEADER
require_once "../inc/function.php"; // Sesuaikan nama file helper/fungsi kamu (function.php / functions.php)

$EMAIL = $_SESSION['Sess_Email'];

$SQL_LOGIN = tampil("SELECT tbl_admin.nama_admin, tbl_users.email, tbl_users.id_user, tbl_tipe_user.tipe_user 
                    FROM tbl_admin 
                    LEFT JOIN tbl_users ON tbl_admin.id_user = tbl_users.id_user 
                    LEFT JOIN tbl_tipe_user ON tbl_users.role = tbl_tipe_user.id_tipe_user 
                    WHERE tbl_users.email='$EMAIL'");

// Ambil hasil query (baris pertama)
$USER_LOGIN = $SQL_LOGIN[0] ?? null;
$NAMA_USER  = $USER_LOGIN['nama_admin'] ?? 'Admin';
$TIPE_USER  = $USER_LOGIN['tipe_user'] ?? 'Administrator';
$ID_USER    = $USER_LOGIN['id_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Mini - Dashboard Admin</title>

    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="../assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="../assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="../assets/compiled/css/table-datatable.css">
    
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

                <!-- SIDEBAR MENU FITUR BANK MINI -->
                <div class="sidebar-menu">
                    <ul class="menu">
                        
                        <!-- SECTION TRANSAKSI -->
                        <li class="sidebar-title">TRANSAKSI</li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-wallet2"></i>
                                <span>Tabungan Nasabah</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="index.php?pages=tabungan_setor" class="submenu-link">Setor Cash</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="index.php?pages=tabungan_tarik" class="submenu-link">Tarik Cash</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-bank"></i>
                                <span>Transaksi Bank Mini</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="index.php?pages=transaksi_masuk" class="submenu-link">Kas Masuk</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="index.php?pages=transaksi_keluar" class="submenu-link">Kas Keluar</a>
                                </li>
                            </ul>
                        </li>

                        <!-- SECTION NASABAH -->
                        <li class="sidebar-title">NASABAH</li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=data_nasabah" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Data Nasabah</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=tidak_menabung" class="sidebar-link">
                                <i class="bi bi-question-circle"></i>
                                <span>Tidak Menabung</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=lihat_saldo" class="sidebar-link">
                                <i class="bi bi-credit-card"></i>
                                <span>Lihat Saldo Nasabah</span>
                            </a>
                        </li>

                        <!-- SECTION REKAP TRANSAKSI -->
                        <li class="sidebar-title">REKAP TRANSAKSI</li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-journal-bookmark-fill"></i>
                                <span>Rekap Tabungan</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="index.php?pages=rekap_harian" class="submenu-link">Rekap Harian</a>
                                </li>
                                <li class="submenu-item">
                                    <a href="index.php?pages=rekap_bulanan" class="submenu-link">Rekap Bulanan</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-journal-text"></i>
                                <span>Rekap Pembayaran</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="index.php?pages=rekap_pembayaran" class="submenu-link">Data Pembayaran</a>
                                </li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=laporan_peringkat" class="sidebar-link">
                                <i class="bi bi-people"></i>
                                <span>Laporan Peringkat</span>
                            </a>
                        </li>

                        <!-- SECTION PENGATURAN -->
                        <li class="sidebar-title">PENGATURAN</li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=pengaturan_petugas" class="sidebar-link">
                                <i class="bi bi-gear-fill"></i>
                                <span>Pengaturan petugas</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=pengaturan_pengawas" class="sidebar-link">
                                <i class="bi bi-gear-fill"></i>
                                <span>Pengaturan pengawas</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-shield-lock-fill"></i>
                                <span>Konfigurasi Sistem</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="index.php?pages=jurusan" class="submenu-link">Jurusan</a>
                                </li>
                            </ul>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="index.php?pages=kelas" class="submenu-link">Kelas</a>
                                </li>
                            </ul>
                        </li>

                        <!-- SECTION AKUN -->
                        <li class="sidebar-title">AKUN</li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=akun_pengguna" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Akun Pengguna</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="index.php?pages=account_profile" class="sidebar-link">
                                <i class="bi bi-person"></i>
                                <span>Profil</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="../inc/logout.php" class="sidebar-link text-danger">
                                <i class="bi bi-box-arrow-right text-danger"></i>
                                <span>Keluar</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

        <div id="main">
        <!-- HEADER DROPDOWN USER -->
            <header class="mb-3 d-flex justify-content-between align-items-center">
                <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
                
                <div class="ms-auto dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex align-items-center text-decoration-none">
                        <div class="user-name text-end me-3 d-none d-md-block">
                            <h6 class="mb-0 text-gray-600 font-bold"><?= htmlspecialchars($NAMA_USER); ?></h6>
                            <p class="mb-0 text-sm text-muted"><?= htmlspecialchars($TIPE_USER); ?></p>
                        </div>
                        <div class="user-img d-flex align-items-center">
                            <i class="bi bi-person-circle fs-3 text-primary"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="dropdown-header">
                            <h6><?= htmlspecialchars($NAMA_USER); ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($TIPE_USER); ?></small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?pages=account_profile"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="index.php?pages=pengaturan_sistem"><i class="bi bi-gear me-2"></i> Pengaturan <span class="badge bg-success ms-2">New</span></a></li>
                        <li><a class="dropdown-item" href="index.php?pages=akun_pengguna"><i class="bi bi-people me-2"></i> Pengguna</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../inc/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </header>
            
            <main id="content" class="main-content">
                <div class="container-fluid">
                    <!-- Konten dinamis dari menu akan dimuat di sini menggunakan PHP -->
                    <?php 
                        require_once '../inc/menu.php'; 
                    ?>
                </div>
            </main>

            <footer>
                <div class="footer clearfix mb-0 text-muted px-4">
                    <div class="float-start"><p>2026 &copy; Bank Mini Mazer</p></div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JS SCRIPTS -->
    <script src="../assets/static/js/components/dark.js"></script>
    <script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../assets/compiled/js/app.js"></script>
    <script src="../assets/extensions/simple-datatables/umd/simple-datatables.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('#sidebar');
            if (sidebar && typeof PerfectScrollbar !== 'undefined') {
                new PerfectScrollbar(sidebar, {
                    wheelPropagation: false,
                    suppressScrollX: true
                });
            }

            const currentPage = new URLSearchParams(window.location.search).get('pages') || 'dashboard';
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
                const link = item.querySelector('a[href*="pages=' + currentPage + '"]');
                if (link) {
                    item.classList.add('active');
                }
            });

            document.querySelector('.burger-btn')?.addEventListener('click', (e) => {
                e.preventDefault();
                document.body.classList.toggle('sidebar-mini');
            });
        });
    </script>
</body>
</html>