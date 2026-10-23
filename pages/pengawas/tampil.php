<style>
    /* Pengaturan gaya CSS tabel kustom agar tata letak border dan padding terlihat presisi */
    table {
        width: 100%;
        border-collapse: collapse !important;
    }
    table, th, td {
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
    }
    th, td {
        padding: 12px 8px !important;
    }
</style>

<!-- ============================================================ -->
<!-- 1. KEPALA HALAMAN & NAVIGASI BREADCRUMB                       -->
<!-- ============================================================ -->
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <!-- Judul Halaman Utama di Sebelah Kiri -->
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>TABEL AKUN PENGAWAS</h3>
            </div>
            <!-- Navigasi Breadcrumb Jalur Posisi Halaman di Sebelah Kanan -->
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Akun Pengawas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- ============================================================ -->
    <!-- 2. SEKSI UTAMA: KARTU TABEL DATA AKUN PENGAWAS                -->
    <!-- ============================================================ -->
    <section class="section">
        <div class="card">
            <!-- Header Kartu: Menampung Sub-Judul dan Deretan Tombol Kontrol -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Data Akun Pengawas</h5>
                <div>
                    <!-- Tombol Pemicu Pop-Up Modal Laporan -->
                    <button type="button" class="btn btn-info text-white btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalLaporan">
                        <i class="bi bi-file-earmark-text me-1"></i> Laporan
                    </button>
                    <!-- Tombol Pemicu Pop-Up Modal Tambah Modal/Pengawas -->
                    <button type="button" class="btn btn-info text-white btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalTambahModal">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Modal
                    </button>
                    <!-- Tautan Langsung ke Halaman Form Pendaftaran Pengawas Baru -->
                    <a href="index.php?pages=tambah_pengawas" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Tambah Pengawas
                    </a>
                </div>
            </div>

            <!-- Bodi Kartu: Tempat Elemen Tabel Dirender -->
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <!-- Judul Kepala Kolom Tabel -->
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>ID Pengawas</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat Pada</th>
                            <th>Diperbarui Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <!-- Bodi Data Tabel yang Diisi Secara Dinamis dari Database via PHP -->
                    <tbody>
                        <?php
                            // Memanggil pustaka fungsi pendukung dari folder inc untuk mendapatkan fungsi tampil()
                            require_once __DIR__ . '/../../inc/function.php';

                            // Query khusus Pengawas menggunakan INNER JOIN
                            $USERS = tampil("SELECT tbl_users.*, tbl_pengawas.path_photo_pengawas 
                            FROM tbl_users 
                            INNER JOIN tbl_pengawas ON tbl_users.id_user = tbl_pengawas.id_user 
                            WHERE tbl_users.role = 2
                            ORDER BY tbl_users.id_user ASC");
                            
                            // Inisialisasi variabel penomoran urut baris tabel
                            $NO = 1;

                            // Perulangan foreach untuk mengekstrak setiap baris data pengguna dari array $USERS
                            foreach ($USERS as $DATA) {
                                // Menangkap nama berkas foto pengawas dari hasil query
                                $NAMA_FOTO = $DATA['path_photo_pengawas']; 
                                // Menentukan rute gambar avatar default/bawaan
                                $SRC_FOTO = "../images/avatar/1.jpg"; 

                                // Memeriksa apakah kolom nama foto pada database tidak kosong
                                if (!empty($NAMA_FOTO)) {
                                    $PATH_PENGAWAS  = __DIR__ . "/../../images/user_pengawas/" . $NAMA_FOTO;
                                    $PATH_AVATAR = __DIR__ . "/../../images/avatar/" . $NAMA_FOTO;

                                    if (file_exists($PATH_PENGAWAS) && !is_dir($PATH_PENGAWAS)) {
                                        $SRC_FOTO = "../images/user_pengawas/" . $NAMA_FOTO;
                                    } elseif (file_exists($PATH_AVATAR) && !is_dir($PATH_AVATAR)) {
                                        $SRC_FOTO = "../images/avatar/" . $NAMA_FOTO;
                                    }
                                }
                                $SRC_FOTO .= "?t=" . time();
                            ?>
                        <!-- Baris Data Tabel Pengguna -->
                        <tr>
                            <td><?php echo $NO; ?></td>
                            <td class="text-center">
                                <img src="<?php echo htmlspecialchars($SRC_FOTO); ?>" 
                                     alt="Foto User" 
                                     width="40" 
                                     height="40" 
                                     class="rounded-circle" 
                                     style="object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($DATA['id_user']); ?></td>
                            <td><?php echo htmlspecialchars($DATA['email']); ?></td>  
                            <td>
                                <span class="badge bg-secondary">
                                    Role <?php echo htmlspecialchars($DATA['role']); ?>
                                </span>
                            </td> 
                            <td><?php echo htmlspecialchars($DATA['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($DATA['updated_at']); ?></td>  
                            <td>
                                <a href='index.php?pages=edit_pengawas&Id=<?php echo $DATA['id_user']; ?>' class="btn btn-warning btn-sm">EDIT</a>
                                <a href='index.php?pages=edit_pengawas&Id=<?php echo $DATA['id_user']; ?>' class="btn btn-warning btn-sm">EDIT MODAL</a>
                                <a href='index.php?pages=hapus_pengawas&Id=<?php echo $DATA['id_user']; ?>' class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengawas ini?')">DELETE</a>
                            </td>
                        </tr>
                        <?php
                            $NO++;
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- 3. POP-UP MODAL 1: CETAK / LIHAT LAPORAN                      -->
    <!-- ============================================================ -->
    <div class="modal fade text-left" id="modalLaporan" tabindex="-1" role="dialog" aria-labelledby="modalLaporanLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title white" id="modalLaporanLabel">
                <i class="bi bi-file-earmark-text me-2"></i>Cetak / Lihat Laporan
            </h5>
            <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                <i data-feather="x"></i>
            </button>
          </div>
          <form action="pages/pengawas/proses_modal_pengawas.php" method="POST">  
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="fw-bold mb-1">Periode Laporan</label>
                    <select class="form-select" name="periode" required>
                        <option value="harian">Harian</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-info text-white ms-1">Cetak Laporan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- 4. POP-UP MODAL 2: FORM TAMBAH DATA MODAL (PENGAWAS)           -->
    <!-- ============================================================ -->
    <div class="modal fade text-left" id="modalTambahModal" tabindex="-1" role="dialog" aria-labelledby="modalTambahModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title white" id="modalTambahModalLabel">
                <i class="bi bi-plus-circle me-2"></i>Form Tambah Data Modal
            </h5>
            <button type="button" class="close rounded-pill" data-bs-dismiss="modal" aria-label="Close">
                <i data-feather="x"></i>
            </button>
          </div>
          
           <?php
           // Menggunakan autonumber berawalan 'PTG'
           $AUTO_ID = autonumber('tbl_users', 'id_user', 7, 'PGW');
           $PAGE_ROUTE = $_GET['pages'] ?? '';
           $IS_PENGGUNA = ($PAGE_ROUTE === 'tambah_pengguna');
           ?> 
          <!-- id form diubah menjadi FormTambahPengawas dan diarahkan ke proses_modal_pengawas.php -->
          <form id="FormTambahPengawas" action="../pages/pengawas/proses_modal_pengawas.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                
                <input type="hidden" name="Role" value="3">

                <!-- Field Input ID Pengawas -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="id_pengawas">ID Pengawas</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" id="id_pengawas" name="IdPengawas" value="<?php echo $AUTO_ID; ?>" readonly>
                    </div>
                </div>

                <!-- Field Input Nama Lengkap -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="nama_pengawas">Nama Lengkap</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" id="nama_pengawas" name="NamaPengawas" placeholder="Masukkan Nama" required>
                    </div>
                </div>

                <!-- Field Input Textarea Alamat -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="alamat_pengawas">Alamat</label>
                    </div>
                    <div class="col-md-10">
                        <textarea class="form-control" id="alamat_pengawas" name="AlamatPengawas" rows="3" placeholder="Masukkan Alamat" required></textarea>
                    </div>
                </div>

                <!-- Field Input Nomor Telepon -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="telepon_pengawas">Nomor Telepon</label>
                    </div>
                    <div class="col-md-10">
                        <input type="tel" class="form-control" id="telepon_pengawas" name="TeleponPengawas" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <!-- Field Radio Button Pilihan Jenis Kelamin -->
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

                <!-- Field Input Email -->
                <div class="row mb-3 <?php echo $IS_PENGGUNA ? 'd-none' : ''; ?>">
                    <div class="col-md-2">
                        <label for="email">Email</label>
                    </div>
                    <div class="col-md-10">
                        <input type="email" class="form-control" id="email" name="Email" placeholder="pengawas@example.com" required>
                    </div>
                </div>

                <!-- Field Input Password Utama -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="password">Password</label>
                    </div>
                    <div class="col-md-10">
                        <input type="password" class="form-control" id="password" name="Password" placeholder="Password" required>
                    </div>
                </div>

                <!-- Field Input Konfirmasi Password -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="password2">Confirm Password</label>
                    </div>
                    <div class="col-md-10">
                        <input type="password" class="form-control" id="password2" name="Password2" placeholder="Konfirmasi Password" required>
                    </div>
                </div>

                <!-- Field Input Unggah Berkas Foto Profil & Kontainer Preview -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="photo">Photo</label>
                    </div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input type="file" class="form-control" id="photo" name="Photo" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="mt-2">
                            <img id="imgPreview" src="../images/avatar/1.jpg" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" alt="Preview">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-info text-white ms-1">Simpan Modal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- 5. SCRIPT PUSTAKA JAVASCRIPT EXTERNAL                         -->
<!-- ============================================================ -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ============================================================ -->
<!-- 6. LOGIKA CLIENT-SIDE JAVASCRIPT & AJAX SUBMISSION            -->
<!-- ============================================================ -->
<script>
// Fungsi Helper Pratinjau Gambar (Live Preview Photo)
function previewImage(event) {
    const img = document.getElementById('imgPreview');
    const file = event.target.files[0];

    if (!file) {
        img.src = '../images/avatar/1.jpg';
        return;
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/bmp'];

    if (!allowedTypes.includes(file.type) || file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'Foto tidak valid',
            text: 'Pilih JPG, JPEG, PNG, atau BMP dengan ukuran maksimal 2MB.'
        });
        event.target.value = '';
        img.src = '../images/avatar/1.jpg';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Penanganan Event Submit Form Modal Menggunakan jQuery & AJAX
$(document).ready(function() {
    $('#FormTambahPengawas').on('submit', function(e) {
        e.preventDefault();

        const form = this;
        const password = form.querySelector('[name="Password"]').value;
        const confirmPassword = form.querySelector('[name="Password2"]').value;

        if (password !== confirmPassword) {
            Swal.fire('Gagal!', 'Password dan Confirm Password tidak sama!', 'error');
            return;
        }

        if (password.length < 6) {
            Swal.fire('Gagal!', 'Password minimal 6 karakter!', 'error');
            return;
        }

        const formData = new FormData(form);
        formData.append('tambah_modal', '1');

        Swal.fire({
            title: 'Menyimpan data...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // Pastikan file proses_modal_pengawas.php sudah tersedia dan menampung fungsi tambah_pengawas
        $.ajax({
            url: '../pages/pengawas/proses_modal_pengawas.php', 
            type: 'POST',
            data: formData,
            contentType: false, 
            processData: false, 
            dataType: 'json',   

            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },

            error: function(xhr, status, error) {
                Swal.close();
                console.log('HTTP Status:', xhr.status);
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Respons PHP:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Respons Tidak Valid',
                    text: 'Cek Console browser untuk melihat error PHP.'
                });
            }
        });
    });
});
</script>