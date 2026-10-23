<?php
// Memeriksa jika belum ada sesi PHP yang berjalan, lalu jalankan session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memanggil file pustaka fungsi terpusat
require_once __DIR__ . '/../../inc/function.php';

// Memeriksa apakah tombol submit UpdateProfile telah diklik
if (isset($_POST['UpdateProfile'])) {

    // Mengeksekusi fungsi edit_admin dari function.php
    // (Path disesuaikan menjadi '../images/user_admin' karena dipanggil melalui router index.php)
    if (edit_admin($_POST, $_FILES, '../images/user_admin')) {
        
        echo "<script>
            alert('Data profil berhasil diperbarui!');
            window.location.href = 'index.php?pages=profile';
        </script>";
        exit();
        
    } else {
        // Menangkap pesan error dari Session yang dikirim oleh fungsi edit_admin
        $pesan_error = isset($_SESSION['Sess_FormErrors']) ? implode('\\n', $_SESSION['Sess_FormErrors']) : 'Gagal memperbarui data profil!';
        unset($_SESSION['Sess_FormErrors']); 
        
        echo "<script>
            alert('Gagal: " . addslashes($pesan_error) . "');
            window.history.back();
        </script>";
        exit();
    }

} else {
    // Pengalihan kembali jika halaman diakses secara langsung tanpa menekan tombol submit
    echo "<script>window.history.back();</script>";
    exit();
}
?>