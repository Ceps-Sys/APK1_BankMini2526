<?php
// Memeriksa status sesi aktif, jika belum ada maka jalankan session_start()[cite: 5]
if (!isset($_SESSION['Sess_Email'])) {
    // Alihkan pengguna ke halaman login jika tidak ada sesi email terdaftar[cite: 5]
    header("Location: ../../inc/login.php");
    // Hentikan eksekusi script[cite: 5]
    exit();
}

// Memanggil file fungsi pendukung[cite: 5]
require_once __DIR__ . '/../../inc/function.php';

// Memeriksa apakah terdapat parameter GET 'Id' pada URL[cite: 5]
if (isset($_GET['Id'])) {
    // Panggil variabel koneksi global[cite: 5]
    global $KONEKSI;
    // Sanitasi nilai ID dari URL untuk keamanan[cite: 5]
    $ID_USER = mysqli_real_escape_string($KONEKSI, $_GET['Id']);

    // Memanggil fungsi hapus_user dari file function.php[cite: 5]
    if (hapus_user($ID_USER)) {
        // Tampilkan pesan JavaScript berhasil hapus dan alihkan ke tabel akun_pengguna[cite: 5]
        echo "<script>
                alert('Data user berhasil dihapus!');
                window.location.href = 'index.php?pages=akun_pengguna';
              </script>";
    } else {
        // Tampilkan alert gagal jika penanganan fungsi mengembalikan nilai false[cite: 5]
        echo "<script>
                alert('Gagal menghapus data user!');
                window.location.href = 'index.php?pages=akun_pengguna';
              </script>";
    }
} else {
    // Pengalihan otomatis jika URL diakses tanpa membawa parameter Id[cite: 5]
    echo "<script>window.location.href = 'index.php?pages=akun_pengguna';</script>";
}
?>