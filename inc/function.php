<?php
//+=+=========================================+=+
// Aplikasi Bank_Mini untuk siswa SMK MVP
// File ini berguna untuk menampung semua fungsi dan dapat digunakan untuk berbagai keperluan
//+=+=========================================+=+


//+=+=========================================+=+
// 1. PENGATURAN WAKTU APLIKASI
//+=+=========================================+=+

// Mengatur zona waktu standar aplikasi ke WIB (Asia/Jakarta)[cite: 6]
date_default_timezone_set('Asia/Jakarta');
// Menyimpan format tanggal dan waktu saat ini (YYYY-MM-DD HH:MM:SS) ke variabel global[cite: 6]
$WAKTU_SEKARANG = date('Y-m-d H:i:s');


//+=+=========================================+=+
// 2. KONEKSI DATABASE
//+=+=========================================+=+

// Menentukan konfigurasi server dan database[cite: 6]
$SERVERNAME = "localhost";
$USERNAME = "root";
$PASSWORD = "";
$DBNAME = "db_bankmini2526";

// Membuka koneksi baru ke server database MySQL menggunakan driver mysqli[cite: 6]
$KONEKSI = mysqli_connect($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME);

// Memeriksa apakah koneksi database gagal terhubung[cite: 6]
if (!$KONEKSI) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


//+=+=========================================+=+
// 3. FUNGSI AUTONUMBER (ID OTOMATIS)
//+=+=========================================+=+

/**
 * Membentuk ID otomatis berdasarkan parameter tabel, kolom, lebar digit, dan awalan.
 */
function autonumber($TABEL, $KOLOM, $LEBAR=0, $AWALAN='') {
    global $KONEKSI; 

    // Mengeksekusi query untuk mengambil ID terakhir berdasarkan awalan secara urutan menurun[cite: 6]
    $QUERY = mysqli_query($KONEKSI, "SELECT $KOLOM FROM $TABEL WHERE $KOLOM LIKE '$AWALAN%' ORDER BY $KOLOM DESC LIMIT 1") or die ('error:  ' . mysqli_error($KONEKSI));

    $JUMLAH_RECORD = mysqli_num_rows($QUERY);
    if ($JUMLAH_RECORD == 0) {
        $NOMOR_BARU = 1;
    } else {
        $ROW = mysqli_fetch_array($QUERY);
        // Memotong string awalan dan merubah sisa karakter angka menjadi integer lalu ditambah 1[cite: 6]
        $NOMOR_BARU = intval(substr($ROW[0], strlen($AWALAN))) + 1;
    }

    // Jika parameter lebar diisi, format nomor baru dengan penambahan angka nol di sebelah kiri[cite: 6]
    if ($LEBAR > 0) {
        $ID_BARU = $AWALAN . str_pad($NOMOR_BARU, $LEBAR, "0", STR_PAD_LEFT);
    } else {
        $ID_BARU = $AWALAN . $NOMOR_BARU;
    }
    
    return $ID_BARU;
}


//+=+=========================================+=+
// 4. FUNGSI REGISTRASI PENGGUNA BARU
//+=+=========================================+=+

/**
 * Memproses pendaftaran akun baru ke tabel tbl_users dan tabel terkait (misal tbl_admin).
 */
