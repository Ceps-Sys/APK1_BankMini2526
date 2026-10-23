<?php
// Memulai sesi PHP untuk mengakses variabel $_SESSION
session_start();

// Memuat file fungsi pendukung menggunakan path absolut agar tidak error
require_once __DIR__ . '/../../inc/function.php';

// Mengatur header agar respon dikembalikan dalam format 
header('Content-Type: application/json');

// Menyembunyikan error PHP dari output langsung agar tidak merusak format JSON
ini_set('display_errors', 0);
// Tetap mencatat/melaporkan semua tingkat error PHP`
error_reporting(E_ALL);

// Cek apakah metode HTTP request yang digunakan adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cek apakah parameter 'tambah_modal' dikirimkan dalam request
    if (isset($_POST['tambah_modal'])) {

        // Memastikan fungsi tambah_admin sudah didefinisikan sebelum dipanggil
        if (!function_exists('tambah_admin')) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Fungsi tambah_admin tidak ditemukan'
            ]);
            exit;
        }

        // Menentukan folder tujuan untuk menyimpan upload foto admin
        $TARGET_FOLDER = __DIR__ . '/../../images/user_admin/';
         
        // Memanggil fungsi tambah_admin dengan mengirim data form, data file, dan direktori target
        $result = tambah_admin($_POST, $_FILES, $TARGET_FOLDER);

        // Jika proses penambahan data berhasil
        if ($result === true) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan!'
            ]);
        } else {
            // Jika gagal, ambil pesan error dari session jika ada, atau gunakan pesan default
            $errorMessage = isset($_SESSION['Sess_FormErrors'])
                ? implode(', ', $_SESSION['Sess_FormErrors'])
                : 'Gagal menambahkan data!';

            // Hapus data error pada session setelah digunakan
            unset($_SESSION['Sess_FormErrors']);

            // Mengirim respon error dalam bentuk JSON
            echo json_encode([
                'status' => 'error',
                'message' => $errorMessage
            ]);
        }
        // Menghentikan eksekusi script setelah mengirim respon JSON
        exit;
    }
}
?>