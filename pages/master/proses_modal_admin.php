<?php
session_start();

// Perbaikan path relative require_once
require_once __DIR__ . '/../../inc/function.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah_modal'])) {

        if (!function_exists('tambah_admin')) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Fungsi tambah_admin tidak ditemukan'
            ]);
            exit;
        }

        // Folder target foto fisik
        $TARGET_FOLDER = __DIR__ . '/../../images/user_admin/';
        $result = tambah_admin($_POST, $_FILES, $TARGET_FOLDER);

        if ($result === true) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan!'
            ]);
        } else {
            $errorMessage = isset($_SESSION['Sess_FormErrors'])
                ? implode(', ', $_SESSION['Sess_FormErrors'])
                : 'Gagal menambahkan data!';

            unset($_SESSION['Sess_FormErrors']);

            echo json_encode([
                'status' => 'error',
                'message' => $errorMessage
            ]);
        }
        exit;
    }
}
?>