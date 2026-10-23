<?php
// Memanggil file helper/fungsi eksternal untuk koneksi dan fungsi-fungsi pendukung[cite: 1]
require_once __DIR__ . '/../../inc/function.php';
// Mengakses variabel koneksi database MySQLi global[cite: 1]
global $KONEKSI;

// 1. Menangkap parameter ID dari URL GET query string (menggunakan null coalescing operator)[cite: 1]
$ID_USER_URL = $_GET['Id'] ?? '';

// 2. Memeriksa apakah parameter ID dari URL bernilai numerik/angka[cite: 1]
if (is_numeric($ID_USER_URL)) {
    // Jika ID berupa angka, susun query SQL untuk mencari data berdasarkan kolom id_petugas pada tbl_petugas[cite: 1]
    $QUERY_EDIT = "SELECT tbl_users.*, tbl_petugas.* 
                   FROM tbl_petugas 
                   LEFT JOIN tbl_users ON tbl_petugas.id_user = tbl_users.id_user 
                   WHERE tbl_petugas.id_petugas = '$ID_USER_URL'";
} else {
    // Jika ID berupa teks/string, susun query SQL untuk mencari data berdasarkan kolom id_user pada tbl_users[cite: 1]
    $QUERY_EDIT = "SELECT tbl_users.*, tbl_petugas.* 
                   FROM tbl_users 
                   LEFT JOIN tbl_petugas ON tbl_users.id_user = tbl_petugas.id_user 
                   WHERE tbl_users.id_user = '$ID_USER_URL'";
}

// Mengeksekusi query pencarian data ke database dan menghentikan script jika terjadi kesalahan query[cite: 1]
$HASIL_EDIT = mysqli_query($KONEKSI, $QUERY_EDIT) or die("Gagal mengambil data: " . mysqli_error($KONEKSI));
// Mengambil baris data hasil query dalam bentuk array asosiatif[cite: 1]
$DATA_LAMA = mysqli_fetch_assoc($HASIL_EDIT);

// Memeriksa apakah data lama tidak ditemukan dari query relasi gabungan di atas[cite: 1]
if (!$DATA_LAMA) {
    // Susun query cadangan (fallback) untuk mencari langsung ke tbl_users saja[cite: 1]
    $QUERY_FALLBACK = "SELECT * FROM tbl_users WHERE id_user = '$ID_USER_URL'";
    // Eksekusi query cadangan ke database[cite: 1]
    $HASIL_FALLBACK = mysqli_query($KONEKSI, $QUERY_FALLBACK);
    // Mengambil hasil baris data cadangan dalam bentuk array asosiatif[cite: 1]
    $DATA_LAMA = mysqli_fetch_assoc($HASIL_FALLBACK);
}

// Menentukan ID User final dari data lama yang ditemukan, atau menggunakan ID dari URL[cite: 1]
$ID_USER_FINAL = !empty($DATA_LAMA['id_user']) ? $DATA_LAMA['id_user'] : $ID_USER_URL;

// 3. Menangkap nama file foto petugas dari database (jika ada)[cite: 1]
$FOTO_PETUGAS = $DATA_LAMA['path_photo_petugas'] ?? '';
// Memeriksa keberadaan nama foto dan keberadaan file fisiknya di direktori server[cite: 1]
if (!empty($FOTO_PETUGAS) && file_exists(__DIR__ . '/../../images/user_petugas/' . $FOTO_PETUGAS)) {
    // Set lokasi path gambar ke foto khusus pengguna[cite: 1]
    $FOTO_SAAT_INI = '../../images/user_petugas/' . $FOTO_PETUGAS;
} else {
    // Set lokasi path gambar ke gambar avatar bawaan/default[cite: 1]
    $FOTO_SAAT_INI = '../../images/avatar/1.jpg';
}

