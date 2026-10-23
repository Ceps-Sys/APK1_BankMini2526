<?php
// Memanggil file helper/fungsi eksternal untuk koneksi dan fungsi pendukung[cite: 1]
require_once __DIR__ . '/../../inc/function.php';
global $KONEKSI;

// 1. Menangkap parameter ID dari URL GET query string[cite: 1]
$ID_USER_URL = $_GET['Id'] ?? '';

// 2. Menyusun kueri SQL khusus untuk Pengawas (menggabungkan tbl_users dan tbl_pengawas)
$QUERY_EDIT = "SELECT tbl_users.*, tbl_pengawas.* 
               FROM tbl_users 
               INNER JOIN tbl_pengawas ON tbl_users.id_user = tbl_pengawas.id_user 
               WHERE tbl_users.id_user = '$ID_USER_URL'";

$HASIL_EDIT = mysqli_query($KONEKSI, $QUERY_EDIT) or die("Gagal mengambil data: " . mysqli_error($KONEKSI));
$DATA_LAMA = mysqli_fetch_assoc($HASIL_EDIT);

// Jika data tidak ditemukan via join, lakukan fallback pencarian ke tbl_users
if (!$DATA_LAMA) {
    $QUERY_FALLBACK = "SELECT * FROM tbl_users WHERE id_user = '$ID_USER_URL'";
    $HASIL_FALLBACK = mysqli_query($KONEKSI, $QUERY_FALLBACK);
    $DATA_LAMA = mysqli_fetch_assoc($HASIL_FALLBACK);
}

$ID_USER_FINAL = !empty($DATA_LAMA['id_user']) ? $DATA_LAMA['id_user'] : $ID_USER_URL;

// 3. Menangkap nama file foto pengawas dari database[cite: 1]
$FOTO_PENGAWAS = $DATA_LAMA['path_photo_pengawas'] ?? '';
if (!empty($FOTO_PENGAWAS) && file_exists(__DIR__ . '/../../images/user_pengawas/' . $FOTO_PENGAWAS)) {
    $FOTO_SAAT_INI = '../../images/user_pengawas/' . $FOTO_PENGAWAS;
} else {
    $FOTO_SAAT_INI = '../../images/avatar/1.jpg';
}

// Proses eksekusi ganti password via modal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['UpdatePassword'])) {
    $result = ganti_password($_POST);
    if ($result) {
        echo "<script>
            alert('Password berhasil diubah!');
            window.location.href = 'index.php?pages=edit_pengawas&Id=" . htmlspecialchars($_POST['IdUserPassword']) . "';
        </script>";
        exit();
    } else {
        if (isset($_SESSION['Sess_FormErrors'])) {
            echo "<script>
                alert('" . implode("\\n", $_SESSION['Sess_FormErrors']) . "');
            </script>";
            unset($_SESSION['Sess_FormErrors']);
        }
    }
}
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3>Edit Data Pengawas</h3>
            </div>
        </div>
    </div>

    <section class="section mt-3">
        <div class="card">
            <div class="card-body">

                <?php if (isset($_SESSION['Sess_FormErrors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <?php foreach ($_SESSION['Sess_FormErrors'] as $ERR): ?>
                            <p class="mb-0"><?= htmlspecialchars($ERR) ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['Sess_FormErrors']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['Sess_SuccessMessage'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <?= htmlspecialchars($_SESSION['Sess_SuccessMessage']) ?>
                    </div>
                    <?php unset($_SESSION['Sess_SuccessMessage']); ?>
                <?php endif; ?>

                <!-- Form utama diarahkan ke prosesedit_pengawas -->
               <form action="index.php?pages=prosesedit_pengawas" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="IdUser" value="<?= htmlspecialchars($ID_USER_FINAL) ?>">
                    <input type="hidden" name="FotoLama" value="<?= htmlspecialchars($DATA_LAMA['path_photo_pengawas'] ?? '') ?>">

                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <label class="form-label fw-bold d-block mb-2">Foto Profil</label>
                            <img id="imagePreview" src="<?= htmlspecialchars($FOTO_SAAT_INI) ?>" class="img-thumbnail mb-2" style="width: 160px; height: 160px; object-fit: cover;" alt="Preview Foto">
                            <input type="file" class="form-control" id="photoInput" name="Photo" accept=".png, .jpg, .jpeg" />
                            <small class="text-muted d-block mt-1">!! Biarkan kosong jika tidak ingin mengganti foto</small>
                        </div>

                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ID User</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Username / Email</label>
                                    <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['email'] ?? '') ?>" required />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Pengawas</label>
                                <input type="text" name="NamaPengawas" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['nama_pengawas'] ?? '') ?>" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Alamat</label>
                                <input type="text" name="AlamatPengawas" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['alamat_pengawas'] ?? '') ?>" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Telepon</label>
                                <input type="text" name="TeleponPengawas" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['telepon_pengawas'] ?? '') ?>" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Jenis Kelamin</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Jengkel" value="L" id="jengkel_l" <?= (($DATA_LAMA['jenkel_pengawas'] ?? '') === 'L') ? 'checked' : '' ?> />
                                    <label class="form-check-label" for="jengkel_l">Laki-Laki</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Jengkel" value="P" id="jengkel_p" <?= (($DATA_LAMA['jenkel_pengawas'] ?? '') === 'P') ? 'checked' : '' ?> />
                                    <label class="form-check-label" for="jengkel_p">Perempuan</label>
                                </div>
                            </div>

                            <div class="alert alert-light-danger mt-3 mb-3">
                                <small>
                                    <i class="bi bi-shield-lock-fill"></i> <b>Password:</b> 
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalGantiPassword" class="text-danger fw-bold" style="text-decoration: underline;">
                                        Ganti password anda disini
                                    </a>
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="index.php?pages=tampil_pengawas" class="btn btn-secondary">Batal</a>
                                <button type="submit" name="UpdateProfile" class="btn btn-warning">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </section>

<!-- Pop-up Modal Ganti Password -->
<div class="modal fade text-left" id="modalGantiPassword" tabindex="-1" role="dialog" aria-labelledby="modalGantiPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title white" id="modalGantiPasswordLabel">
                    <i class="bi bi-shield-lock me-2"></i>Ganti Password
                </h5>
                <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            
            <form action="" method="POST" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="IdUserPassword" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly>
                    
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">ID Pengawas</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Nama Pengawas</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($DATA_LAMA['nama_pengawas'] ?? '') ?>" readonly>
                    </div>

                    <hr class="my-3">

                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Password Lama</label>
                        <input type="password" name="PassLama" class="form-control" placeholder="Masukkan password lama" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Password Baru</label>
                        <input type="password" name="PassBaru" class="form-control" placeholder="Masukkan password baru" minlength="6" required>
                        <small class="text-danger d-block mt-1"><i class="bi bi-info-circle"></i> Password minimal 6 karakter!</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-bold mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="PassBaruConfirm" class="form-control" placeholder="Ketik ulang password baru" minlength="6" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="UpdatePassword" class="btn btn-danger ms-1">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    document.getElementById('photoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('imagePreview').src = evt.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>