function registrasi($DATA) {
    global $KONEKSI, $WAKTU_SEKARANG;

    // Sanitasi input data
    $ID_USER = stripslashes($DATA["IdUser"]);
    $EMAIL = strtolower(stripslashes($DATA["Email"]));
    $PASSWORD = mysqli_real_escape_string($KONEKSI, $DATA["Password"]);
    $CONFIRM_PASSWORD = mysqli_real_escape_string($KONEKSI, $DATA["ConfirmPassword"]);

    // Cek duplikasi email[cite: 6]
    $RESULT = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email = '$EMAIL'") or die('Error: ' . mysqli_error($KONEKSI));
    if (mysqli_fetch_assoc($RESULT)) {
        echo "<script>alert('Email sudah terdaftar!');</script>";
        return false;
    }

    // Validasi kesesuaian password[cite: 6]
    if ($PASSWORD !== $CONFIRM_PASSWORD) {
        echo "<script>alert('Password dan Confirm Password tidak sama! cek lagi gih');</script>";
        return false;
    }

    // Hashing password[cite: 6]
    $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
    $LEVEL = isset($DATA['Role']) ? (int)$DATA['Role'] : 4; 

    // Simpan data ke tabel tbl_users[cite: 6]
    $QUERY = "INSERT INTO tbl_users (id_user, email, password, role, created_at, updated_at) 
              VALUES ('$ID_USER', '$EMAIL', '$PASSWORD_HASH', '$LEVEL', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";
              
    mysqli_query($KONEKSI, $QUERY) or die ('Gagal simpan data di tbl_users : ' . mysqli_error($KONEKSI));

    // Jika role adalah Admin (Level 1), masukkan juga ke tbl_admin[cite: 6]
    if ($LEVEL === 1) {
        $QUERY2 = "INSERT INTO tbl_admin (id_user, created_at, updated_at) 
                   VALUES ('$ID_USER', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";
        mysqli_query($KONEKSI, $QUERY2) or die ('Gagal simpan data di tbl_admin : ' . mysqli_error($KONEKSI));
    } 

    echo "<script>
        alert('Selamat bung anda berhasil regis, Silahkan login untuk lanjut!!');
        document.location.href = 'login.php';
        </script>";

    return mysqli_affected_rows($KONEKSI);
}


//+=+=========================================+=+
// 5. FUNGSI TAMPIL DATA (QUERY SELECT)
//+=+=========================================+=+

/**
 * Mengambil kumpulan data dari database berdasarkan string query SQL yang diberikan.
 */
function tampil($QUERY_STRING)
{
    global $KONEKSI; 

    $QUERY = mysqli_query($KONEKSI, $QUERY_STRING) or die('Error: ' . mysqli_error($KONEKSI));
    $HASIL_DATA = []; 

    while ($ROW = mysqli_fetch_assoc($QUERY)) {
        $HASIL_DATA[] = $ROW; 
    }

    return $HASIL_DATA; 
}


//+=+=========================================+=+
// 6. FUNGSI UPLOAD BERKAS / FILE
//+=+=========================================+=+

/**
 * Menangani pengunggahan berkas file (seperti foto profil) ke server dengan validasi tipe dan ukuran.
 */
function upload_files($FILE_INPUT, $IDENTIFIER, $TARGET_FOLDER = '', $OPTION = [])
{
    $DEFAULT = [
        'max_size' => 2 * 1024 * 1024, 
        'allowed_types' => ['jpg', 'png', 'gif', 'bmp'], 
        'create_folder' => true, 
        'prefix' => ''           
    ];

    $OPTIONS = array_merge($DEFAULT, $OPTION);

    if (!isset($FILE_INPUT['error']) || !isset($FILE_INPUT['tmp_name'])) {
        return [
            'success' => false,
            'message' => 'File yang di upload tidak valid. Pastikan form sudah diisi dengan benar.'
        ];
    }

    $ERRORS = $FILE_INPUT['error']; 
    $TMP_NAME = $FILE_INPUT['tmp_name']; 
    $NAMA_ASLI = $FILE_INPUT['name']; 
    $UKURAN_FILE = $FILE_INPUT['size']; 

    if ($ERRORS === UPLOAD_ERR_NO_FILE) {
        return [
            'success' => false,
            'message' => 'Tidak ada file yang diupload. Silahkan pilih file terlebih dahulu.'
        ];
    }

    if ($ERRORS !== UPLOAD_ERR_OK) {
        $ERROR_MESSAGES = [
            UPLOAD_ERR_INI_SIZE => 'File yang diupload melebihi batas ukuran server.',
            UPLOAD_ERR_FORM_SIZE => 'File yang diupload melebihi batas ukuran Form.',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara tidak ditemukan.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk.',
            UPLOAD_ERR_EXTENSION => 'Upload file dihentikan oleh ekstensi PHP.'
        ];

        $MESSAGE = isset($ERROR_MESSAGES[$ERRORS]) ? $ERROR_MESSAGES[$ERRORS] : 'Terjadi kesalahan saat mengupload file.';
        return ['success' => false, 'message' => $MESSAGE];
    }

    $EKSTENSI_FILE = strtolower(pathinfo($NAMA_ASLI, PATHINFO_EXTENSION)); 

    if (!in_array($EKSTENSI_FILE, $OPTIONS['allowed_types'])) {
        $EXT_STR = implode(', ', $OPTIONS['allowed_types']);
        return [
            'success' => false,
            'message' => "Ekstensi file tidak diizinkan. Hanya format: $EXT_STR."
        ];
    }

    if ($UKURAN_FILE > $OPTIONS['max_size']) {
        $MAX_SIZE_MB = number_format($OPTIONS['max_size'] / 1048576, 2); 
        return [
            'success' => false,
            'message' => "Ukuran file melebihi batas maksimal ($MAX_SIZE_MB MB)."
        ];
    }

    $PREFIX = $OPTIONS['prefix'] ? $OPTIONS['prefix'] . '_' : ''; 
    $UNIQUE_ID = uniqid(); 
    $TIMESTAMP = date('Ymd_His'); 
    $NAMA_FILE_BARU = $PREFIX . $IDENTIFIER . $TIMESTAMP . $UNIQUE_ID . '.' . $EKSTENSI_FILE; 
    $FILE_PATH = rtrim($TARGET_FOLDER, '/') . '/' . $NAMA_FILE_BARU;

    if (move_uploaded_file($TMP_NAME, $FILE_PATH)) {
        return [
            'success' => true,                   
            'message' => 'File berhasil upload.',
            'Filename_asli' => $NAMA_ASLI,        
            'Filename_tersimpan' => $NAMA_FILE_BARU,
            'path' => $FILE_PATH,                  
            'size' => $UKURAN_FILE,                 
            'size_mb' => round($UKURAN_FILE / 1048576, 2),
            'extension' => $EKSTENSI_FILE,               
            'identifier' => $IDENTIFIER,                
            'prefix' => $OPTIONS['prefix']              
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Gagal memindahkan file ke folder tujuan.',
        ];
    }
}


//+=+=========================================+=+
// 7. FUNGSI TAMBAH DATA ADMIN
//+=+=========================================+=+

/**
 * Menambahkan data akun admin baru beserta upload foto profil dan relasi tabelnya.
 */
function tambah_admin($DATA, $FILES, $TARGET_FOLDER = "../images/user_admin")
{
    global $KONEKSI, $WAKTU_SEKARANG; 

    $ID_ADMIN      = htmlspecialchars(trim($DATA["IdAdmin"] ?? ''));
    $NAMA_ADMIN    = htmlspecialchars(trim($DATA["NamaAdmin"] ?? ''));
    $ALAMAT_ADMIN  = htmlspecialchars(trim($DATA["AlamatAdmin"] ?? ''));
    $TELEPON_ADMIN = htmlspecialchars(trim($DATA["TeleponAdmin"] ?? ''));
    $JENGKEL_RAW   = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    $EMAIL         = htmlspecialchars(trim($DATA["Email"] ?? ''));
    $PASSWORD      = htmlspecialchars(trim($DATA["Password"] ?? ''));
    $PASSWORD2     = htmlspecialchars(trim($DATA["Password2"] ?? ''));

    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    $ERRORS = []; 
    if (empty($ID_ADMIN))      $ERRORS[] = "ID Admin tidak boleh kosong.";
    if (empty($NAMA_ADMIN))    $ERRORS[] = "Nama Admin tidak boleh kosong.";
    if (empty($ALAMAT_ADMIN))  $ERRORS[] = "Alamat Admin tidak boleh kosong.";
    if (empty($TELEPON_ADMIN)) $ERRORS[] = "Telepon Admin tidak boleh kosong.";
    if (empty($EMAIL))         $ERRORS[] = "Email Admin tidak boleh kosong.";
    if (empty($PASSWORD))      $ERRORS[] = "Password Admin tidak boleh kosong.";
    if (empty($PASSWORD2))     $ERRORS[] = "Konfirmasi Password Admin tidak boleh kosong.";

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS; 
        return false; 
    }

    $RESULT = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email ='$EMAIL'") or die('Error: ' . mysqli_error($KONEKSI));
    if (mysqli_fetch_assoc($RESULT)) {
        $_SESSION['Sess_FormErrors'] = ["Email Admin yang diinput sudah terdaftar."]; 
        return false; 
    }

    if ($PASSWORD !== $PASSWORD2) {
        $_SESSION['Sess_FormErrors'] = ["Password dan Konfirmasi Password Admin tidak sama."]; 
        return false; 
    }

    if (strlen($PASSWORD) < 6) {
        $_SESSION['Sess_FormErrors'] = ["Password Admin minimal 6 karakter."]; 
        return false; 
    }

    $GAMBAR_FOTO = ""; 
    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_ADMIN,
            $TARGET_FOLDER,
            [
                'max_size' => 2097152, 
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'], 
                'create_folder' => true, 
                'prefix' => 'ADM' 
            ]
        );

        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
    mysqli_begin_transaction($KONEKSI);

    try {
        $LEVEL_ADMIN = 1;
        $SQL_USER = "INSERT INTO tbl_users (id_user, email, password, role, created_at, updated_at) 
                     VALUES ('$ID_ADMIN', '$EMAIL', '$PASSWORD_HASH', '$LEVEL_ADMIN', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal menambahkan user: " . mysqli_error($KONEKSI));
        }

        $SQL_ADMIN = "INSERT INTO tbl_admin (nama_admin, telepon_admin, alamat_admin, jenis_kelamin, path_photo_admin, id_user, created_at, updated_at) 
                      VALUES ('$NAMA_ADMIN', '$TELEPON_ADMIN', '$ALAMAT_ADMIN', '$JENGKEL', '$GAMBAR_FOTO', '$ID_ADMIN', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        if (!mysqli_query($KONEKSI, $SQL_ADMIN)) {
            throw new Exception("Gagal menambahkan admin: " . mysqli_error($KONEKSI));
        }

        mysqli_commit($KONEKSI);
        $_SESSION['Sess_SuccessMessage'] = "Data berhasil ditambahkan!";
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        if (!empty($GAMBAR_FOTO)) {
            $FILE_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($FILE_PATH)) { unlink($FILE_PATH); }
        }
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}


//+=+=========================================+=+
// 8. FUNGSI EDIT DATA ADMIN
//+=+=========================================+=+

/**
 * Memperbarui informasi data profil admin dan kredensial akun utamanya.
 */
function edit_admin($DATA, $FILES, $TARGET_FOLDER = "../images/user_admin")
{
    global $KONEKSI, $WAKTU_SEKARANG; 

    $ID_ADMIN      = htmlspecialchars(trim($DATA["IdAdmin"] ?? ''));
    $ID_USER       = htmlspecialchars(trim($DATA["IdUser"] ?? ''));
    $NAMA_ADMIN    = htmlspecialchars(trim($DATA["NamaAdmin"] ?? ''));
    $ALAMAT_ADMIN  = htmlspecialchars(trim($DATA["AlamatAdmin"] ?? ''));
    $TELEPON_ADMIN = htmlspecialchars(trim($DATA["TeleponAdmin"] ?? ''));
    $JENGKEL_RAW   = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    $EMAIL         = htmlspecialchars(trim($DATA["Email"] ?? ''));
    $PASSWORD      = htmlspecialchars(trim($DATA["Password"] ?? ''));
    $PASSWORD2     = htmlspecialchars(trim($DATA["Password2"] ?? ''));
    $FOTO_LAMA     = htmlspecialchars(trim($DATA["FotoLama"] ?? ''));

    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    $ERRORS = []; 
    if (empty($ID_USER))       $ERRORS[] = "ID User tidak valid.";
    if (empty($NAMA_ADMIN))    $ERRORS[] = "Nama Admin tidak boleh kosong.";
    if (empty($ALAMAT_ADMIN))  $ERRORS[] = "Alamat Admin tidak boleh kosong.";
    if (empty($TELEPON_ADMIN)) $ERRORS[] = "Telepon Admin tidak boleh kosong.";
    if (empty($EMAIL))         $ERRORS[] = "Email Admin tidak boleh kosong.";

    if (!empty($PASSWORD)) {
        if ($PASSWORD !== $PASSWORD2) { $ERRORS[] = "Password dan Konfirmasi Password tidak sama."; }
        if (strlen($PASSWORD) < 6) { $ERRORS[] = "Password minimal 6 karakter."; }
    }

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS;
        return false; 
    }

    $CEK_EMAIL = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email = '$EMAIL' AND id_user != '$ID_USER'");
    if (mysqli_fetch_assoc($CEK_EMAIL)) {
        $_SESSION['Sess_FormErrors'] = ["Email sudah digunakan."]; 
        return false; 
    }

    $GAMBAR_FOTO = $FOTO_LAMA; 
    $FOTO_BARU_UPLOADED = false;

    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_USER,
            $TARGET_FOLDER,
            [
                'max_size' => 52428800,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'],
                'create_folder' => true,
                'prefix' => 'ADM'
            ]
        );

        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
            $FOTO_BARU_UPLOADED = true;
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    mysqli_begin_transaction($KONEKSI);

    try {
        $SQL_PASSWORD = "";
        if (!empty($PASSWORD)) {
            $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
            $SQL_PASSWORD = ", password = '$PASSWORD_HASH'";
        }

        $SQL_USER = "UPDATE tbl_users SET email = '$EMAIL' $SQL_PASSWORD, updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";
        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal memperbarui data user: " . mysqli_error($KONEKSI));
        }

        $SQL_ADMIN = "UPDATE tbl_admin SET nama_admin = '$NAMA_ADMIN', telepon_admin = '$TELEPON_ADMIN', alamat_admin = '$ALAMAT_ADMIN', jenis_kelamin = '$JENGKEL', path_photo_admin = '$GAMBAR_FOTO', updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";
        if (!mysqli_query($KONEKSI, $SQL_ADMIN)) {
            throw new Exception("Gagal memperbarui data admin: " . mysqli_error($KONEKSI));
        }

        mysqli_commit($KONEKSI);

        if ($FOTO_BARU_UPLOADED && !empty($FOTO_LAMA)) {
            $OLD_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $FOTO_LAMA;
            if (file_exists($OLD_PATH)) { unlink($OLD_PATH); }
        }

        $_SESSION['Sess_SuccessMessage'] = "Data berhasil diperbarui!";
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        if ($FOTO_BARU_UPLOADED && !empty($GAMBAR_FOTO)) {
            $NEW_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($NEW_PATH)) { unlink($NEW_PATH); }
        }
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}


