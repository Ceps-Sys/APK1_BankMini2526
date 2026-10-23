<?php
// Memanggil file fungsi/helper eksternal
require_once __DIR__ . '/../../inc/function.php';

// Menangkap rute halaman dari parameter URL GET 'pages'
$PAGE_ROUTE = $_GET['pages'] ?? '';
$IS_PENGGUNA = ($PAGE_ROUTE === 'tambah_petugas');
// Menghasilkan ID otomatis pengawas
$AUTO_ID = autonumber('tbl_users', 'id_user', 7, 'PGW');

// Mengeksekusi query untuk mengambil ID Tipe User
$QUERY_TIPE_USER = mysqli_query($KONEKSI, "SELECT id_tipe_user FROM tbl_tipe_user WHERE tipe_user='Pengawas'");
if ($QUERY_TIPE_USER && mysqli_num_rows($QUERY_TIPE_USER) > 0) {
    $ROW = mysqli_fetch_assoc($QUERY_TIPE_USER);
    $ID_TIPE_USER = $ROW['id_tipe_user'];
} else {
    $ID_TIPE_USER = 3; 
}
?>
    
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Data Pengawas</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Input Pengawas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title text-white">Form Input Data</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            
                            <?php 
                            if (isset($_SESSION['Sess_FormErrors'])) {
                                foreach ($_SESSION['Sess_FormErrors'] as $ERR) {
                                    echo "<div class='alert alert-danger mb-3'>".htmlspecialchars($ERR)."</div>";
                                }
                                unset($_SESSION['Sess_FormErrors']);
                            }
                            ?>

                            
                            <!-- [PENTING] Action diarahkan ke rute prosestambah_petugas -->
                            <form class="form form-horizontal" method="POST" action="index.php?pages=prosestambah_petugas" enctype="multipart/form-data">
                                <div class="form-body">
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="id_petugas">ID Petugas</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="id_petugas" name="IdPetugas" value="<?php echo $AUTO_ID; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="nama_petugas">Nama Lengkap</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" id="nama_petugas" name="NamaPetugas" placeholder="Masukkan Nama" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="alamat_petugas">Alamat</label>
                                        </div>
                                        <div class="col-md-10">
                                            <textarea class="form-control" id="alamat_petugas" name="AlamatPetugas" rows="3" placeholder="Masukkan Alamat" required></textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="telepon_petugas">Nomor Telepon</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="tel" class="form-control" id="telepon_petugas" name="TeleponPetugas" placeholder="08xxxxxxxxxx" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label>Jenis Kelamin</label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="Jengkel" id="laki" value="L" checked>
                                                <label class="form-check-label" for="laki">Laki-laki</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="Jengkel" id="perempuan" value="P">
                                                <label class="form-check-label" for="perempuan">Perempuan</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3 <?php echo $IS_PENGGUNA ? 'd-none' : ''; ?>">
                                        <div class="col-md-2">
                                            <label for="email">Email</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="email" class="form-control" id="email" name="Email" placeholder="petugas@example.com" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="password" class="form-control" id="password" name="Password" placeholder="Password" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="password2">Confirm Password</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="password" class="form-control" id="password2" name="Password2" placeholder="Konfirmasi Password" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-2">
                                            <label for="photo">Photo</label>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="photo" name="Photo" accept="image/*">
                                            </div>
                                            <div class="mt-2">
                                                <img id="imagePreview" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22150%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20150%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_185%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20Open%20Sans%2C%20Arial%2C%20sans-serif%3Bfont-size%3A12pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_185%22%3E%3Crect%20width%3D%22150%22%20height%3D%22150%22%20fill%3D%22%23eee%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2235%22%20y%3D%2280%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" alt="Preview">
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="Role" value="<?php echo $ID_TIPE_USER; ?>">

                                    <div class="col-12 d-flex justify-content-start mt-4">
                                        <button type="submit" name="TambahData" class="btn btn-info me-1 mb-1 text-white">Tambah Data</button>
                                        <a href="index.php?pages=tampil_petugas" class="btn btn-warning me-1 mb-1 text-white">Batal</a>
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

<script>
    const defaultPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22150%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20150%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_185%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20Open%20Sans%2C%20Arial%2C%20sans-serif%3Bfont-size%3A12pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_185%22%3E%3Crect%20width%3D%22150%22%20height%3D%22150%22%20fill%3D%22%23eee%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2235%22%20y%3D%2280%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E";
    const fileInput = document.getElementById('photo');
    const previewImage = document.getElementById('imagePreview');

    fileInput?.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
        } else {
            previewImage.src = defaultPlaceholder;
        }
    });
</script>