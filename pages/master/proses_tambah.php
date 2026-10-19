<?php
session_start();

require_once __DIR__ . '/../../inc/function.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sesuaikan path target foto naik 2 tingkat ke folder /admin/images/user_admin
    if (tambah_admin($_POST, $_FILES, '../../images/user_admin')) {
        echo "<script>
                alert('Data admin berhasil ditambahkan!');
                window.location.href = '../../index.php?pages=akun_pengguna';
              </script>";
        exit();
    } else {
        // Tangkap pesan error spesifik yang disimpan di SESSION oleh fungsi tambah_admin()
        $pesan_error = isset($_SESSION['Sess_FormErrors']) ? implode('\n', $_SESSION['Sess_FormErrors']) : 'Gagal menambahkan data admin!';
        unset($_SESSION['Sess_FormErrors']); // Bersihkan session error
        
        // Redirect kembali ke halaman akun_pengguna agar modal/halaman tidak hilang ke route 404
        echo "<script>
                alert('Gagal: " . addslashes($pesan_error) . "');
                window.location.href = '../../index.php?pages=akun_pengguna';
              </script>";
        exit();
    }
}
?>