//+=+=========================================+=+
// 9. FUNGSI HAPUS USER ADMIN / NASABAH
//+=+=========================================+=+

/**
 * Menghapus akun pengguna admin beserta file fisik foto dan relasi tabelnya.
 */
function hapus_user($ID_USER)
{
    global $KONEKSI;
    mysqli_begin_transaction($KONEKSI);

    try {
        $CEK_FOTO = mysqli_query($KONEKSI, "SELECT path_photo_admin FROM tbl_admin WHERE id_user = '$ID_USER'");
        
        if ($CEK_FOTO && mysqli_num_rows($CEK_FOTO) > 0) {
            $ROW_FOTO = mysqli_fetch_assoc($CEK_FOTO);
            $NAMA_FOTO = $ROW_FOTO['path_photo_admin'];
            $AVATAR_BAWAAN = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
            
            if (!empty($NAMA_FOTO) && !in_array($NAMA_FOTO, $AVATAR_BAWAAN)) {
                $PATH_FOTO_ADMIN = __DIR__ . "/../images/user_admin/" . $NAMA_FOTO;
                if (file_exists($PATH_FOTO_ADMIN)) { unlink($PATH_FOTO_ADMIN); }
            }
        }

        mysqli_query($KONEKSI, "DELETE FROM tbl_admin WHERE id_user = '$ID_USER'");
        mysqli_query($KONEKSI, "DELETE FROM nasabah WHERE id_nasabah = '$ID_USER'");

        $HAPUS_UTAMA = mysqli_query($KONEKSI, "DELETE FROM tbl_users WHERE id_user = '$ID_USER'");
        if (!$HAPUS_UTAMA) {
            throw new Exception("Gagal menghapus data dari tbl_users.");
        }

        mysqli_commit($KONEKSI);
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        return false;
    }
}


