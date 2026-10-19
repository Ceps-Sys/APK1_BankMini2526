<?php
// 1. Tangkap parameter 'pages' dari URL router index.php
$page = $_GET['pages'] ?? '';

// 2. Jika diakses dari 'tambah_pengguna', sembunyikan Email
$isPengguna = ($page === 'tambah_pengguna');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin & Pengguna - Mazer</title>
    
    <link rel="stylesheet" crossorigin href="../../admin/assets1/compiled/css/app.css">
    <link rel="stylesheet" crossorigin href="../../admin/assets1/compiled/css/app-dark.css">
</head>

<body>
    <script src="../../admin/assets1/static/js/initTheme.js"></script>
    
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><?php echo $isPengguna ? 'Tambah Data Pengguna' : 'Tambah Data Admin'; ?></h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Form Input</li>
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
                                <!-- ACTION DIARAHKAN KE SWITCH-CASE PROSESTAMBAH_INNER -->
                                <form class="form form-horizontal" method="POST" action="index.php?pages=prosestambah_inner" enctype="multipart/form-data">
                                    <div class="form-body">
                                        
                                        <!-- ID ADMIN (READONLY) -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="id_admin">ID Admin</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" id="id_admin" name="id_admin" readonly placeholder="Auto Generated">
                                            </div>
                                        </div>

                                        <!-- NAMA -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="nama_admin">Nama Lengkap</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" id="nama_admin" name="nama_admin" placeholder="Masukkan Nama" required>
                                            </div>
                                        </div>

                                        <!-- ALAMAT -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="alamat_admin">Alamat</label>
                                            </div>
                                            <div class="col-md-10">
                                                <textarea class="form-control" id="alamat_admin" name="alamat_admin" rows="3" placeholder="Masukkan Alamat"></textarea>
                                            </div>
                                        </div>

                                        <!-- TELEPON -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="telepon_admin">Nomor Telepon</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="tel" class="form-control" id="telepon_admin" name="telepon_admin" placeholder="08xxxxxxxxxx">
                                            </div>
                                        </div>

                                        <!-- JENIS KELAMIN -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label>Jenis Kelamin</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="laki" value="L" checked>
                                                    <label class="form-check-label" for="laki">Laki-laki</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="perempuan" value="P">
                                                    <label class="form-check-label" for="perempuan">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- EMAIL (HIDDEN SECARA OTOMATIS JIKA DIKLIK DARI TABEL PENGGUNA) -->
                                        <div class="row mb-3 <?php echo $isPengguna ? 'd-none' : ''; ?>">
                                            <div class="col-md-2">
                                                <label for="email">Email</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="admin@example.com">
                                            </div>
                                        </div>

                                        <!-- USERNAME -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="username">Username</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                                            </div>
                                        </div>

                                        <!-- PASSWORD -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="password">Password</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                            </div>
                                        </div>

                                        <!-- CONFIRM PASSWORD (SUDAH DIKEMBALIKAN) -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="confirm_password">Confirm Password</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password">
                                            </div>
                                        </div>

                                        <!-- PHOTO PATH / FILE -->
                                        <div class="row mb-3">
                                            <div class="col-md-2">
                                                <label for="path_photo_admin">Photo</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="input-group">
                                                    <input type="file" class="form-control" id="path_photo_admin" name="path_photo_admin" accept="image/*">
                                                </div>
                                                <div class="mt-2">
                                                    <img id="imagePreview" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22150%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20150%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_185%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20Open%20Sans%2C%20Arial%2C%20sans-serif%3Bfont-size%3A12pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_185%22%3E%3Crect%20width%3D%22150%22%20height%3D%22150%22%20fill%3D%22%23eee%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2235%22%20y%3D%2280%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" alt="Preview">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TOMBOL AKSI -->
                                        <div class="col-12 d-flex justify-content-start mt-4">
                                            <button type="submit" class="btn btn-info me-1 mb-1 text-white">Tambah Data</button>
                                            <a href="index.php?pages=<?php echo $isPengguna ? 'tabel_pengguna' : 'tabel_admin'; ?>" class="btn btn-warning me-1 mb-1 text-white">Batal</a>
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

    <script src="../../admin/assets1/static/js/components/dark.js"></script>
    <script src="../../admin/assets1/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../admin/assets1/compiled/js/app.js"></script>
    <script src="../../admin/assets1/extensions/jquery/jquery.min.js"></script>
    <script src="../../admin/assets1/extensions/parsleyjs/parsley.min.js"></script>
    <script src="../../admin/assets1/static/js/pages/parsley.js"></script>

    <script>
        const defaultPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22150%22%20height%3D%22150%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20150%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_185%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20Open%20Sans%2C%20Arial%2C%20sans-serif%3Bfont-size%3A12pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_185%22%3E%3Crect%20width%3D%22150%22%20height%3D%22150%22%20fill%3D%22%23eee%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2235%22%20y%3D%2280%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E";
        const fileInput = document.getElementById('path_photo_admin');
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
</body>
</html>