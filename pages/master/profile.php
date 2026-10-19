<?php
// Memanggil file pustaka fungsi utama
require_once __DIR__ . '/../../inc/function.php';
global $KONEKSI;

// 1. Mengambil Email dari Session (User yang sedang login)
$EMAIL_SESSION = $_SESSION['Sess_Email'] ?? '';

// 2. Query Ambil Data User & Detail Admin
$QUERY_USER = "SELECT tbl_users.*, tbl_admin.*, tbl_tipe_user.tipe_user 
               FROM tbl_users 
               LEFT JOIN tbl_admin ON tbl_users.id_user = tbl_admin.id_user 
               LEFT JOIN tbl_tipe_user ON tbl_users.role = tbl_tipe_user.id_tipe_user 
               WHERE tbl_users.email = '$EMAIL_SESSION'";

$HASIL_USER = mysqli_query($KONEKSI, $QUERY_USER);
$DATA_USER = mysqli_fetch_assoc($HASIL_USER);

// ID User & Nama
$ID_USER_FINAL = $DATA_USER['id_user'] ?? '';
$NAMA_USER = !empty($DATA_USER['nama_admin']) ? $DATA_USER['nama_admin'] : 'Pengguna';
$ROLE_USER = !empty($DATA_USER['tipe_user']) ? $DATA_USER['tipe_user'] : 'User';

// 3. Menentukan Foto Profil / Avatar Saat Ini
$FOTO_ADMIN = $DATA_USER['path_photo_admin'] ?? '';
$FOTO_SAAT_INI = '../../images/avatar/1.jpg'; // Default

if (!empty($FOTO_ADMIN)) {
    if (file_exists(__DIR__ . '/../../images/user_admin/' . $FOTO_ADMIN)) {
        $FOTO_SAAT_INI = '../../images/user_admin/' . $FOTO_ADMIN;
    } elseif (file_exists(__DIR__ . '/../../images/avatar/' . $FOTO_ADMIN)) {
        $FOTO_SAAT_INI = '../../images/avatar/' . $FOTO_ADMIN;
    }
}
?>