//+=+=========================================+=+
// 10. FUNGSI GANTI PASSWORD (VIA MODAL)
//+=+=========================================+=+

/**
 * Memproses verifikasi password lama, validasi aturan password baru, dan memperbarui hash password di database.
 */
function ganti_password($DATA)
{
    global $KONEKSI;        
    global $WAKTU_SEKARANG; 

    $ID_USER           = mysqli_real_escape_string($KONEKSI, $DATA['IdUserPassword'] ?? '');
    $PASS_LAMA         = $DATA['PassLama'] ?? '';         
    $PASS_BARU         = $DATA['PassBaru'] ?? '';         
    $PASS_BARU_CONFIRM = $DATA['PassBaruConfirm'] ?? ''; 

    $ERRORS = []; 
    if (empty($ID_USER))           $ERRORS[] = "ID User tidak ditemukan!";                 
    if (empty($PASS_LAMA))         $ERRORS[] = "Password lama tidak boleh kosong!";         
    if (empty($PASS_BARU))         $ERRORS[] = "Password baru tidak boleh kosong!";         
    if (empty($PASS_BARU_CONFIRM)) $ERRORS[] = "Konfirmasi password tidak boleh kosong!"; 

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS; 
        return false;                          
    }

    $SQL_CEK    = "SELECT password FROM tbl_users WHERE id_user = '$ID_USER'";
    $RESULT_CEK = mysqli_query($KONEKSI, $SQL_CEK);

    if (mysqli_num_rows($RESULT_CEK) == 0) {
        $_SESSION['Sess_FormErrors'] = ["User tidak ditemukan di database!"]; 
        return false;
    }

    $USER_DATA = mysqli_fetch_assoc($RESULT_CEK); 

    if (!password_verify($PASS_LAMA, $USER_DATA['password'])) {
        $_SESSION['Sess_FormErrors'] = ["Password lama yang Anda masukkan SALAH!"]; 
        return false;
    }

    if (strlen($PASS_BARU) < 6) {
        $_SESSION['Sess_FormErrors'] = ["Password baru minimal 6 karakter!"]; 
        return false;
    }

    if ($PASS_BARU !== $PASS_BARU_CONFIRM) {
        $_SESSION['Sess_FormErrors'] = ["Konfirmasi password baru tidak sesuai!"]; 
        return false;
    }

    if (password_verify($PASS_BARU, $USER_DATA['password'])) {
        $_SESSION['Sess_FormErrors'] = ["Password baru tidak boleh sama dengan password lama!"]; 
        return false;
    }

    $PASSWORD_HASH = password_hash($PASS_BARU, PASSWORD_DEFAULT);
    $SQL_UPDATE = "UPDATE tbl_users SET password = '$PASSWORD_HASH', updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";

    if (mysqli_query($KONEKSI, $SQL_UPDATE)) {
        if (mysqli_affected_rows($KONEKSI) > 0) {
            $_SESSION['Sess_SuccessMessage'] = "Password berhasil diubah!"; 
            return true;                                                    
        } else {
            $_SESSION['Sess_FormErrors'] = ["Tidak ada perubahan pada password."]; 
            return false;
        }
    } else {
        $_SESSION['Sess_FormErrors'] = ["Gagal mengupdate password: " . mysqli_error($KONEKSI)];
        return false;
    }
}


