<?php
//+=+=========================================+=+
//Aplikasi Bank_Mini untuk siswa SMK MVP
//File ini berguna untuk menampung semua fungsi. Dan dapat digunakan untuk berbagai keperluan
//+=+=========================================+=+

//+=+=========================================+=+
//1.Fungsi Pengaturan Waktu
//+=+=========================================+=+

// Mengatur zona waktu standar aplikasi ke WIB (Asia/Jakarta)[cite: 6]
date_default_timezone_set('Asia/Jakarta');
// Menyimpan format tanggal dan waktu saat ini (YYYY-MM-DD HH:MM:SS) ke variabel global[cite: 6]
$WAKTU_SEKARANG = date('Y-m-d H:i:s');

//+=+=========================================+=+
//2.Fungsi mengambil data dari database
//+=+=========================================+=+

// Menentukan nama host server database lokal[cite: 6]
$SERVERNAME = "localhost";
// Menentukan nama pengguna (username) database MySQL[cite: 6]
$USERNAME = "root";
// Menentukan kata sandi (password) database MySQL[cite: 6]
$PASSWORD = "";
// Menentukan nama basis data (database) yang digunakan aplikasi[cite: 6]
$DBNAME = "db_bankmini2526";
// Membuka koneksi baru ke server database MySQL menggunakan driver mysqli[cite: 6]
$KONEKSI = mysqli_connect($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME);

// Memeriksa apakah koneksi database gagal terhubung[cite: 6]
if (!$KONEKSI) {
    // Menghentikan eksekusi script dan menampilkan pesan kesalahan koneksi[cite: 6]
    die("koneksi gagal: " . mysqli_connect_error());
}

//+=+=========================================+=+
//3. fungsi autonumber untuk id user
//+=+=========================================+=+
// Mendefinisikan fungsi pembentuk ID otomatis berdasarkan parameter tabel, kolom, lebar, dan awalan[cite: 6]
function autonumber($TABEL, $KOLOM, $LEBAR=0, $AWALAN='') {
    // Mengakses variabel koneksi global di luar scope fungsi[cite: 6]
    global $KONEKSI; 

    // Mengeksekusi query untuk mengambil ID terakhir berdasarkan awalan (misal USR atau ADM) secara urutan menurun[cite: 6]
    $QUERY = mysqli_query($KONEKSI, "SELECT $KOLOM FROM $TABEL WHERE $KOLOM LIKE '$AWALAN%' ORDER BY $KOLOM DESC LIMIT 1") or die ('error:  ' . mysqli_error($KONEKSI));

    // Menghitung jumlah baris hasil query pencarian[cite: 6]
    $JUMLAH_RECORD = mysqli_num_rows($QUERY);
    // Jika tidak ada record awal, nomor baru dimulai dari angka 1[cite: 6]
    if ($JUMLAH_RECORD == 0) {
        // Set nomor angka urut pertama[cite: 6]
        $NOMOR_BARU = 1;
    } else {
        // Mengambil baris data hasil query dalam bentuk array terurut[cite: 6]
        $ROW = mysqli_fetch_array($QUERY);
        // Memotong string awalan dan merubah sisa karakter angka menjadi integer lalu ditambah 1[cite: 6]
        $NOMOR_BARU = intval(substr($ROW[0], strlen($AWALAN))) + 1;
    }

    // Jika parameter lebar diisi, format nomor baru dengan penambahan angka nol di sebelah kiri[cite: 6]
    if ($LEBAR > 0) {
        // Menggabungkan awalan dengan angka yang telah diformat str_pad[cite: 6]
        $ID_BARU = $AWALAN . str_pad($NOMOR_BARU, $LEBAR, "0", STR_PAD_LEFT);
    } else {
        // Jika tidak ada lebar khusus, langsung gabungkan awalan dan angka[cite: 6]
        $ID_BARU = $AWALAN . $NOMOR_BARU;
    }
    // Mengembalikan string ID otomatis yang telah dibentuk[cite: 6]
    return $ID_BARU;
}