// ============================================================
// PROSES EKSEKUSI GANTI PASSWORD (SAAT FORM MODAL DI-SUBMIT)
// ============================================================
// Memeriksa apakah request HTTP berupa POST dan tombol UpdatePassword ditekan[cite: 1]
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['UpdatePassword'])) {

    // Memanggil fungsi ganti_password dengan mengirimkan seluruh data array $_POST[cite: 1]
    $result = ganti_password($_POST);

    // Memeriksa apakah fungsi ganti_password mengembalikan nilai true (berhasil)[cite: 1]
    if ($result) {
        // Menampilkan pesan pop-up JavaScript sukses dan mengarahkan kembali ke halaman edit[cite: 1]
        echo "<script>
            alert('Password berhasil diubah!');
            window.location.href = 'index.php?pages=edit_user&Id=" . htmlspecialchars($_POST['IdUserPassword']) . "';
        </script>";
        // Menghentikan eksekusi script PHP selanjutnya[cite: 1]
        exit();
    } else {
        // Memeriksa apakah terdapat pesan error yang disimpan dalam session[cite: 1]
        if (isset($_SESSION['Sess_FormErrors'])) {
            // Menampilkan pesan error gabungan dengan JavaScript alert[cite: 1]
            echo "<script>
                alert('" . implode("\\n", $_SESSION['Sess_FormErrors']) . "');
            </script>";
            // Menghapus variabel session error dari memori[cite: 1]
            unset($_SESSION['Sess_FormErrors']);
        }
    }
}
?>