//+=+=========================================+=+
// 11. FUNGSI TAMBAH DATA PETUGAS
//+=+=========================================+=+

/**
 * Menambahkan data akun petugas baru beserta unggahan foto dan pencatatan relasi tabel.
 */
function tambah_petugas($DATA, $FILES, $TARGET_FOLDER = "../images/user_petugas")
{
    global $KONEKSI, $WAKTU_SEKARANG; 

    $ID_PETUGAS      = htmlspecialchars(trim($DATA["IdPetugas"] ?? ''));
    $NAMA_PETUGAS    = htmlspecialchars(trim($DATA["NamaPetugas"] ?? ''));
    $ALAMAT_PETUGAS  = htmlspecialchars(trim($DATA["AlamatPetugas"] ?? ''));
    $TELEPON_PETUGAS = htmlspecialchars(trim($DATA["TeleponPetugas"] ?? ''));
    $JENGKEL_RAW     = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    $EMAIL           = htmlspecialchars(trim($DATA["Email"] ?? ''));
    $PASSWORD        = htmlspecialchars(trim($DATA["Password"] ?? ''));
    $PASSWORD2       = htmlspecialchars(trim($DATA["Password2"] ?? ''));
    $ROLE            = isset($DATA["Role"]) ? (int)$DATA["Role"] : 3;

    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    $ERRORS = []; 
    if (empty($ID_PETUGAS))      $ERRORS[] = "ID Petugas tidak boleh kosong.";
    if (empty($NAMA_PETUGAS))    $ERRORS[] = "Nama Petugas tidak boleh kosong.";
    if (empty($ALAMAT_PETUGAS))  $ERRORS[] = "Alamat Petugas tidak boleh kosong.";
    if (empty($TELEPON_PETUGAS)) $ERRORS[] = "Telepon Petugas tidak boleh kosong.";
    if (empty($EMAIL))           $ERRORS[] = "Email Petugas tidak boleh kosong.";
    if (empty($PASSWORD))        $ERRORS[] = "Password Petugas tidak boleh kosong.";
    if (empty($PASSWORD2))       $ERRORS[] = "Konfirmasi Password Petugas tidak boleh kosong.";

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS; 
        return false; 
    }

    $RESULT = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email ='$EMAIL'") or die('Error: ' . mysqli_error($KONEKSI));
    if (mysqli_fetch_assoc($RESULT)) {
        $_SESSION['Sess_FormErrors'] = ["Email Petugas yang diinput sudah terdaftar."]; 
        return false; 
    }

    if ($PASSWORD !== $PASSWORD2) {
        $_SESSION['Sess_FormErrors'] = ["Password dan Konfirmasi Password Petugas tidak sama."]; 
        return false; 
    }

    if (strlen($PASSWORD) < 6) {
        $_SESSION['Sess_FormErrors'] = ["Password Petugas minimal 6 karakter."]; 
        return false; 
    }

    $GAMBAR_FOTO = ""; 
    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_PETUGAS,
            $TARGET_FOLDER,
            [
                'max_size' => 2097152, 
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'], 
                'create_folder' => true, 
                'prefix' => 'PTG' 
            ]
        );

        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
    mysqli_begin_transaction($KONEKSI);

    try {
        $SQL_USER = "INSERT INTO tbl_users (id_user, email, password, role, created_at, updated_at) 
                     VALUES ('$ID_PETUGAS', '$EMAIL', '$PASSWORD_HASH', '$ROLE', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal menambahkan user: " . mysqli_error($KONEKSI));
        }

        $SQL_PETUGAS = "INSERT INTO tbl_petugas (nama_petugas, telepon_petugas, alamat_petugas, jenkel_petugas, path_photo_petugas, id_user, created_at, updated_at) 
              VALUES ('$NAMA_PETUGAS', '$TELEPON_PETUGAS', '$ALAMAT_PETUGAS', '$JENGKEL', '$GAMBAR_FOTO', '$ID_PETUGAS', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        if (!mysqli_query($KONEKSI, $SQL_PETUGAS)) {
            throw new Exception("Gagal menambahkan detail Petugas: " . mysqli_error($KONEKSI));
        }

        mysqli_commit($KONEKSI);
        $_SESSION['Sess_SuccessMessage'] = "Data Petugas berhasil ditambahkan!";
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        if (!empty($GAMBAR_FOTO)) {
            $FILE_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($FILE_PATH)) { unlink($FILE_PATH); }
        }
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}


