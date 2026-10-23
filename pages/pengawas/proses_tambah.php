<?php
session_start();

require_once __DIR__ . '/../../inc/function.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Mengeksekusi fungsi tambah_petugas dari function.php
    if (tambah_pengawas($_POST, $_FILES, '../images/user_pengawas')) {
        echo "<script>
                alert('Data pengawas berhasil ditambahkan!');
                // [PERBAIKAN] Redirect relatif langsung ke index.php
                window.location.href = 'index.php?pages=tampil_pengawas';
              </script>";
        exit();
    } else {
        // Tangkap pesan error spesifik yang disimpan di SESSION
        $pesan_error = isset($_SESSION['Sess_FormErrors']) ? implode('\\n', $_SESSION['Sess_FormErrors']) : 'Gagal menambahkan data pengawas!';
        unset($_SESSION['Sess_FormErrors']); 
        
        echo "<script>
                alert('Gagal: " . addslashes($pesan_error) . "');
                window.location.href = 'index.php?pages=tambah_pengawas';
              </script>";
        exit();
    }
}
?>