<div class="page-heading">
    <!-- Title / Header Halaman -->
    <div class="page-title mb-4">
        <h3>Profil Saya</h3>
        <p class="text-subtitle text-muted">Informasi data akun pengguna sistem Bank Mini</p>
    </div>

    <!-- Alert Error / Sukses dari Session -->
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

    <section class="section">
        <form action="index.php?pages=proses_edit" method="POST" enctype="multipart/form-data">
            <!-- Hidden inputs -->
            <input type="hidden" name="IdAdmin" value="<?= htmlspecialchars($DATA_USER['id_admin'] ?? '') ?>">
            <input type="hidden" name="IdUser" value="<?= htmlspecialchars($ID_USER_FINAL) ?>">
            <input type="hidden" name="FotoLama" value="<?= htmlspecialchars($FOTO_ADMIN) ?>">

            <div class="row">
                <!-- SISI KIRI: KARTU RINGKASAN PROFIL -->
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center d-flex flex-column align-items-center justify-content-center py-5">
                            <div class="avatar avatar-5xl mb-3">
                                <img id="imagePreview" src="<?= htmlspecialchars($FOTO_SAAT_INI) ?>?t=<?= time() ?>" alt="Foto Profil" class="rounded-circle" style="width: 140px; height: 140px; object-fit: cover;">
                            </div>
                            <h4 class="card-title fw-bold mb-1"><?= htmlspecialchars($NAMA_USER) ?></h4>
                            <p class="text-muted mb-2"><?= htmlspecialchars($DATA_USER['email'] ?? '') ?></p>
                            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                                <?= htmlspecialchars($ROLE_USER) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SISI KANAN: FORM EDIT DETAIL PENGGUNA -->
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Edit Detail Pengguna</h5>
                        </div>
                        <div class="card-body">
                            
                            <!-- Email System -->
                            <div class="form-group mb-3">
                                <label class="fw-bold mb-1">Email System</label>
                                <input type="email" name="Email" class="form-control" value="<?= htmlspecialchars($DATA_USER['email'] ?? '') ?>" required>
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="form-group mb-3">
                                <label class="fw-bold mb-1">Nama Lengkap</label>
                                <input type="text" name="NamaAdmin" class="form-control" value="<?= htmlspecialchars($DATA_USER['nama_admin'] ?? '') ?>" required>
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="form-group mb-3">
                                <label class="fw-bold mb-1">Nomor Telepon</label>
                                <input type="text" name="TeleponAdmin" class="form-control" value="<?= htmlspecialchars($DATA_USER['telepon_admin'] ?? '') ?>" required>
                            </div>

                            <!-- Alamat -->
                            <div class="form-group mb-3">
                                <label class="fw-bold mb-1">Alamat</label>
                                <textarea name="AlamatAdmin" class="form-control" rows="3" required><?= htmlspecialchars($DATA_USER['alamat_admin'] ?? '') ?></textarea>
                            </div>

                            <!-- Pilih Avatar Bawaan -->
                            <div class="form-group mb-4">
                                <label class="fw-bold mb-2 d-block">Pilih Avatar Bawaan</label>
                                <div class="d-flex align-items-center gap-3">
                                    <?php 
                                    $AVATARS = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg'];
                                    foreach ($AVATARS as $INDEX => $AVTR): 
                                        $IS_CHECKED = ($FOTO_ADMIN === $AVTR) ? 'checked' : '';
                                    ?>
                                        <div class="text-center">
                                            <input class="form-check-input mb-1 d-block mx-auto" type="radio" name="AvatarBawaan" id="avatar_<?= $INDEX ?>" value="<?= $AVTR ?>" <?= $IS_CHECKED ?> onchange="pilihAvatar('<?= $AVTR ?>')">
                                            <label for="avatar_<?= $INDEX ?>" style="cursor: pointer;">
                                                <img src="../../images/avatar/<?= $AVTR ?>" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;" alt="Avatar">
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Unggah Foto Kustom -->
                            <div class="form-group mb-4">
                                <label class="fw-bold mb-1">Atau Unggah Foto Kustom</label>
                                <input type="file" class="form-control" id="photoInput" name="Photo" accept=".jpg, .jpeg, .png, .bmp" onchange="previewKustom(event)">
                                <small class="text-muted d-block mt-1">Ekstensi: JPG, PNG, BMP (Maks. 2MB). Upload foto baru akan menimpa pilihan Avatar Bawaan.</small>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <!-- Tombol Pemicu Modal Ganti Password (Merah/Kuning) -->
                                <button type="button" class="btn btn-warning text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalGantiPassword">
                                    Ganti Password
                                </button>
                                <button type="submit" name="UpdateProfile" class="btn btn-primary fw-bold">
                                    Simpan Perubahan
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <!-- ========================================== -->
    <!-- MODAL GANTI PASSWORD                       -->
    <!-- ========================================== -->
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
                
                <form action="" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="IdUserPassword" value="<?= htmlspecialchars($ID_USER_FINAL) ?>">
                        
                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1">ID Admin / User</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($ID_USER_FINAL) ?>" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-bold mb-1">Nama Admin / User</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($DATA_USER['nama_admin'] ?? '') ?>" readonly>
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
    <!-- ========================================== -->

</div>

<!-- Script Preview Gambar Real-time (Avatar / Unggah) -->
<script>
    function pilihAvatar(fileName) {
        // Reset file input jika memilih avatar radio
        document.getElementById('photoInput').value = '';
        document.getElementById('imagePreview').src = '../../images/avatar/' + fileName;
    }

    function previewKustom(event) {
        const file = event.target.files[0];
        if (file) {
            // Uncheck semua radio button avatar
            const radioButtons = document.querySelectorAll('input[name="AvatarBawaan"]');
            radioButtons.forEach(radio => radio.checked = false);

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>