//+=+=========================================+=+
// 12. FUNGSI EDIT DATA PETUGAS
//+=+=========================================+=+

/**
 * Memperbarui data profil petugas beserta pengelolaan file foto dan kredensialnya.
 */
function edit_petugas($DATA, $FILES, $TARGET_FOLDER = "../images/user_petugas")
{
    global $KONEKSI, $WAKTU_SEKARANG; 

    $ID_USER         = htmlspecialchars(trim($DATA["IdUser"] ?? ''));
    $NAMA_PETUGAS    = htmlspecialchars(trim($DATA["NamaPetugas"] ?? ''));
    $ALAMAT_PETUGAS  = htmlspecialchars(trim($DATA["AlamatPetugas"] ?? ''));
    $TELEPON_PETUGAS = htmlspecialchars(trim($DATA["TeleponPetugas"] ?? ''));
    $JENGKEL_RAW     = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    $EMAIL           = htmlspecialchars(trim($DATA["Email"] ?? ''));
    $PASSWORD        = htmlspecialchars(trim($DATA["Password"] ?? ''));
    $PASSWORD2       = htmlspecialchars(trim($DATA["Password2"] ?? ''));
    $FOTO_LAMA       = htmlspecialchars(trim($DATA["FotoLama"] ?? ''));

    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    $ERRORS = []; 
    if (empty($ID_USER))         $ERRORS[] = "ID User tidak valid.";
    if (empty($NAMA_PETUGAS))    $ERRORS[] = "Nama Petugas tidak boleh kosong.";
    if (empty($ALAMAT_PETUGAS))  $ERRORS[] = "Alamat Petugas tidak boleh kosong.";
    if (empty($TELEPON_PETUGAS)) $ERRORS[] = "Telepon Petugas tidak boleh kosong.";
    if (empty($EMAIL))           $ERRORS[] = "Email Petugas tidak boleh kosong.";

    if (!empty($PASSWORD)) {
        if ($PASSWORD !== $PASSWORD2) { $ERRORS[] = "Password dan Konfirmasi Password tidak sama."; }
        if (strlen($PASSWORD) < 6) { $ERRORS[] = "Password minimal 6 karakter."; }
    }

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS;
        return false; 
    }

    $CEK_EMAIL = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email = '$EMAIL' AND id_user != '$ID_USER'");
    if (mysqli_fetch_assoc($CEK_EMAIL)) {
        $_SESSION['Sess_FormErrors'] = ["Email sudah digunakan."]; 
        return false; 
    }

    $GAMBAR_FOTO = $FOTO_LAMA; 
    $FOTO_BARU_UPLOADED = false;

    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_USER,
            $TARGET_FOLDER,
            [
                'max_size' => 2428800,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'],
                'create_folder' => true,
                'prefix' => 'PTG' 
            ]
        );

        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
            $FOTO_BARU_UPLOADED = true;
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    mysqli_begin_transaction($KONEKSI);

    try {
        $SQL_PASSWORD = "";
        if (!empty($PASSWORD)) {
            $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
            $SQL_PASSWORD = ", password = '$PASSWORD_HASH'";
        }

        $SQL_USER = "UPDATE tbl_users SET email = '$EMAIL' $SQL_PASSWORD, updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";
        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal memperbarui data user: " . mysqli_error($KONEKSI));
        }

        $SQL_PETUGAS = "UPDATE tbl_petugas SET nama_petugas = '$NAMA_PETUGAS', telepon_petugas = '$TELEPON_PETUGAS', alamat_petugas = '$ALAMAT_PETUGAS', jenkel_petugas = '$JENGKEL', path_photo_petugas = '$GAMBAR_FOTO', updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";
        if (!mysqli_query($KONEKSI, $SQL_PETUGAS)) {
            throw new Exception("Gagal memperbarui data petugas: " . mysqli_error($KONEKSI));
        }

        mysqli_commit($KONEKSI);

        if ($FOTO_BARU_UPLOADED && !empty($FOTO_LAMA)) {
            $OLD_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $FOTO_LAMA;
            if (file_exists($OLD_PATH)) { unlink($OLD_PATH); }
        }

        $_SESSION['Sess_SuccessMessage'] = "Data Petugas berhasil diperbarui!";
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        if ($FOTO_BARU_UPLOADED && !empty($GAMBAR_FOTO)) {
            $NEW_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($NEW_PATH)) { unlink($NEW_PATH); }
        }
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}


//+=+=========================================+=+
// 13. FUNGSI HAPUS DATA PETUGAS
//+=+=========================================+=+

/**
 * Menghapus data akun petugas beserta file foto fisiknya dari folder server.
 */
function hapus_petugas($ID_USER)
{
    global $KONEKSI;
    mysqli_begin_transaction($KONEKSI);

    try {
        $CEK_FOTO = mysqli_query($KONEKSI, "SELECT path_photo_petugas FROM tbl_petugas WHERE id_user = '$ID_USER'");
        
        if ($CEK_FOTO && mysqli_num_rows($CEK_FOTO) > 0) {
            $ROW_FOTO = mysqli_fetch_assoc($CEK_FOTO);
            $NAMA_FOTO = $ROW_FOTO['path_photo_petugas'];
            $AVATAR_BAWAAN = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
            
            if (!empty($NAMA_FOTO) && !in_array($NAMA_FOTO, $AVATAR_BAWAAN)) {
                $PATH_FOTO_PETUGAS = __DIR__ . "/../images/user_petugas/" . $NAMA_FOTO;
                if (file_exists($PATH_FOTO_PETUGAS)) { unlink($PATH_FOTO_PETUGAS); }
            }
        }

        mysqli_query($KONEKSI, "DELETE FROM tbl_petugas WHERE id_user = '$ID_USER'");

        $HAPUS_UTAMA = mysqli_query($KONEKSI, "DELETE FROM tbl_users WHERE id_user = '$ID_USER'");
        if (!$HAPUS_UTAMA) {
            throw new Exception("Gagal menghapus data dari tbl_users.");
        }

        mysqli_commit($KONEKSI);
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        return false;
    }
}


