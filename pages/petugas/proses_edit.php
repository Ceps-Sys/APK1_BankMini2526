<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../inc/function.php';

if (isset($_POST['UpdateProfile'])) {

    // [PERBAIKAN] Gunakan fungsi edit_petugas dan arahkan ke folder user_petugas
    if (edit_petugas($_POST, $_FILES, '../images/user_petugas')) {
        
        echo "<script>
            alert('Data profil Petugas berhasil diperbarui!');
            // Sesuaikan redirect ini ke halaman tabel petugas
            window.location.href = 'index.php?pages=tampil_petugas';
        </script>";
        exit();
        
    } else {
        $pesan_error = isset($_SESSION['Sess_FormErrors']) ? implode('\\n', $_SESSION['Sess_FormErrors']) : 'Gagal memperbarui data profil!';
        unset($_SESSION['Sess_FormErrors']); 
        
        echo "<script>
            alert('Gagal: " . addslashes($pesan_error) . "');
            window.history.back();
        </script>";
        exit();
    }

} else {
    echo "<script>window.history.back();</script>";
    exit();
}
?>