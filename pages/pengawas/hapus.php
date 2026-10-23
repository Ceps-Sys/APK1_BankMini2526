<?php
// Memeriksa status sesi aktif, jika belum ada maka jalankan session_start()
if (!isset($_SESSION['Sess_Email'])) {
    header("Location: ../../inc/login.php");
    exit();
}

require_once __DIR__ . '/../../inc/function.php';

if (isset($_GET['Id'])) {
    global $KONEKSI;
    $ID_USER = mysqli_real_escape_string($KONEKSI, $_GET['Id']);

    // Mengeksekusi fungsi hapus khusus petugas
    if (hapus_pengawas($ID_USER)) {
        echo "<script>
                alert('Data petugas berhasil dihapus!');
                window.location.href = 'index.php?pages=tampil_petugas';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data petugas!');
                window.location.href = 'index.php?pages=tampil_petugas';
              </script>";
    }
} else {
    echo "<script>window.location.href = 'index.php?pages=tampil_petugas';</script>";
}
?>