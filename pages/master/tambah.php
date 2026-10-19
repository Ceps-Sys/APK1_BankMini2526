<?php
// Memanggil file fungsi/helper eksternal[cite: 2]
require_once __DIR__ . '/../../inc/function.php';

// Menangkap rute halaman dari parameter URL GET 'pages'[cite: 2]
$PAGE_ROUTE = $_GET['pages'] ?? '';
// Pengecekan kondisi boolean apakah halaman saat ini adalah 'tambah_pengguna'[cite: 2]
$IS_PENGGUNA = ($PAGE_ROUTE === 'tambah_pengguna');
// Menghasilkan ID otomatis menggunakan fungsi autonumber dari database[cite: 2]
$AUTO_ID = autonumber('tbl_users', 'id_user', 7, 'ADM');
?>
    
<!-- Kontainer utama halaman input data[cite: 2] -->
<div class="page-heading">
    <!-- Kontainer judul halaman dan navigasi breadcrumb[cite: 2] -->
    <div class="page-title">
        <!-- Baris elemen layout Bootstrap[cite: 2] -->
        <div class="row">
            <!-- Kolom judul halaman kondisional[cite: 2] -->
            <div class="col-12 col-md-6 order-md-1 order-last">
                <!-- Tampilkan judul 'Tambah Data Pengguna' atau 'Tambah Data Admin'[cite: 2] -->
                <h3><?php echo $IS_PENGGUNA ? 'Tambah Data Pengguna' : 'Tambah Data Admin'; ?></h3>
            </div>
            <!-- Kolom breadcrumb[cite: 2] -->
            <div class="col-12 col-md-6 order-md-2 order-first">
                <!-- Navigasi breadcrumb[cite: 2] -->
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <!-- Daftar link urutan halaman[cite: 2] -->
                    <ol class="breadcrumb">
                        <!-- Link ke beranda[cite: 2] -->
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <!-- Item breadcrumb aktif saat ini[cite: 2] -->
                        <li class="breadcrumb-item active" aria-current="page">Form Input</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bagian seksi layout form[cite: 2] -->
    <section id="basic-vertical-layouts">
        <!-- Baris perataan tinggi card[cite: 2] -->
        <div class="row match-height">
            <!-- Kolom penuh (12 kolom)[cite: 2] -->
            <div class="col-12">
                <!-- Elemen card Bootstrap[cite: 2] -->
                <div class="card">
                    <!-- Header kartu berwarna biru info[cite: 2] -->
                    <div class="card-header bg-info">
                        <!-- Judul dalam header kartu[cite: 2] -->
                        <h4 class="card-title text-white">Form Input Data</h4>
                    </div>
                    <!-- Pembungkus konten kartu[cite: 2] -->
                    <div class="card-content">
                        <!-- Bodi kartu[cite: 2] -->
                        <div class="card-body">
                            
                            <?php 
                            // Pengecekan ada/tidaknya daftar kesalahan validasi form di session[cite: 2]
                            if (isset($_SESSION['Sess_FormErrors'])) {
                                // Perulangan untuk menampilkan setiap pesan kesalahan[cite: 2]
                                foreach ($_SESSION['Sess_FormErrors'] as $ERR) {
                                    // Tampilkan komponen alert danger[cite: 2]
                                    echo "<div class='alert alert-danger mb-3'>".htmlspecialchars($ERR)."</div>";
                                }
                                // Hapus session error[cite: 2]
                                unset($_SESSION['Sess_FormErrors']);
                            }
                            ?>

                            <!-- Form pendaftaran admin/pengguna baru[cite: 2] -->
                            <form class="form form-horizontal" method="POST" action="index.php?pages=prosestambah_admin" enctype="multipart/form-data">
                                <!-- Bodi dari elemen form[cite: 2] -->
                                <div class="form-body">
                                    
                                    <!-- Input tersembunyi default nilai role[cite: 2] -->
                                    <input type="hidden" name="Role" value="1">

                                    <!-- Baris input ID Admin/User[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label ID[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="id_admin">ID User/Admin</label>
                                        </div>
                                        <!-- Field ID terisi nomor otomatis[cite: 2] -->
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="id_admin" name="IdAdmin" value="<?php echo $AUTO_ID; ?>" readonly>
                                        </div>
                                    </div>

                                    <!-- Baris input Nama Lengkap[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Nama[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="nama_admin">Nama Lengkap</label>
                                        </div>
                                        <!-- Field Nama Lengkap[cite: 2] -->
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="nama_admin" name="NamaAdmin" placeholder="Masukkan Nama" required>
                                        </div>
                                    </div>

                                    <!-- Baris input Alamat[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Alamat[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="alamat_admin">Alamat</label>
                                        </div>
                                        <!-- Field Textarea Alamat[cite: 2] -->
                                        <div class="col-md-10">
                                            <textarea class="form-control" id="alamat_admin" name="AlamatAdmin" rows="3" placeholder="Masukkan Alamat" required></textarea>
                                        </div>
                                    </div>

                                    <!-- Baris input Nomor Telepon[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Nomor Telepon[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="telepon_admin">Nomor Telepon</label>
                                        </div>
                                        <!-- Field input tel[cite: 2] -->
                                        <div class="col-md-10">
                                            <input type="tel" class="form-control" id="telepon_admin" name="TeleponAdmin" placeholder="08xxxxxxxxxx" required>
                                        </div>
                                    </div>

                                    <!-- Baris radio button jenis kelamin[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Jenis Kelamin[cite: 2] -->
                                        <div class="col-md-2">
                                            <label>Jenis Kelamin</label>
                                        </div>
                                        <!-- Radio pilihan Laki-laki / Perempuan[cite: 2] -->
                                        <div class="col-md-10">
                                            <!-- Pilihan Laki-laki[cite: 2] -->
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="Jengkel" id="laki" value="L" checked>
                                                <label class="form-check-label" for="laki">Laki-laki</label>
                                            </div>
                                            <!-- Pilihan Perempuan[cite: 2] -->
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="Jengkel" id="perempuan" value="P">
                                                <label class="form-check-label" for="perempuan">Perempuan</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Baris input Email (disembunyikan kondisional jika $IS_PENGGUNA true)[cite: 2] -->
                                    <div class="row mb-3 <?php echo $IS_PENGGUNA ? 'd-none' : ''; ?>">
                                        <!-- Label Email[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="email">Email</label>
                                        </div>
                                        <!-- Field Input Email[cite: 2] -->
                                        <div class="col-md-10">
                                            <input type="email" class="form-control" id="email" name="Email" placeholder="admin@example.com" required>
                                        </div>
                                    </div>

                                    <!-- Baris input Password utama[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Password[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="password">Password</label>
                                        </div>
                                        <!-- Field Input Password[cite: 2] -->
                                        <div class="col-md-10">
                                            <input type="password" class="form-control" id="password" name="Password" placeholder="Password" required>
                                        </div>
                                    </div>

                                    <!-- Baris input Konfirmasi Password[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Confirm Password[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="password2">Confirm Password</label>
                                        </div>
                                        <!-- Field Input Konfirmasi Password[cite: 2] -->
                                        <div class="col-md-10">
                                            <input type="password" class="form-control" id="password2" name="Password2" placeholder="Konfirmasi Password" required>
                                        </div>
                                    </div>

                                    <!-- Baris input unggah berkas Foto[cite: 2] -->
                                    <div class="row mb-3">
                                        <!-- Label Photo[cite: 2] -->
                                        <div class="col-md-2">
                                            <label for="photo">Photo</label>
                                        </div>
                                        <!-- Field file photo dan kontainer preview gambar[cite: 2] -->
                                        <div class="col-md-10">
                                            <!-- Komponen input group[cite: 2] -->
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="photo" name="Photo" accept="image/*">
                                            </div>
                                            <!-- Kontainer preview[cite: 2] -->
                                            <div class="mt-2">
                                                <!-- Gambar pratinjau dengan placeholder SVG default[cite: 2] -->
                                                <img id="imagePreview" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22150%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20150%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_185%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20Open%20Sans%2C%20Arial%2C%20sans-serif%3Bfont-size%3A12pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_185%22%3E%3Crect%20width%3D%22150%22%20height%3D%22150%22%20fill%3D%22%23eee%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2235%22%20y%3D%2280%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" alt="Preview">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Baris tombol submit dan batal[cite: 2] -->
                                    <div class="col-12 d-flex justify-content-start mt-4">
                                        <!-- Tombol simpan data[cite: 2] -->
                                        <button type="submit" name="TambahData" class="btn btn-info me-1 mb-1 text-white">Tambah Data</button>
                                        <!-- Tombol pembatalan[cite: 2] -->
                                        <a href="index.php?pages=akun_pengguna" class="btn btn-warning me-1 mb-1 text-white">Batal</a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Script JS penanganan pratinjau gambar lokal[cite: 2] -->
<script>
    // Menyimpan string placeholder SVG default[cite: 2]
    const defaultPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22150%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20150%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_185%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20Open%20Sans%2C%20Arial%2C%20sans-serif%3Bfont-size%3A12pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_185%22%3E%3Crect%20width%3D%22150%22%20height%3D%22150%22%20fill%3D%22%23eee%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2235%22%20y%3D%2280%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E";
    // Mendapatkan elemen input file photo[cite: 2]
    const fileInput = document.getElementById('photo');
    // Mendapatkan elemen tag img preview[cite: 2]
    const previewImage = document.getElementById('imagePreview');

    // Menambahkan event listener change pada input file[cite: 2]
    fileInput?.addEventListener('change', function(event) {
        // Ambil file yang dipilih[cite: 2]
        const file = event.target.files[0];

        // Jika file terpilih[cite: 2]
        if (file) {
            // Inisialisasi FileReader[cite: 2]
            const reader = new FileReader();
            // Jalankan event onload saat selesai membaca file[cite: 2]
            reader.onload = function(e) {
                // Set src gambar dengan hasil pembacaan[cite: 2]
                previewImage.src = e.target.result;
            }
            // Baca berkas sebagai data URL[cite: 2]
            reader.readAsDataURL(file);
        } else {
            // Jika pemilihan dibatalkan, reset ke gambar default[cite: 2]
            previewImage.src = defaultPlaceholder;
        }
    });
</script>