<!-- Container utama layout pembungkus halaman edit[cite: 1] -->
<div class="page-heading">
    <!-- Bagian header dan judul halaman[cite: 1] -->
    <div class="page-title">
        <!-- Baris elemen sistem grid Bootstrap[cite: 1] -->
        <div class="row">
            <!-- Kolom Bootstrap setengah lebar layar[cite: 1] -->
            <div class="col-12 col-md-6">
                <!-- Elemen teks heading judul halaman edit[cite: 1] -->
                <h3>Edit Data Petugas</h3>
            </div>
        </div>
    </div>

    <!-- Bagian seksi utama konten form edit[cite: 1] -->
    <section class="section mt-3">
        <!-- Komponen card Bootstrap pembungkus isi form[cite: 1] -->
        <div class="card">
            <!-- Bodi kartu tempat komponen UI dirender[cite: 1] -->
            <div class="card-body">

                <!-- Blok pengecekan ada/tidaknya pesan error pada session[cite: 1] -->
                <?php if (isset($_SESSION['Sess_FormErrors'])): ?>
                    <!-- Kotak notifikasi alert berwarna merah (danger) Bootstrap[cite: 1] -->
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <!-- Tombol untuk menutup alert[cite: 1] -->
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <!-- Perulangan foreach untuk menampilkan setiap pesan error[cite: 1] -->
                        <?php foreach ($_SESSION['Sess_FormErrors'] as $ERR): ?>
                            <!-- Menampilkan teks deskripsi pesan error ter-escaped[cite: 1] -->
                            <p class="mb-0"><?= htmlspecialchars($ERR) ?></p>
                        <?php endforeach; ?>
                    </div>
                    <!-- Menghapus session error setelah selesai ditampilkan ke layar[cite: 1] -->
                    <?php unset($_SESSION['Sess_FormErrors']); ?>
                <?php endif; ?>

                <!-- Blok pengecekan ada/tidaknya pesan sukses pada session[cite: 1] -->
                <?php if (isset($_SESSION['Sess_SuccessMessage'])): ?>
                    <!-- Kotak notifikasi alert berwarna hijau (success) Bootstrap[cite: 1] -->
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <!-- Tombol untuk menutup alert[cite: 1] -->
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <!-- Menampilkan teks pesan sukses[cite: 1] -->
                        <?= htmlspecialchars($_SESSION['Sess_SuccessMessage']) ?>
                    </div>
                    <!-- Menghapus session pesan sukses dari memori[cite: 1] -->
                    <?php unset($_SESSION['Sess_SuccessMessage']); ?>
                <?php endif; ?>

                <!-- Form utama untuk mengubah data profil (dikirim ke handler proses_edit via POST)[cite: 1] -->
               <form action="index.php?pages=prosesedit_petugas" method="POST" enctype="multipart/form-data">
                    <!-- Input tersembunyi untuk membawa nilai ID petugas[cite: 1] -->
                    <input type="hidden" name="IdPetugas" value="<?= htmlspecialchars($DATA_LAMA['id_petugas'] ?? '') ?>">
                    <!-- Input tersembunyi untuk membawa nilai ID User[cite: 1] -->
                    <input type="hidden" name="IdUser" value="<?= htmlspecialchars($ID_USER_FINAL) ?>">
                    <!-- Input tersembunyi untuk menyimpan nama foto lama[cite: 1] -->
                    <input type="hidden" name="FotoLama" value="<?= htmlspecialchars($DATA_LAMA['path_photo_petugas'] ?? '') ?>">

                    <!-- Baris pembagi tampilan kiri (foto) dan kanan (form)[cite: 1] -->
                    <div class="row">
                        <!-- Kolom kiri (lebar 4) untuk foto profil dan input file[cite: 1] -->
                        <div class="col-md-4 text-center mb-4">
                            <!-- Label judul bagian foto profil[cite: 1] -->
                            <label class="form-label fw-bold d-block mb-2">Foto Profil</label>
                            <!-- Elemen gambar preview foto pengguna[cite: 1] -->
                            <img id="imagePreview" src="<?= htmlspecialchars($FOTO_SAAT_INI) ?>" class="img-thumbnail mb-2" style="width: 160px; height: 160px; object-fit: cover;" alt="Preview Foto">
                            <!-- Input unggah file foto baru[cite: 1] -->
                            <input type="file" class="form-control" id="photoInput" name="Photo" accept=".png, .jpg, .jpeg" />
                            <!-- Teks bantuan opsional untuk user[cite: 1] -->
                            <small class="text-muted d-block mt-1">!! Biarkan kosong jika tidak ingin mengganti foto</small>
                        </div>

                        <!-- Kolom kanan (lebar 8) untuk input data teks[cite: 1] -->
                        <div class="col-md-8">
                            <!-- Baris pertama: ID User dan Email[cite: 1] -->
                            <div class="row mb-3">
                                <!-- Kolom ID User[cite: 1] -->
                                <div class="col-md-6">
                                    <!-- Label ID User[cite: 1] -->
                                    <label class="form-label fw-bold">ID User</label>
                                    <!-- Input ID User yang bernilai tetap (readonly)[cite: 1] -->
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly />
                                </div>
                                <!-- Kolom Email[cite: 1] -->
                                <div class="col-md-6">
                                    <!-- Label Email[cite: 1] -->
                                    <label class="form-label fw-bold">Username / Email</label>
                                    <!-- Input email pengguna[cite: 1] -->
                                    <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['email'] ?? '') ?>" required />
                                </div>
                            </div>

                            <!-- Input nama lengkap pengguna[cite: 1] -->
                            <div class="mb-3">
                                <!-- Label nama[cite: 1] -->
                                <label class="form-label fw-bold">Nama User</label>
                                <!-- Input teks nama[cite: 1] -->
                                <input type="text" name="NamaPetugas" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['nama_petugas'] ?? '') ?>" required />
                            </div>

                            <!-- Input alamat lengkap pengguna[cite: 1] -->
                            <div class="mb-3">
                                <!-- Label alamat[cite: 1] -->
                                <label class="form-label fw-bold">Alamat</label>
                                <!-- Input teks alamat[cite: 1] -->
                                <input type="text" name="AlamatPetugas" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['alamat_petugas'] ?? '') ?>" required />
                            </div>

                            <!-- Input nomor telepon[cite: 1] -->
                            <div class="mb-3">
                                <!-- Label nomor telepon[cite: 1] -->
                                <label class="form-label fw-bold">Telepon</label>
                                <!-- Input teks telepon[cite: 1] -->
                                <input type="text" name="TeleponPetugas" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['telepon_petugas'] ?? '') ?>" required />
                            </div>

                            <!-- Opsi radio button jenis kelamin[cite: 1] -->
                            <div class="mb-3">
                                <!-- Label jenis kelamin[cite: 1] -->
                                <label class="form-label fw-bold d-block">Jenis Kelamin</label>
                                <!-- Radio button Laki-Laki[cite: 1] -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Jengkel" value="L" id="jengkel_l" <?= (($DATA_LAMA['jenkel_petugas'] ?? '') === 'L') ? 'checked' : '' ?> />
                                    <label class="form-check-label" for="jengkel_l">Laki-Laki</label>
                                </div>
                                <!-- Radio button Perempuan[cite: 1] -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Jengkel" value="P" id="jengkel_p" <?= (($DATA_LAMA['jenkel_petugas'] ?? '') === 'P') ? 'checked' : '' ?> />
                                    <label class="form-check-label" for="jengkel_p">Perempuan</label>
                                </div>
                            </div>

                            <!-- Dropdown pilihan Role/Tipe User[cite: 1] -->
                            <div class="mb-3">
                                <!-- Label pilihan Role[cite: 1] -->
                                <label class="form-label fw-bold">Role / Tipe User</label>
                                <!-- Elemen select dropdown[cite: 1] -->
                                <select name="Role" class="form-select" required>
                                    <!-- Opsi tempat penampung (placeholder)[cite: 1] -->
                                    <option value="" disabled>-- Pilih Role --</option>
                                    <?php
                                    // Query SQL untuk mengambil daftar role dari tabel tbl_tipe_user[cite: 1]
                                    $SQL_ROLES = "SELECT id_tipe_user, tipe_user FROM tbl_tipe_user ORDER BY tipe_user ASC";
                                    // Eksekusi query role ke database[cite: 1]
                                    $QUERY_ROLES = mysqli_query($KONEKSI, $SQL_ROLES);
                                    
                                    // Memeriksa jika query berhasil dieksekusi[cite: 1]
                                    if ($QUERY_ROLES) {
                                        // Menangkap ID role user saat ini[cite: 1]
                                        $CURRENT_ROLE = $DATA_LAMA['role'] ?? '';
                                        // Perulangan untuk setiap baris data role[cite: 1]
                                        while ($ROW_ROLE = mysqli_fetch_assoc($QUERY_ROLES)) {
                                            // Menandai variabel ID Role[cite: 1]
                                            $ID_ROLE   = $ROW_ROLE['id_tipe_user'];
                                            // Menandai variabel nama Role[cite: 1]
                                            $NAMA_ROLE = $ROW_ROLE['tipe_user'];
                                            // Cek apakah ID role match dengan role pengguna saat ini untuk atribut 'selected'[cite: 1]
                                            $SELECTED  = ($CURRENT_ROLE == $ID_ROLE) ? 'selected' : '';
                                            // Tampilkan tag option pilihan[cite: 1]
                                            echo "<option value='" . htmlspecialchars($ID_ROLE) . "' $SELECTED>" . htmlspecialchars($NAMA_ROLE) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Banner alert pemicu modal ganti password[cite: 1] -->
                            <div class="alert alert-light-danger mt-3 mb-3">
                                <small>
                                    <!-- Ikon kunci gembok[cite: 1] -->
                                    <i class="bi bi-shield-lock-fill"></i> <b>Password:</b> 
                                    <!-- Link jangkar pembuka pop-up modal ganti password[cite: 1] -->
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalGantiPassword" class="text-danger fw-bold" style="text-decoration: underline;">
                                        Ganti password anda disini
                                    </a>
                                </small>
                            </div>

                            <!-- Tombol aksi pembatalan dan submit form[cite: 1] -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <!-- Tombol kembali/batal ke halaman tabel[cite: 1] -->
                                <a href="index.php?pages=table" class="btn btn-secondary">Batal</a>
                                <!-- Tombol submit pengiriman pembaruan profil[cite: 1] -->
                                <button type="submit" name="UpdateProfile" class="btn btn-warning">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </section>

<!-- Pop-up Modal Bootstrap untuk Form Ganti Password[cite: 1] -->
<div class="modal fade text-left" id="modalGantiPassword" tabindex="-1" role="dialog" aria-labelledby="modalGantiPasswordLabel" aria-hidden="true">
    <!-- Kontainer dialog modal rata tengah[cite: 1] -->
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <!-- Konten utama komponen modal[cite: 1] -->
        <div class="modal-content">
            <!-- Header modal dengan latar warna merah (danger)[cite: 1] -->
            <div class="modal-header bg-danger text-white">
                <!-- Judul modal[cite: 1] -->
                <h5 class="modal-title white" id="modalGantiPasswordLabel">
                    <i class="bi bi-shield-lock me-2"></i>Ganti Password
                </h5>
                <!-- Tombol silang untuk menutup modal[cite: 1] -->
                <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            
            <!-- Form pengiriman data ganti password ke halaman ini sendiri[cite: 1] -->
            <form action="" method="POST" novalidate>
                <!-- Body tempat bidang input modal[cite: 1] -->
                <div class="modal-body">
                    
                    <!-- Input ID User tersembunyi yang akan dikirimkan via POST[cite: 1] -->
                    <input type="hidden" name="IdUserPassword" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly>
                    
                    <!-- Tampilan readonly ID petugas[cite: 1] -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">ID Petugas</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly>
                    </div>

                    <!-- Tampilan nama petugas[cite: 1] -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Nama Petugas</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['nama_petugas'] ?? '') ?>" >
                    </div>

                    <!-- Garis pembagi horisontal modal[cite: 1] -->
                    <hr class="my-3">

                    <!-- Field input password lama[cite: 1] -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Password Lama</label>
                        <input type="password" name="PassLama" class="form-control" placeholder="Masukkan password lama" required>
                    </div>

                    <!-- Field input password baru[cite: 1] -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Password Baru</label>
                        <input type="password" name="PassBaru" class="form-control" placeholder="Masukkan password baru" minlength="6" required>
                        <small class="text-danger d-block mt-1"><i class="bi bi-info-circle"></i> Password minimal 6 karakter!</small>
                    </div>

                    <!-- Field input konfirmasi password baru[cite: 1] -->
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="PassBaruConfirm" class="form-control" placeholder="Ketik ulang password baru" minlength="6" required>
                    </div>

                </div>

                <!-- Footer modal berisi tombol aksi[cite: 1] -->
                <div class="modal-footer">
                    <!-- Tombol batal menutup modal[cite: 1] -->
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <span class="d-none d-sm-block">Batal</span>
                    </button>
                    <!-- Tombol submit pengiriman ganti password[cite: 1] -->
                    <button type="submit" name="UpdatePassword" class="btn btn-danger ms-1">
                        <span class="d-none d-sm-block">Simpan Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<!-- Script JavaScript client-side untuk pratinjau gambar secara otomatis[cite: 1] -->
<script>
    // Memasang event listener saat input file mengalami perubahan pilihan[cite: 1]
    document.getElementById('photoInput')?.addEventListener('change', function(e) {
        // Ambil file pertama yang dipilih pengguna[cite: 1]
        const file = e.target.files[0];
        // Jika file ditemukan[cite: 1]
        if (file) {
            // Buat objek FileReader baru[cite: 1]
            const reader = new FileReader();
            // Tentukan fungsi penanganan saat file selesai dibaca[cite: 1]
            reader.onload = function(evt) {
                // Set atribut src elemen elemen imagePreview ke hasil pembacaan file[cite: 1]
                document.getElementById('imagePreview').src = evt.target.result;
            }
            // Mendorong pembacaan berkas file sebagai Data URL[cite: 1]
            reader.readAsDataURL(file);
        }
    });
</script>