//+=+=========================================+=+
// 14. FUNGSI TAMBAH DATA PENGAWAS
//+=+=========================================+=+

/**
 * Menambahkan data akun pengawas baru (role = 2) beserta file foto dan detail profilnya.
 */
function tambah_pengawas($DATA, $FILES, $TARGET_FOLDER = "../images/user_pengawas")
{
    global $KONEKSI, $WAKTU_SEKARANG; 

    $ID_PENGAWAS      = htmlspecialchars(trim($DATA["IdPengawas"] ?? ''));
    $NAMA_PENGAWAS    = htmlspecialchars(trim($DATA["NamaPengawas"] ?? ''));
    $ALAMAT_PENGAWAS  = htmlspecialchars(trim($DATA["AlamatPengawas"] ?? ''));
    $TELEPON_PENGAWAS = htmlspecialchars(trim($DATA["TeleponPengawas"] ?? ''));
    $JENGKEL_RAW      = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    $EMAIL            = htmlspecialchars(trim($DATA["Email"] ?? ''));
    $PASSWORD         = htmlspecialchars(trim($DATA["Password"] ?? ''));
    $PASSWORD2        = htmlspecialchars(trim($DATA["Password2"] ?? ''));

    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    $ERRORS = []; 
    if (empty($ID_PENGAWAS))      $ERRORS[] = "ID Pengawas tidak boleh kosong.";
    if (empty($NAMA_PENGAWAS))    $ERRORS[] = "Nama Pengawas tidak boleh kosong.";
    if (empty($ALAMAT_PENGAWAS))  $ERRORS[] = "Alamat Pengawas tidak boleh kosong.";
    if (empty($TELEPON_PENGAWAS)) $ERRORS[] = "Telepon Pengawas tidak boleh kosong.";
    if (empty($EMAIL))            $ERRORS[] = "Email Pengawas tidak boleh kosong.";
    if (empty($PASSWORD))         $ERRORS[] = "Password Pengawas tidak boleh kosong.";
    if (empty($PASSWORD2))        $ERRORS[] = "Konfirmasi Password Pengawas tidak boleh kosong.";

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS; 
        return false; 
    }

    $RESULT = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email ='$EMAIL'") or die('Error: ' . mysqli_error($KONEKSI));
    if (mysqli_fetch_assoc($RESULT)) {
        $_SESSION['Sess_FormErrors'] = ["Email Pengawas yang diinput sudah terdaftar."]; 
        return false; 
    }

    if ($PASSWORD !== $PASSWORD2) {
        $_SESSION['Sess_FormErrors'] = ["Password dan Konfirmasi Password Pengawas tidak sama."]; 
        return false; 
    }

    if (strlen($PASSWORD) < 6) {
        $_SESSION['Sess_FormErrors'] = ["Password Pengawas minimal 6 karakter."]; 
        return false; 
    }

    $GAMBAR_FOTO = "";  
    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_PENGAWAS,
            $TARGET_FOLDER,
            [
                'max_size'      => 2097152, 
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'], 
                'create_folder' => true, 
                'prefix'        => 'PGW' 
            ]
        );

        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
    mysqli_begin_transaction($KONEKSI);

    try {
        $LEVEL_PENGAWAS = 2;

        $SQL_USER = "INSERT INTO tbl_users (id_user, email, password, role, created_at, updated_at) 
                     VALUES ('$ID_PENGAWAS', '$EMAIL', '$PASSWORD_HASH', '$LEVEL_PENGAWAS', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal menambahkan user: " . mysqli_error($KONEKSI));
        }

        $SQL_PENGAWAS = "INSERT INTO tbl_pengawas (nama_pengawas, telepon_pengawas, alamat_pengawas, jenkel_pengawas, path_photo_pengawas, id_user, created_at, updated_at) 
                         VALUES ('$NAMA_PENGAWAS', '$TELEPON_PENGAWAS', '$ALAMAT_PENGAWAS', '$JENGKEL', '$GAMBAR_FOTO', '$ID_PENGAWAS', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        if (!mysqli_query($KONEKSI, $SQL_PENGAWAS)) {
            throw new Exception("Gagal menambahkan pengawas: " . mysqli_error($KONEKSI));
        }

        mysqli_commit($KONEKSI);
        $_SESSION['Sess_SuccessMessage'] = "Data berhasil ditambahkan!";
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        if (!empty($GAMBAR_FOTO)) {
            $FILE_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($FILE_PATH)) { unlink($FILE_PATH); }
        }
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}


//+=+=========================================+=+
// 15. FUNGSI EDIT DATA PENGAWAS
//+=+=========================================+=+

/**
 * Memperbarui data profil pengawas, kredensial akun, serta penanganan file foto barunya.
 */