//+=+=========================================+=+
// 4. fungsi registrasi
//+=+=========================================+=+

    // Mendefinisikan fungsi proses pendaftaran akun pengguna baru[cite: 6]
    function registrasi($DATA) {
    // ----------------------------------------------------------------------
    // GLOBAL VARIABLES
    // ----------------------------------------------------------------------
    // Mengambil variabel koneksi database ($KONEKSI) dan waktu saat ini ($WAKTU_SEKARANG)[cite: 6]
    // dari scope luar (global) agar bisa digunakan di dalam fungsi ini[cite: 6]
    global $KONEKSI, $WAKTU_SEKARANG;

    // ----------------------------------------------------------------------
    // 1. SANITASI & PERSIAPAN INPUT DATA
    // ----------------------------------------------------------------------
    // Stripslashes digunakan untuk menghapus karakter backslash (\) yang tidak sengaja[cite: 6]
    // terinput dari form pada field ID User[cite: 6]
    $ID_USER = stripslashes($DATA["IdUser"]);
    
    // Konversi email menjadi huruf kecil semua (strtolower) agar konsisten di DB,[cite: 6]
    // lalu hapus karakter backslash jika ada[cite: 6]
    $EMAIL = strtolower(stripslashes($DATA["Email"]));

    // Mengamankan karakter khusus (seperti tanda petik) pada password untuk mencegah SQL Injection[cite: 6]
    // saat dimasukkan ke dalam query database nantinya[cite: 6]
    $PASSWORD = mysqli_real_escape_string($KONEKSI, $DATA["Password"]);
    
    // Mengamankan input konfirmasi password dengan fungsi yang sama[cite: 6]
    $CONFIRM_PASSWORD = mysqli_real_escape_string($KONEKSI, $DATA["ConfirmPassword"]);

    // ----------------------------------------------------------------------
    // 2. CEK DUPLIKASI EMAIL
    // ----------------------------------------------------------------------
    // Jalankan query SELECT untuk mencari apakah email yang diinput sudah ada di tabel tbl_users[cite: 6]
    $RESULT = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email = '$EMAIL'")  
              or die('Error: ' . mysqli_error($KONEKSI));

    // Jika mysqli_fetch_assoc mengembalikan data (true), artinya email sudah terpakai[cite: 6]
    if (mysqli_fetch_assoc($RESULT)) {
        // Tampilkan pop-up peringatan lewat JavaScript[cite: 6]
        echo "<script>
            alert('Email sudah terdaftar!');
            </script>";
        // Hentikan eksekusi fungsi dan kembalikan nilai false[cite: 6]
        return false;
    }

    // ----------------------------------------------------------------------
    // 3. VALIDASI KESESUAIAN PASSWORD
    // ----------------------------------------------------------------------
    // Bandingkan apakah password dan konfirmasi password tidak identik (beda nilai atau tipe data)[cite: 6]
    if ($PASSWORD !== $CONFIRM_PASSWORD) {
        // Tampilkan peringatan jika password yang diinputkan tidak cocok[cite: 6]
        echo "<script>
            alert('Password dan Confirm Password tidak sama! cek lagi gih');
            </script>";
        // Hentikan eksekusi fungsi dan kembalikan nilai false[cite: 6]
        return false;
    }

    // ----------------------------------------------------------------------
    // 4. ENKRIPSI / HASHING PASSWORD
    // ----------------------------------------------------------------------
    // Amankan password menggunakan algoritma hashing standar PHP (Bcrypt/Argon2 tergantung versi PHP)[cite: 6]
    // Jangan simpan password plain-text ke database demi keamanan[cite: 6]
    $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);

    // ----------------------------------------------------------------------
    // 5. PENENTUAN ROLE / LEVEL PENGGUNA
    // ----------------------------------------------------------------------
    // Cek apakah ada input 'Role' dari form. Jika ada, ubah ke tipe data integer.[cite: 6]
    // Jika tidak ada, tetapkan nilai bawaan (default) yaitu 4 (misal: Nasabah)[cite: 6]
    $LEVEL = isset($DATA['Role']) ? (int)$DATA['Role'] : 4; 

    // ----------------------------------------------------------------------
    // 6. SIMPAN DATA KE DATABASE
    // ----------------------------------------------------------------------
    // Susun perintah SQL INSERT untuk memasukkan data pengguna baru ke tabel utama (tbl_users)[cite: 6]
    $QUERY = "INSERT INTO tbl_users (id_user, email, password, role, created_at, updated_at) 
              VALUES ('$ID_USER', '$EMAIL', '$PASSWORD_HASH', '$LEVEL', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";
              
    // Eksekusi query ke DB, jika gagal tampilkan pesan error bawaan MySQL[cite: 6]
    mysqli_query($KONEKSI, $QUERY) or die ('Gagal simpan data di tbl_users : ' . mysqli_error($KONEKSI));

    // Pengecekan kondisi khusus: jika user yang terdaftar punya role Admin (Level 1)[cite: 6]
    if ($LEVEL === 1) {
        // Buat query tambahan untuk mendaftarkan ID User tersebut ke tabel khusus admin (tbl_admin)[cite: 6]
        $QUERY2 = "INSERT INTO tbl_admin (id_user, created_at, updated_at) 
                   VALUES ('$ID_USER', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";
                   
        // Eksekusi query insert ke tbl_admin[cite: 6]
        mysqli_query($KONEKSI, $QUERY2) or die ('Gagal simpan data di tbl_admin : ' . mysqli_error($KONEKSI));
    } 

    // ----------------------------------------------------------------------
    // 7. NOTIFIKASI SUKSES & REDIRECT
    // ----------------------------------------------------------------------
    // Tampilkan pesan sukses dan alihkan halaman (redirect) user ke halaman login.php[cite: 6]
    echo "<script>
        alert('Selamat bung anda berhasil regis,Silahkan login untuk lanjut!!');
        document.location.href = 'login.php';
        </script>";

    // Kembalikan angka jumlah baris data yang berhasil ditambahkan di DB (> 0 berarti sukses)[cite: 6]
    return mysqli_affected_rows($KONEKSI);
}

//+=+=========================================+=+
//5. Fungsi Tampil User
//+=+=========================================+=+

// Mendefinisikan fungsi penampil kumpulan data hasil query[cite: 6]
function tampil($QUERY_STRING)
{
    // Memanggil variabel koneksi global[cite: 6]
    global $KONEKSI; 

    // Mengeksekusi perintah SQL query pencarian data[cite: 6]
    $QUERY = mysqli_query($KONEKSI, $QUERY_STRING) or die('Error: ' . mysqli_error($KONEKSI));

    // Inisialisasi variabel array kosong untuk menampung kumpulan data[cite: 6]
    $HASIL_DATA = []; 
    // Melakukan perulangan untuk mengambil setiap baris hasil query sebagai array asosiatif[cite: 6]
    while ($ROW = mysqli_fetch_assoc($QUERY)) {
        // Menambahkan baris data ke dalam array penampung[cite: 6]
        $HASIL_DATA[] = $ROW; 
    }

    // Mengembalikan seluruh sekumpulan data berbentuk array asosiatif[cite: 6]
    return $HASIL_DATA; 
}

//+=+=========================================+=+
//6. Fungsi Upload File
//+=+=========================================+=+ 

// Mendefinisikan fungsi umum penanganan pengunggahan berkas file ke server[cite: 6]
function upload_files($FILE_INPUT, $IDENTIFIER, $TARGET_FOLDER = '', $OPTION = [])
{

    // a. Konfigurasi bawaan default untuk validasi upload berkas[cite: 6]
    $DEFAULT = [
        'max_size' => 2 * 1024 * 1024, // Menetapkan batas ukuran maksimal[cite: 6]
        'allowed_types' => ['jpg', 'png', 'gif', 'bmp'], // Ekstensi berkas yang diizinkan[cite: 6]
        'create_folder' => true, // Pengaturan pembuat folder otomatis jika belum ada[cite: 6]
        'prefix' => ''           // Awalan string nama file[cite: 6]
    ];

    // Menggabungkan konfigurasi kustom pengirim dengan opsi default[cite: 6]
    $OPTIONS = array_merge($DEFAULT, $OPTION);

    // b. Validasi struktur array input file dari $_FILES[cite: 6]
    if (!isset($FILE_INPUT['error']) || !isset($FILE_INPUT['tmp_name'])) {
        // Mengembalikan status gagal jika struktur berkas unggahan tidak sesuai format[cite: 6]
        return [
            'success' => false,
            'message' => 'File yang di upload tidak valid. Pastikan form sudah diisi dengan benar (menggunakan enctype="multipart/form-data").'
        ];
    }

    // Menguraikan komponen metadata dari file yang dikirimkan[cite: 6]
    $ERRORS = $FILE_INPUT['error']; // Kode status error bawaan PHP[cite: 6]
    $TMP_NAME = $FILE_INPUT['tmp_name']; // Path file sementara di server[cite: 6]
    $NAMA_ASLI = $FILE_INPUT['name']; // Nama asli file di komputer client[cite: 6]
    $UKURAN_FILE = $FILE_INPUT['size']; // Ukuran file dalam satuan byte[cite: 6]

    // c. Mengecek apakah pengguna tidak memilih berkas apapun untuk diunggah[cite: 6]
    if ($ERRORS === UPLOAD_ERR_NO_FILE) {
        // Mengembalikan respon gagal karena tidak ada berkas terpilih[cite: 6]
        return [
            'success' => false,
            'message' => 'Tidak ada file yang diupload. Silahkan pilih file terlebih dahulu.'
        ];
    }

    // d. Mengecek apakah terjadi kesalahan saat proses pengunggahan file oleh PHP[cite: 6]
    if ($ERRORS !== UPLOAD_ERR_OK) {
        // Pemetaan pesan kesalahan berdasarkan kode error PHP[cite: 6]
        $ERROR_MESSAGES = [
            UPLOAD_ERR_INI_SIZE => 'File yang diupload melebihi batas ukuran yang diizinkan oleh server (Upload_max_filesize). Silahkan hubungi administrator.',
            UPLOAD_ERR_FORM_SIZE => 'File yang diupload melebihi batas ukuran yang diizinkan oleh Form (MAX_FILE_SIZE). Silahkan hubungi administrator.',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian. Silahkan coba lagi.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara untuk upload tidak ditemukan. Silahkan hubungi administrator.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk. Silahkan hubungi administrator.',
            UPLOAD_ERR_EXTENSION => 'Upload file dihentikan oleh ekstensi PHP. Silahkan hubungi administrator.'
        ];

        // Memilih pesan kesalahan yang sesuai dari array[cite: 6]
        $MESSAGE = isset($ERROR_MESSAGES[$ERRORS]) ? $ERROR_MESSAGES[$ERRORS] : 'Terjadi kesalahan saat mengupload file. Silahkan coba lagi.';
        // Mengembalikan array status error pengunggahan[cite: 6]
        return ['success' => false, 'message' => $MESSAGE];

    }

    // e. Mengambil ekstensi file asli dan mengonversinya ke huruf kecil[cite: 6]
    $EKSTENSI_FILE = strtolower(pathinfo($NAMA_ASLI, PATHINFO_EXTENSION)); 

    // f. Memvalidasi apakah ekstensi file sesuai dengan daftar yang diizinkan[cite: 6]
    if (!in_array($EKSTENSI_FILE, $OPTIONS['allowed_types'])) {
        // Menggabungkan daftar tipe ekstensi yang diperbolehkan menjadi string[cite: 6]
        $EXT_STR = implode(', ', $OPTIONS['allowed_types']);
        // Mengembalikan pesan kesalahan ekstensi tidak didukung[cite: 6]
        return [
            'success' => false,
            'message' => "Ekstensi file tidak diizinkan. Hanya file dengan ekstensi berikut yang diperbolehkan: $EXT_STR."
        ];
    }
    // g. Memvalidasi ukuran berkas agar tidak melebihi batas maksimal yang dikonfigurasi[cite: 6]
    if ($UKURAN_FILE > $OPTIONS['max_size']) {
        // Menghitung konversi byte ke Megabyte untuk tampilan pesan[cite: 6]
        $MAX_SIZE_MB = number_format($OPTIONS['max_size'] / 1048576, 2); 
        // Mengembalikan respon gagal jika ukuran melampaui batas[cite: 6]
        return [
            'success' => false,
            'message' => "Ukuran file melebihi batas maksimal yang diizinkan. Maksimal ukuran file adalah $MAX_SIZE_MB MB."
        ];
    }

    // h. Membentuk awalan string nama file jika diset[cite: 6]
    $PREFIX = $OPTIONS['prefix'] ? $OPTIONS['prefix'] . '_' : ''; 

    // Membuat identitas string acak berbasis mikrodetik[cite: 6]
    $UNIQUE_ID = uniqid(); 

    // Mengambil timestamp tanggal dan waktu saat ini[cite: 6]
    $TIMESTAMP = date('Ymd_His'); 

    // Menggabungkan elemen penyusun menjadi nama file yang baru dan unik[cite: 6]
    $NAMA_FILE_BARU = $PREFIX . $IDENTIFIER . '' . $TIMESTAMP . '' . $UNIQUE_ID . '.' . $EKSTENSI_FILE; 

    // i. Menentukan letak path tujuan penyimpanan file secara lengkap[cite: 6]
    $FILE_PATH = rtrim($TARGET_FOLDER, '/') . '/' . $NAMA_FILE_BARU;

    // j. Memindahkan berkas dari direktori sementara ke folder target di server[cite: 6]
    if (move_uploaded_file($TMP_NAME, $FILE_PATH)) {
        // Mengembalikan statistik informasi upload yang sukses dalam bentuk array[cite: 6]
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
        // Mengembalikan tanggapan gagal jika file gagal dipindahkan[cite: 6]
        return [
            'success' => false,
            'message' => 'Gagal memindahkan file ke folder tujuan. silahkan coba lagi. pastikan folder tujuan memiliki izin tulis (write permission) yang cukup.',
        ];
    }
}

//+=+=========================================+=+
//7. Fungsi Tambah User Admin
//+=+=========================================+=+

// Mendefinisikan fungsi pendaftaran data admin baru ke sistem[cite: 6]
// Mendefinisikan fungsi 7admin yang menerima masukan data form, file foto, dan folder lokasi penyimpanan
function tambah_admin($DATA, $FILES, $TARGET_FOLDER = "../images/user_admin")
{
    // Mengakses variabel koneksi MySQLi ($KONEKSI) dan format waktu saat ini ($WAKTU_SEKARANG) dari scope global
    global $KONEKSI, $WAKTU_SEKARANG; 

    // a. Pembersihan (Sanitasi) dan Pengamanan Karakter Input Form
    // Menghapus spasi luar (trim) dan mengonversi karakter khusus ke entitas HTML pada input ID Admin
    $ID_ADMIN      = htmlspecialchars(trim($DATA["IdAdmin"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Nama Admin
    $NAMA_ADMIN    = htmlspecialchars(trim($DATA["NamaAdmin"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Alamat Admin
    $ALAMAT_ADMIN  = htmlspecialchars(trim($DATA["AlamatAdmin"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Telepon Admin
    $TELEPON_ADMIN = htmlspecialchars(trim($DATA["TeleponAdmin"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Jenis Kelamin mentah
    $JENGKEL_RAW   = htmlspecialchars(trim($DATA["Jengkel"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Email
    $EMAIL         = htmlspecialchars(trim($DATA["Email"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Password
    $PASSWORD      = htmlspecialchars(trim($DATA["Password"] ?? ''));
    // Menghapus spasi luar dan mengonversi karakter khusus pada input Konfirmasi Password
    $PASSWORD2     = htmlspecialchars(trim($DATA["Password2"] ?? ''));

    // Memeriksa dan mengonversi format teks jenis kelamin ke karakter ENUM tunggal ('L' untuk Laki-Laki, 'P' untuk Perempuan)
    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    // b. Pengujian Validasi Kelengkapan Kolom Wajib
    // Inisialisasi variabel array kosong untuk menampung pesan-pesan kesalahan validasi
    $ERRORS = []; 
    // Pengecekan apakah ID Admin bernilai kosong, jika ya tambahkan pesan error ke array
    if (empty($ID_ADMIN))      $ERRORS[] = "ID Admin tidak boleh kosong.";
    // Pengecekan apakah Nama Admin bernilai kosong, jika ya tambahkan pesan error ke array[cite: 6]
    if (empty($NAMA_ADMIN))    $ERRORS[] = "Nama Admin tidak boleh kosong.";
    // Pengecekan apakah Alamat Admin bernilai kosong, jika ya tambahkan pesan error ke array[cite: 6]
    if (empty($ALAMAT_ADMIN))  $ERRORS[] = "Alamat Admin tidak boleh kosong.";
    // Pengecekan apakah Telepon Admin bernilai kosong, jika ya tambahkan pesan error ke array[cite: 6]
    if (empty($TELEPON_ADMIN)) $ERRORS[] = "Telepon Admin tidak boleh kosong.";
    // Pengecekan apakah Email bernilai kosong, jika ya tambahkan pesan error ke array[cite: 6]
    if (empty($EMAIL))         $ERRORS[] = "Email Admin tidak boleh kosong.";
    // Pengecekan apakah Password bernilai kosong, jika ya tambahkan pesan error ke array[cite: 6]
    if (empty($PASSWORD))      $ERRORS[] = "Password Admin tidak boleh kosong.";
    // Pengecekan apakah Konfirmasi Password bernilai kosong, jika ya tambahkan pesan error ke array[cite: 6]
    if (empty($PASSWORD2))     $ERRORS[] = "Konfirmasi Password Admin tidak boleh kosong.";

    // Memeriksa jika terdapat minimal satu pesan kesalahan dalam array $ERRORS[cite: 6]
    if (!empty($ERRORS)) {
        // Simpan seluruh daftar pesan error ke variabel Session agar bisa ditampilkan di UI form[cite: 6]
        $_SESSION['Sess_FormErrors'] = $ERRORS; 
        // Hentikan proses fungsi dan kembalikan nilai boolean false (gagal)[cite: 6]
        return false; 
    }

    // c. Pengecekan Duplikasi Email pada Database
    // Menjalankan query SQL untuk mengecek apakah email yang diinputkan sudah terdaftar pada tabel tbl_users[cite: 6]
    $RESULT = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email ='$EMAIL'") or die('Error: ' . mysqli_error($KONEKSI));
    // Memeriksa apakah query menemukan baris data email yang sama[cite: 6]
    if (mysqli_fetch_assoc($RESULT)) {
        // Simpan pesan kesalahan email terduplikasi ke dalam variabel Session[cite: 6]
        $_SESSION['Sess_FormErrors'] = ["Email Admin yang diinput sudah terdaftar."]; 
        // Hentikan eksekusi fungsi dan kembalikan boolean false[cite: 6]
        return false; 
    }

    // d. Validasi Kesesuaian Kata Sandi (Password Matching)
    // Memeriksa apakah password utama tidak identik dengan password konfirmasi[cite: 6]
    if ($PASSWORD !== $PASSWORD2) {
        // Simpan pesan kesalahan ketidakcocokan password pada Session[cite: 6]
        $_SESSION['Sess_FormErrors'] = ["Password dan Konfirmasi Password Admin tidak sama."]; 
        // Hentikan eksekusi fungsi dan kembalikan boolean false[cite: 6]
        return false; 
    }

    // e. Validasi Batas Minimal Panjang Password
    // Memeriksa apakah jumlah karakter password kurang dari 6 karakter[cite: 6]
    if (strlen($PASSWORD) < 6) {
        // Simpan pesan kesalahan batas minimal karakter password pada Session[cite: 6]
        $_SESSION['Sess_FormErrors'] = ["Password Admin minimal 6 karakter."]; 
        // Hentikan eksekusi fungsi dan kembalikan boolean false[cite: 6]
        return false; 
    }

    // f. Penanganan Pengunggahan Berkas Foto Profil
    // Inisialisasi variabel nama gambar foto dengan nilai awal string kosong[cite: 6]
    $GAMBAR_FOTO = ""; 
    // Memeriksa apakah file foto diisi dan tidak dalam status error UPLOAD_ERR_NO_FILE (pengguna memilih file)[cite: 6]
    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Memeriksa apakah terjadi kesalahan teknis saat proses upload oleh PHP[cite: 6]
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            // Simpan pesan error pengunggahan berkas ke dalam Session[cite: 6]
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            // Hentikan eksekusi fungsi dan kembalikan boolean false[cite: 6]
            return false;
        }

        // Mengeksekusi fungsi helper upload_files dengan mengirimkan parameter konfigurasi unggahan[cite: 6]
        $UPLOAD_RESULT = upload_files(
            $FILES['Photo'],
            $ID_ADMIN,
            $TARGET_FOLDER,
            [
                'max_size' => 2097152, // Batas maksimal ukuran berkas (2 MB dalam byte)[cite: 6]
                'allowed_types' => ['jpg', 'jpeg', 'png', 'bmp'], // Format gambar yang diizinkan[cite: 6]
                'create_folder' => true, // Pengaturan pembuat folder otomatis jika direktori belum ada[cite: 6]
                'prefix' => 'ADM' // Awalan penamaan berkas fisik di server[cite: 6]
            ]
        );

        // Memeriksa apakah fungsi upload_files mengembalikan status sukses (true)[cite: 6]
        if ($UPLOAD_RESULT['success']) {
            // Menyimpan nama unik berkas gambar yang berhasil tersimpan di server ke variabel $GAMBAR_FOTO[cite: 6]
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
        } else {
            // Simpan pesan gagal unggah dari helper ke dalam Session[cite: 6]
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            // Hentikan eksekusi fungsi dan kembalikan boolean false[cite: 6]
            return false;
        }
    }

    // g. Pengenkripsian Password Kredensial
    // Mengamankan kata sandi menggunakan algoritma Hashing standar PHP (Bcrypt/Argon2)[cite: 6]
    $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);

    // h. Pengelolaan Transaksi Database (Database Transaction)
    // Memulai mode transaksi database agar eksekusi multiple query bersifat atomic (semua sukses atau semua dibatalkan)[cite: 6]
    mysqli_begin_transaction($KONEKSI);

    try {
        // Menetapkan angka identifier level/role secara langsung (1 = Admin)[cite: 6]
        $LEVEL_ADMIN = 1;

        // Menyusun query SQL INSERT untuk memasukkan data akun utama ke tabel tbl_users[cite: 6]
        $SQL_USER = "INSERT INTO tbl_users (id_user, email, password, role, created_at, updated_at) 
                     VALUES ('$ID_ADMIN', '$EMAIL', '$PASSWORD_HASH', '$LEVEL_ADMIN', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        // Mengeksekusi query simpan user dan melemparkan Exception jika eksekusi SQL gagal[cite: 6]
        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal menambahkan user: " . mysqli_error($KONEKSI));
        }

        // Menyusun query SQL INSERT untuk memasukkan data detail profil ke tabel tbl_admin[cite: 6]
        $SQL_ADMIN = "INSERT INTO tbl_admin (nama_admin, telepon_admin, alamat_admin, jenis_kelamin, path_photo_admin, id_user, created_at, updated_at) 
                      VALUES ('$NAMA_ADMIN', '$TELEPON_ADMIN', '$ALAMAT_ADMIN', '$JENGKEL', '$GAMBAR_FOTO', '$ID_ADMIN', '$WAKTU_SEKARANG', '$WAKTU_SEKARANG')";

        // Mengeksekusi query simpan admin dan melemparkan Exception jika eksekusi SQL gagal[cite: 6]
        if (!mysqli_query($KONEKSI, $SQL_ADMIN)) {
            throw new Exception("Gagal menambahkan admin: " . mysqli_error($KONEKSI));
        }

        // Menyelesaikan dan menerapkan seluruh perubahan query ke database secara permanen jika tidak ada eror[cite: 6]
        mysqli_commit($KONEKSI);
        // Menyimpan pesan berhasil ke dalam variabel Session untuk notifikasi UI[cite: 6]
        $_SESSION['Sess_SuccessMessage'] = "Data berhasil ditambahkan!";
        // Kembalikan boolean true menandakan seluruh proses berhasil penuh[cite: 6]
        return true;

    } catch (Exception $E) {
        // Membatalkan seluruh pembaruan database yang sempat berjalan jika terjadi kegagalan/exception[cite: 6]
        mysqli_rollback($KONEKSI);

        // Pengecekan pembersihan: jika berkas gambar sempat terupload tetapi simpan DB gagal, hapus file gambar fisiknya[cite: 6]
        if (!empty($GAMBAR_FOTO)) {
            // Membentuk jalur lengkap lokasi berkas fisik gambar di server[cite: 6]
            $FILE_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            // Memeriksa keberadaan file fisik sebelum dihapus[cite: 6]
            if (file_exists($FILE_PATH)) {
                // Hapus berkas gambar fisik dari server[cite: 6]
                unlink($FILE_PATH);
            }
        }

        // Simpan pesan kesalahan exception ke dalam variabel Session[cite: 6]
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        // Kembalikan boolean false menandakan pemrosesan gagal[cite: 6]
        return false;
    }
}

//+=+=========================================+=+
//8. Fungsi Edit User Admin
//+=+=========================================+=+

// Mendefinisikan fungsi untuk mengubah/memperbarui data admin yang sudah ada[cite: 6]
function edit_admin($DATA, $FILES, $TARGET_FOLDER = "../images/user_admin")
{
    // Memanggil variabel global koneksi dan waktu[cite: 6]
    global $KONEKSI, $WAKTU_SEKARANG; 

    // a. Pembersihan masukan data pembaharuan[cite: 6]
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

    // Konversi format pilihan jenis kelamin[cite: 6]
    $JENGKEL = ($JENGKEL_RAW === 'Laki-Laki' || $JENGKEL_RAW === 'L') ? 'L' : 'P';

    // b. Memvalidasi kolom wajib isi[cite: 6]
    $ERRORS = []; 
    if (empty($ID_USER))       $ERRORS[] = "ID User tidak valid.";
    if (empty($NAMA_ADMIN))    $ERRORS[] = "Nama Admin tidak boleh kosong.";
    if (empty($ALAMAT_ADMIN))  $ERRORS[] = "Alamat Admin tidak boleh kosong.";
    if (empty($TELEPON_ADMIN)) $ERRORS[] = "Telepon Admin tidak boleh kosong.";
    if (empty($EMAIL))         $ERRORS[] = "Email Admin tidak boleh kosong.";

    // Pengecekan password Opsional (hanya diproses jika form password diisi)[cite: 6]
    if (!empty($PASSWORD)) {
        if ($PASSWORD !== $PASSWORD2) {
            $ERRORS[] = "Password dan Konfirmasi Password tidak sama.";
        }
        if (strlen($PASSWORD) < 6) {
            $ERRORS[] = "Password minimal 6 karakter.";
        }
    }

    // Menyimpan error jika ditemukan kendala validasi[cite: 6]
    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS;
        return false; 
    }

    // c. Mengecek ketersediaan email (mengabaikan milik dirinya sendiri)[cite: 6]
    $CEK_EMAIL = mysqli_query($KONEKSI, "SELECT email FROM tbl_users WHERE email = '$EMAIL' AND id_user != '$ID_USER'");
    if (mysqli_fetch_assoc($CEK_EMAIL)) {
        $_SESSION['Sess_FormErrors'] = ["Email sudah digunakan."]; 
        return false; 
    }

    // d. Penanganan upload foto baru[cite: 6]
    $GAMBAR_FOTO = $FOTO_LAMA; // Menggunakan nama foto lama sebagai default[cite: 6]
    $FOTO_BARU_UPLOADED = false;

    // Pengecekan apakah pengguna memilih file foto baru[cite: 6]
    if (isset($FILES['Photo']) && $FILES['Photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($FILES['Photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['Sess_FormErrors'] = ['Terjadi error saat mengunggah berkas foto.'];
            return false;
        }

        // Mengeksekusi pengunggahan foto pengganti[cite: 6]
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

        // Jika upload berhasil, perbarui variabel nama foto[cite: 6]
        if ($UPLOAD_RESULT['success']) {
            $GAMBAR_FOTO = $UPLOAD_RESULT['Filename_tersimpan'];
            $FOTO_BARU_UPLOADED = true;
        } else {
            $_SESSION['Sess_FormErrors'] = [$UPLOAD_RESULT['message']];
            return false;
        }
    }

    // e. Memulai transaksi pembaruan data[cite: 6]
    mysqli_begin_transaction($KONEKSI);

    try {
        // Mengatur klausa password jika pengubahan kata sandi diisi[cite: 6]
        $SQL_PASSWORD = "";
        if (!empty($PASSWORD)) {
            $PASSWORD_HASH = password_hash($PASSWORD, PASSWORD_DEFAULT);
            $SQL_PASSWORD = ", password = '$PASSWORD_HASH'";
        }

        // Query update pada tabel akun utama[cite: 6]
        $SQL_USER = "UPDATE tbl_users SET 
                     email = '$EMAIL' 
                     $SQL_PASSWORD, 
                     updated_at = '$WAKTU_SEKARANG' 
                     WHERE id_user = '$ID_USER'";

        // Eksekusi query update user[cite: 6]
        if (!mysqli_query($KONEKSI, $SQL_USER)) {
            throw new Exception("Gagal memperbarui data user: " . mysqli_error($KONEKSI));
        }

        // Query update pada tabel detail profil admin[cite: 6]
        $SQL_ADMIN = "UPDATE tbl_admin SET 
                      nama_admin = '$NAMA_ADMIN',
                      telepon_admin = '$TELEPON_ADMIN',
                      alamat_admin = '$ALAMAT_ADMIN',
                      jenis_kelamin = '$JENGKEL',
                      path_photo_admin = '$GAMBAR_FOTO',
                      updated_at = '$WAKTU_SEKARANG' 
                      WHERE id_user = '$ID_USER'";

        // Eksekusi query update admin[cite: 6]
        if (!mysqli_query($KONEKSI, $SQL_ADMIN)) {
            throw new Exception("Gagal memperbarui data admin: " . mysqli_error($KONEKSI));
        }

        // Menyelesaikan transaksi[cite: 6]
        mysqli_commit($KONEKSI);

        // Menghapus file fisik foto lama di server jika berhasil mengganti dengan foto baru[cite: 6]
        if ($FOTO_BARU_UPLOADED && !empty($FOTO_LAMA)) {
            $OLD_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $FOTO_LAMA;
            if (file_exists($OLD_PATH)) {
                unlink($OLD_PATH);
            }
        }

        // Simpan notifikasi sukses di session[cite: 6]
        $_SESSION['Sess_SuccessMessage'] = "Data berhasil diperbarui!";
        return true;

    } catch (Exception $E) {
        // Pembatalan transaksi jika terjadi masalah[cite: 6]
        mysqli_rollback($KONEKSI);

        // Menghapus foto baru yang baru terunggah jika pembaharuan database gagal[cite: 6]
        if ($FOTO_BARU_UPLOADED && !empty($GAMBAR_FOTO)) {
            $NEW_PATH = rtrim($TARGET_FOLDER, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $GAMBAR_FOTO;
            if (file_exists($NEW_PATH)) {
                unlink($NEW_PATH);
            }
        }

        // Simpan pesan error ke sesi[cite: 6]
        $_SESSION['Sess_FormErrors'] = [$E->getMessage()];
        return false;
    }
}

//+=+=========================================+=+
//9. Fungsi Hapus User Admin / Nasabah
//+=+=========================================+=+

// Mendefinisikan fungsi penghapusan data akun user[cite: 6]
// Mendefinisikan fungsi hapus_user yang menerima masukan berupa variabel $ID_USER
function hapus_user($ID_USER)
{
    // Mengakses variabel koneksi MySQLi ($KONEKSI) dari scope global di luar fungsi
    global $KONEKSI;

    // Memulai mode transaksi database agar eksekusi multiple query bersifat atomic (semua berhasil atau semua dibatalkan)
    mysqli_begin_transaction($KONEKSI);

    try {
        // 1. PENGECEKAN DAN PEMBERSIHAN BERKAS FOTO FISIK
        // Menjalankan query SQL untuk mencari nama berkas foto admin berdasarkan id_user di tbl_admin
        $CEK_FOTO = mysqli_query($KONEKSI, "SELECT path_photo_admin FROM tbl_admin WHERE id_user = '$ID_USER'");
        
        // Memeriksa apakah query berhasil dijalankan dan menemukan minimal 1 baris data
        if ($CEK_FOTO && mysqli_num_rows($CEK_FOTO) > 0) {
            // Mengambil baris data hasil query dalam bentuk array asosiatif
            $ROW_FOTO = mysqli_fetch_assoc($CEK_FOTO);
            // Menyimpan string nama berkas foto admin ke variabel $NAMA_FOTO
            $NAMA_FOTO = $ROW_FOTO['path_photo_admin'];

            // Mendefinisikan daftar nama file gambar avatar default bawaan sistem yang dilarang dihapus dari server
            $AVATAR_BAWAAN = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
            
            // Memeriksa apakah variabel $NAMA_FOTO terisi dan nama filenya TIDAK ADA di dalam daftar $AVATAR_BAWAAN
            if (!empty($NAMA_FOTO) && !in_array($NAMA_FOTO, $AVATAR_BAWAAN)) {
                // Membentuk jalur (path) direktori lengkap lokasi file gambar di dalam folder server[cite: 6]
                $PATH_FOTO_ADMIN = __DIR__ . "/../images/user_admin/" . $NAMA_FOTO;
                
                // Memeriksa apakah berkas fisik foto tersebut benar-benar ada di direktori server[cite: 6]
                if (file_exists($PATH_FOTO_ADMIN)) {
                    // Menghapus file fisik gambar dari penyimpanan disk server menggunakan fungsi unlink()[cite: 6]
                    unlink($PATH_FOTO_ADMIN);
                }
            }
        }

        // 2. PENGHAPUSAN DATA RELASI DI TABEL CHILD
        // Mengeksekusi query SQL DELETE untuk menghapus data terkait di tabel tbl_admin berdasarkan id_user[cite: 6]
        mysqli_query($KONEKSI, "DELETE FROM tbl_admin WHERE id_user = '$ID_USER'");
        
        // Mengeksekusi query SQL DELETE untuk menghapus data terkait di tabel nasabah berdasarkan id_nasabah[cite: 6]
        mysqli_query($KONEKSI, "DELETE FROM nasabah WHERE id_nasabah = '$ID_USER'");

        // 3. PENGHAPUSAN DATA UTAMA DI TABEL PARENT
        // Mengeksekusi query SQL DELETE untuk menghapus data akun utama dari tabel tbl_users[cite: 6]
        $HAPUS_UTAMA = mysqli_query($KONEKSI, "DELETE FROM tbl_users WHERE id_user = '$ID_USER'");
        
        // Memeriksa apakah query penghapusan akun utama pada tbl_users gagal dieksekusi[cite: 6]
        if (!$HAPUS_UTAMA) {
            // Melemparkan pesan kesalahan (Exception) jika eksekusi SQL hapus akun utama gagal[cite: 6]
            throw new Exception("Gagal menghapus data dari tbl_users.");
        }

        // Menerapkan dan menyimpan seluruh perubahan query secara permanen ke database jika tidak ada error[cite: 6]
        mysqli_commit($KONEKSI);
        // Mengembalikan nilai boolean true yang menandakan proses penghapusan berhasil total[cite: 6]
        return true;

    } catch (Exception $E) {
        // Membatalkan seluruh perintah penghapusan di database (rollback) jika terjadi kesalahan/exception[cite: 6]
        mysqli_rollback($KONEKSI);
        // Mengembalikan nilai boolean false yang menandakan proses penghapusan gagal[cite: 6]
        return false;
    }
}

// ============================================================
// Digunakan untuk mengganti password user admin yang sedang di-edit
// Data $POST yang dikirim dari modal di edit.php
// ============================================================

// ============================================================
// FUNGSI GANTI PASSWORD (KHUSUS UNTUK MODAL DI EDIT.PHP)
// Memproses verifikasi password lama, validasi, dan update hash password baru
// ============================================================

// Mendefinisikan fungsi khusus untuk mengganti password via modal[cite: 6]
function ganti_password($DATA)
{
    global $KONEKSI;        // Memanggil variabel koneksi database global[cite: 6]
    global $WAKTU_SEKARANG; // Memanggil variabel waktu saat ini (WIB)[cite: 6]
    

    // ------------------------------------------------------------
    // 1. AMBIL DATA DARI FORM MODAL & SANITASI
    // ------------------------------------------------------------
    // Mengamankan ID User dari serangan SQL Injection[cite: 6]
    $ID_USER           = mysqli_real_escape_string($KONEKSI, $DATA['IdUserPassword'] ?? '');
    $PASS_LAMA         = $DATA['PassLama'] ?? '';         // Input password lama dari form[cite: 6]
    $PASS_BARU         = $DATA['PassBaru'] ?? '';         // Input password baru dari form[cite: 6]
    $PASS_BARU_CONFIRM = $DATA['PassBaruConfirm'] ?? ''; // Input konfirmasi password baru dari form[cite: 6]

    // ------------------------------------------------------------
    // 2. VALIDASI APABILA ADA BIDANG YANG KOSONG
    // ------------------------------------------------------------
    $ERRORS = []; // Array penampung daftar pesan kesalahan[cite: 6]

    if (empty($ID_USER))           $ERRORS[] = "ID User tidak ditemukan!";                 // Cek ID User[cite: 6]
    if (empty($PASS_LAMA))         $ERRORS[] = "Password lama tidak boleh kosong!";         // Cek Password Lama[cite: 6]
    if (empty($PASS_BARU))         $ERRORS[] = "Password baru tidak boleh kosong!";         // Cek Password Baru[cite: 6]
    if (empty($PASS_BARU_CONFIRM)) $ERRORS[] = "Konfirmasi password tidak boleh kosong!"; // Cek Konfirmasi Password[cite: 6]

    // Jika ditemukan inputan kosong, hentikan proses dan kirim error[cite: 6]
    if (!empty($ERRORS)) {
        $_SESSION['Sess_FormErrors'] = $ERRORS; // Simpan daftar error ke variabel Session[cite: 6]
        return false;                          // Kembalikan status gagal[cite: 6]
    }

    // ------------------------------------------------------------
    // 3. VERIFIKASI PASSWORD LAMA KE DATABASE
    // ------------------------------------------------------------
    // Query untuk mengambil hash password lama pengguna berdasarkan ID User[cite: 6]
    $SQL_CEK    = "SELECT password FROM tbl_users WHERE id_user = '$ID_USER'";
    $RESULT_CEK = mysqli_query($KONEKSI, $SQL_CEK);

    // Cek apakah data user terdaftar di database[cite: 6]
    if (mysqli_num_rows($RESULT_CEK) == 0) {
        $_SESSION['Sess_FormErrors'] = ["User tidak ditemukan di database!"]; // Set pesan error[cite: 6]
        return false;
    }

    $USER_DATA = mysqli_fetch_assoc($RESULT_CEK); // Ambil data baris user[cite: 6]

    // Pencocokan password lama yang diinput dengan hash di database[cite: 6]
    if (!password_verify($PASS_LAMA, $USER_DATA['password'])) {
        $_SESSION['Sess_FormErrors'] = ["Password lama yang Anda masukkan SALAH!"]; // Set pesan jika password beda[cite: 6]
        return false;
    }

    // ------------------------------------------------------------
    // 4. VALIDASI ATURAN PASSWORD BARU
    // ------------------------------------------------------------
    // Cek apakah password baru kurang dari 6 karakter[cite: 6]
    if (strlen($PASS_BARU) < 6) {
        $_SESSION['Sess_FormErrors'] = ["Password baru minimal 6 karakter!"]; // Set peringatan panjang password[cite: 6]
        return false;
    }

    // Cek kesesuaian antara password baru dan konfirmasinya[cite: 6]
    if ($PASS_BARU !== $PASS_BARU_CONFIRM) {
        $_SESSION['Sess_FormErrors'] = ["Konfirmasi password baru tidak sesuai!"]; // Set peringatan konfirmasi beda[cite: 6]
        return false;
    }

    // Cek jika pengguna memasukkan password baru yang sama persis dengan password lama[cite: 6]
    if (password_verify($PASS_BARU, $USER_DATA['password'])) {
        $_SESSION['Sess_FormErrors'] = ["Password baru tidak boleh sama dengan password lama!"]; // Set peringatan password sama[cite: 6]
        return false;
    }

    // ------------------------------------------------------------
    // 5. ENKRIPSI DAN PEMBARUAN PASSWORD DI DATABASE
    // ------------------------------------------------------------
    // Mengamankan password baru dengan hashing Bcrypt / Argon2[cite: 6]
    $PASSWORD_HASH = password_hash($PASS_BARU, PASSWORD_DEFAULT);

    // Query update untuk memperbarui kolom password dan tanggal update[cite: 6]
    $SQL_UPDATE = "UPDATE tbl_users SET 
                    password = '$PASSWORD_HASH',
                    updated_at = '$WAKTU_SEKARANG'
                  WHERE id_user = '$ID_USER'";

    // Eksekusi query update ke database[cite: 6]
    if (mysqli_query($KONEKSI, $SQL_UPDATE)) {
        // Cek jika ada baris data yang berhasil diubah[cite: 6]
        if (mysqli_affected_rows($KONEKSI) > 0) {
            $_SESSION['Sess_SuccessMessage'] = "Password berhasil diubah!"; // Simpan pesan sukses di session[cite: 6]
            return true;                                                    // Kembalikan nilai sukses[cite: 6]
        } else {
            $_SESSION['Sess_FormErrors'] = ["Tidak ada perubahan pada password."]; // Jika tidak ada perubahan[cite: 6]
            return false;
        }
    } else {
        // Apabila query SQL mengalami error[cite: 6]
        $_SESSION['Sess_FormErrors'] = ["Gagal mengupdate password: " . mysqli_error($KONEKSI)];
        return false;
    }
}


?>