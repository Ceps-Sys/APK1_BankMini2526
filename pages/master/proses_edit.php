<?php
// Memeriksa jika belum ada sesi PHP yang berjalan, lalu jalankan session_start()[cite: 4]
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memanggil file pustaka fungsi terpusat[cite: 4]
require_once __DIR__ . '/../../inc/function.php';
// Mengakses variabel koneksi MySQLi global[cite: 4]
global $KONEKSI;

// Memeriksa apakah tombol submit UpdateProfile telah diklik[cite: 4]
if (isset($_POST['UpdateProfile'])) {

    // Sanitasi input ID User dari karakter berbahaya SQL Injection[cite: 4]
    $id_user        = mysqli_real_escape_string($KONEKSI, $_POST['IdUser'] ?? '');
    // Sanitasi input ID Admin[cite: 4]
    $id_admin       = mysqli_real_escape_string($KONEKSI, $_POST['IdAdmin'] ?? '');
    // Sanitasi input Email[cite: 4]
    $email          = mysqli_real_escape_string($KONEKSI, $_POST['Email'] ?? '');
    // Sanitasi input Nama Admin[cite: 4]
    $nama_admin     = mysqli_real_escape_string($KONEKSI, $_POST['NamaAdmin'] ?? '');
    // Sanitasi input Alamat Admin[cite: 4]
    $alamat_admin   = mysqli_real_escape_string($KONEKSI, $_POST['AlamatAdmin'] ?? '');
    // Sanitasi input Nomor Telepon[cite: 4]
    $telepon        = mysqli_real_escape_string($KONEKSI, $_POST['TeleponAdmin'] ?? '');
    // Sanitasi input Jenis Kelamin dengan nilai default 'L'[cite: 4]
    $jengkel        = mysqli_real_escape_string($KONEKSI, $_POST['Jengkel'] ?? 'L');
    // Menangkap nilai nama foto lama dari field hidden[cite: 4]
    $foto_lama      = $_POST['FotoLama'] ?? '';
    // Menangkap nama avatar default dari input jika dipilah[cite: 4]
    $avatar_bawaan  = $_POST['AvatarBawaan'] ?? '';

    // 1. Menyusun perintah SQL pembaruan data pada tabel tbl_users[cite: 4]
    $sql_user = "UPDATE tbl_users SET 
                    email = '$email', 
                    updated_at = NOW() 
                 WHERE id_user = '$id_user'";
    // Eksekusi query pembaruan user ke database[cite: 4]
    $q_user = mysqli_query($KONEKSI, $sql_user);

    // 2. Menyusun perintah SQL pembaruan detail data pada tabel tbl_admin[cite: 4]
    $sql_admin = "UPDATE tbl_admin SET 
                    nama_admin = '$nama_admin', 
                    alamat_admin = '$alamat_admin', 
                    telepon_admin = '$telepon', 
                    jenis_kelamin = '$jengkel' 
                  WHERE id_user = '$id_user'";

    // Memeriksa jika id_admin terisi, tambahkan klausul OR pada query[cite: 4]
    if (!empty($id_admin)) {
        $sql_admin .= " OR id_admin = '$id_admin'";
    }
    // Eksekusi query pembaruan admin ke database[cite: 4]
    $q_admin = mysqli_query($KONEKSI, $sql_admin);

    // 3. Penanganan file unggahan foto profil[cite: 4]
    if (isset($_FILES['Photo']['name']) && $_FILES['Photo']['error'] === 0) {
        // Ambil ekstensi berkas file foto dan ubah ke huruf kecil[cite: 4]
        $ext = strtolower(pathinfo($_FILES['Photo']['name'], PATHINFO_EXTENSION));
        // Buat daftar format ekstensi yang diperbolehkan[cite: 4]
        $allowed = ['jpg', 'jpeg', 'png', 'bmp'];

        // Cek apakah ekstensi file termasuk dalam daftar allowed[cite: 4]
        if (in_array($ext, $allowed)) {
            // Format nama berkas baru yang unik dengan timestamp[cite: 4]
            $nama_foto_baru = time() . '_' . $id_user . '.' . $ext;
            // Tentukan jalur folder direktori upload target[cite: 4]
            $dir_upload = __DIR__ . '/../../images/user_admin/';

            // Pengecekan direktori, buat direktori baru jika folder belum ada[cite: 4]
            if (!file_exists($dir_upload)) {
                mkdir($dir_upload, 0777, true);
            }

            // Gabungkan direktori dan nama berkas foto[cite: 4]
            $path_upload = $dir_upload . $nama_foto_baru;

            // Pindahkan file dari lokasi temporary ke direktori upload[cite: 4]
            if (move_uploaded_file($_FILES['Photo']['tmp_name'], $path_upload)) {
                // Hapus berkas foto lama jika ada file fisiknya[cite: 4]
                if (!empty($foto_lama) && file_exists($dir_upload . $foto_lama)) {
                    unlink($dir_upload . $foto_lama);
                }

                // Update kolom path_photo_admin di tabel tbl_admin[cite: 4]
                mysqli_query($KONEKSI, "UPDATE tbl_admin SET path_photo_admin = '$nama_foto_baru' WHERE id_user = '$id_user'");
            }
        }
    } elseif (!empty($avatar_bawaan)) {
        // Jika memilih avatar bawaan, sanitasi teks avatar[cite: 4]
        $avatar_clean = mysqli_real_escape_string($KONEKSI, $avatar_bawaan);
        // Simpan referensi nama avatar ke database[cite: 4]
        mysqli_query($KONEKSI, "UPDATE tbl_admin SET path_photo_admin = '$avatar_clean' WHERE id_user = '$id_user'");
    }

    // 4. Memeriksa keberhasilan dari kedua query update[cite: 4]
    if ($q_user && $q_admin) {
        // Tampilkan pop-up berhasil dan alihkan halaman ke halaman profile[cite: 4]
        echo "<script>
            alert('Data profil berhasil diperbarui!');
            window.location.href = 'index.php?pages=profile';
        </script>";
        // Hentikan eksekusi script[cite: 4]
        exit();
    } else {
        // Tampilkan pesan error jika query gagal dan kembalikan ke halaman sebelumnya[cite: 4]
        echo "<script>
            alert('Gagal memperbarui data: " . mysqli_error($KONEKSI) . "');
            window.history.back();
        </script>";
        // Hentikan eksekusi script[cite: 4]
        exit();
    }

} else {
    // Pengalihan kembali jika halaman diakses secara langsung tanpa menekan tombol submit[cite: 4]
    echo "<script>window.history.back();</script>";
    exit();
}
?>