function edit_pengawas($DATA, $FILES, $TARGET_FOLDER = "../images/user_pengawas")
{
    global $KONEKSI, $WAKTU_SEKARANG; 

    $ID_USER         = htmlspecialchars(trim($DATA["IdUser"] ?? ''));
    $NAMA_PENGAWAS   = htmlspecialchars(trim($DATA["NamaPengawas"] ?? ''));
    $ALAMAT_PENGAWAS = htmlspecialchars(trim($DATA["AlamatPengawas"] ?? ''));
    $TELEPON_PENGAWAS= htmlspecialchars(trim($DATA["TeleponPengawas"] ?? ''));
    $JENGKEL_RAW     = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    $EMAIL           = htmlspecialchars(trim($DATA["Email"] ?? ''));
    $PASSWORD        = htmlspecialchars(trim($DATA["Password"] ?? ''));
    $PASSWORD2       = htmlspecialchars(trim($DATA["Password2"] ?? ''));
    $FOTO_LAMA       = htmlspecialchars(trim($DATA["FotoLama"] ?? ''));

    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    $ERRORS = []; 
    if (empty($ID_USER))         $ERRORS[] = "ID User tidak valid.";
    if (empty($NAMA_PENGAWAS))   $ERRORS[] = "Nama Pengawas tidak boleh kosong.";
    if (empty($ALAMAT_PENGAWAS)) $ERRORS[] = "Alamat Pengawas tidak boleh kosong.";
    if (empty($TELEPON_PENGAWAS))$ERRORS[] = "Telepon Pengawas tidak boleh kosong.";
    if (empty($EMAIL))           $ERRORS[] = "Email Pengawas tidak boleh kosong.";

    if (!empty($PASSWORD)) {
        if ($PASSWORD !== $PASSWORD2) { $ERRORS[] = "Password dan Konfirmasi Password tidak sama."; }
        if (strlen($PASSWORD) < 6) { $ERRORS[] = "Password minimal 6 karakter."; }
    }

    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS;
        return false; 
    }

    $CEK_EMAIL = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email = '$EMAIL' AND id_user != '$ID_USER'");
    if (mysqli_fetch_assoc($CEK_EMAIL)) {
        $_SESSION['Sess_FormErrors'] = ["Email sudah digunakan."]; 
        return false; 
    }

    $GAMBAR_FOTO = $FOTO_LAMA; 
    $FOTO_BARU_UPLOADED = false;

    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_USER,
            $TARGET_FOLDER,
            [
                'max_size' => 2428800,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'],
                'create_folder' => true,
                'prefix' => 'PGW' 
            ]
        );

        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
            $FOTO_BARU_UPLOADED = true;
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    mysqli_begin_transaction($KONEKSI);

    try {
        $SQL_PASSWORD = "";
        if (!empty($PASSWORD)) {
            $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
            $SQL_PASSWORD = ", password = '$PASSWORD_HASH'";
        }

        $SQL_USER = "UPDATE tbl_users SET email = '$EMAIL' $SQL_PASSWORD, updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";
        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal memperbarui data user: " . mysqli_error($KONEKSI));
        }

        $SQL_PENGAWAS = "UPDATE tbl_pengawas SET nama_pengawas = '$NAMA_PENGAWAS', telepon_pengawas = '$TELEPON_PENGAWAS', alamat_pengawas = '$ALAMAT_PENGAWAS', jenkel_pengawas = '$JENGKEL', path_photo_pengawas = '$GAMBAR_FOTO', updated_at = '$WAKTU_SEKARANG' WHERE id_user = '$ID_USER'";
        if (!mysqli_query($KONEKSI, $SQL_PENGAWAS)) {
            throw new Exception("Gagal memperbarui data pengawas: " . mysqli_error($KONEKSI));
        }

        mysqli_commit($KONEKSI);

        if ($FOTO_BARU_UPLOADED && !empty($FOTO_LAMA)) {
            $OLD_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $FOTO_LAMA;
            if (file_exists($OLD_PATH)) { unlink($OLD_PATH); }
        }

        $_SESSION['Sess_SuccessMessage'] = "Data Pengawas berhasil diperbarui!";
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        if ($FOTO_BARU_UPLOADED && !empty($GAMBAR_FOTO)) {
            $NEW_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($NEW_PATH)) { unlink($NEW_PATH); }
        }
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}


//+=+=========================================+=+
// 16. FUNGSI HAPUS DATA PENGAWAS
//+=+=========================================+=+

/**
 * Menghapus data akun pengawas beserta berkas foto fisik terkait dari folder penyimpanan server.
 */
function hapus_pengawas($ID_USER)
{
    global $KONEKSI;
    mysqli_begin_transaction($KONEKSI);

    try {
        $CEK_FOTO = mysqli_query($KONEKSI, "SELECT path_photo_pengawas FROM tbl_pengawas WHERE id_user = '$ID_USER'");
        
        if ($CEK_FOTO && mysqli_num_rows($CEK_FOTO) > 0) {
            $ROW_FOTO = mysqli_fetch_assoc($CEK_FOTO);
            $NAMA_FOTO = $ROW_FOTO['path_photo_pengawas'];
            $AVATAR_BAWAAN = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
            
            if (!empty($NAMA_FOTO) && !in_array($NAMA_FOTO, $AVATAR_BAWAAN)) {
                $PATH_FOTO_PENGAWAS = __DIR__ . "/../images/user_pengawas/" . $NAMA_FOTO;
                if (file_exists($PATH_FOTO_PENGAWAS)) { unlink($PATH_FOTO_PENGAWAS); }
            }
        }

        mysqli_query($KONEKSI, "DELETE FROM tbl_pengawas WHERE id_user = '$ID_USER'");

        $HAPUS_UTAMA = mysqli_query($KONEKSI, "DELETE FROM tbl_users WHERE id_user = '$ID_USER'");
        if (!$HAPUS_UTAMA) {
            throw new Exception("Gagal menghapus data dari tbl_users.");
        }

        mysqli_commit($KONEKSI);
        return true;

    } catch (Exception $E) {
        mysqli_rollback($KONEKSI);
        return false;
    }
}
?>