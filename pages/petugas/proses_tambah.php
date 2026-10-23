<?php
session_start();

require_once __DIR__ . '/../../inc/function.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Mengeksekusi fungsi tambah_pengawas dari function.php
    if (tambah_petugas($_POST, $_FILES, '../images/user_pengawas')) {
        echo "<script>
                alert('Data petugas berhasil ditambahkan!');
                // [PERBAIKAN] Redirect relatif langsung ke index.php
                window.location.href = 'index.php?pages=tampil_petugas';
              </script>";
        exit();
    } else {
        // Tangkap pesan error spesifik yang disimpan di SESSION
        $pesan_error = isset($_SESSION['Sess_FormErrors']) ? implode('\\n', $_SESSION['Sess_FormErrors']) : 'Gagal menambahkan data pengawas!';
        unset($_SESSION['Sess_FormErrors']); 
        
        echo "<script>
                alert('Gagal: " . addslashes($pesan_error) . "');
                window.location.href = 'index.php?pages=tambah_petugas';
              </script>";
        exit();
    }
}
?>