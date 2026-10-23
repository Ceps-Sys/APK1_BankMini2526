<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../inc/function.php';

if (isset($_POST['UpdateProfile'])) {

    // Memanggil fungsi edit_pengawas khusus untuk pengawas
    if (edit_pengawas($_POST, $_FILES, '../images/user_pengawas')) {
        
        echo "<script>
            alert('Data profil Pengawas berhasil diperbarui!');
            window.location.href = 'index.php?pages=tampil_pengawas';
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