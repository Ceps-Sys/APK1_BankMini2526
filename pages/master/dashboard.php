<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Mini - Dashboard</title>
    
    <link rel="shortcut icon" href="../assets/compiled/svg/favicon.svg" type="image/x-icon">
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
                            <a href="index.html"><img src="../assets/compiled/svg/logo.svg" alt="Logo"></a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">TRANSAKSI</li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-wallet2"></i>
                                <span>Tabungan Nasabah</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="#" class="submenu-link">Setor Cash</a></li>
                                <li class="submenu-item"><a href="#" class="submenu-link">Tarik Cash</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-bank"></i>
                                <span>Transaksi Bank Mini</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="#" class="submenu-link">Kas Masuk</a></li>
                                <li class="submenu-item"><a href="#" class="submenu-link">Kas Keluar</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-title">NASABAH</li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Data Nasabah</span>
                                <span class="badge bg-success ms-auto">New</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-question-circle"></i>
                                <span>Tidak Menabung</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
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
                                <li class="submenu-item"><a href="#" class="submenu-link">Rekap Harian</a></li>
                                <li class="submenu-item"><a href="#" class="submenu-link">Rekap Bulanan</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-journal-text"></i>
                                <span>Rekap Pembayaran</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="#" class="submenu-link">Data Pembayaran</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-people"></i>
                                <span>Laporan Peringkat</span>
                                <span class="badge bg-success ms-auto">New</span>
                            </a>
                        </li>

                        <li class="sidebar-title">PENGATURAN</li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-gear-fill"></i>
                                <span>Pengaturan Sistem</span>
                                <span class="badge bg-success ms-auto">New</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-shield-lock-fill"></i>
                                <span>Pengaturan Nasabah</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="#" class="submenu-link">Kategori Nasabah</a></li>
                            </ul>
                        </li>

                        <li class="sidebar-title">AKUN</li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Akun Pengguna</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-person"></i>
                                <span>Profil</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="#" class='sidebar-link'>
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
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="#"><i class="bi bi-gear me-2"></i> Pengaturan <span class="badge bg-success ms-2">New</span></a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-people me-2"></i> Pengguna</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-lock me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </header>
            
            <div id="content" class="main-content">
                <div class="container-fluid">
                    <div class="page-heading">
                        <h3>Dashboard Bank Mini</h3>
                    </div>
                    <div class="page-content">
                        <section class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Selamat Datang!</h4>
                                    </div>
                                    <div class="card-body">
                                        <p>Silakan pilih menu di sebelah kiri untuk mulai mengelola transaksi atau data nasabah.</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted px-4">
                    <div class="float-start"><p>2026 &copy; Bank Mini Mazer</p></div>
                </div>
            </footer>
        </div>
    </div>

    <script src="../assets/static/js/components/dark.js"></script>
    <script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../assets/compiled/js/app.js"></script>
    <script src="../assets/extensions/simple-datatables/umd/simple-datatables.js"></script>

    <script>
        let ps;
        if (typeof PerfectScrollbar !== 'undefined') {
            const sidebar = document.querySelector('#sidebar');
            ps = new PerfectScrollbar(sidebar, {
                wheelPropagation: false,
                suppressScrollX: true
            });
        }

        document.querySelector('.burger-btn')?.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-mini');
        });
    </script>
</body>
</html>