<?php
session_start();
require_once 'function.php';

// Cek session: Mengatur pengalihan halaman jika user sudah dalam keadaan login
if (@$_SESSION['Sess_Email']) {
    if (@$_SESSION['Sess_Level'] == "Petugas") {
        header("location:../pages/petugas/index.php");
        exit;
    } elseif (@$_SESSION['Sess_Level'] == "Pengawas") {
        header("location:../pages/pengawas/index.php");
        exit;
    }
    // Jika Admin, dibiarkan bisa mengakses form ini untuk mendaftarkan admin lain
} 

// Mengirim formulir registrasi jika tombol ditekan
if (isset($_POST["Registrasi"])) {
    // Memanggil fungsi registrasi
    if (registrasi($_POST) > 0) {
        echo "<script>
                alert('Akun Admin baru berhasil ditambahkan!');
                document.location.href = 'login.php';
              </script>";
    } else {
        echo mysqli_error($KONEKSI);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - Bank Mini</title>
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/app.css">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/auth.css">
</head>
<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="login.php"><img src="https://zuramai.github.io/mazer/demo/assets/static/images/logo/logo.svg" alt="Logo"></a>
                    </div>
                    <h1 class="auth-title">Daftar Admin</h1>
                    <p class="auth-subtitle mb-5">Masukkan data untuk membuat akun Admin baru.</p>

                    <form action="" method="POST">
                    
                        <input type="hidden" name="Role" value="1">

                         <div class="form-group position-relative has-icon-left mb-4">
                            <input type="hidden" name="IdUser" class="form-control form-control-xl" readonly value="<?php echo autonumber("tbl_users", "id_user", 7, "ADM"); ?>">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" name="Email" class="form-control form-control-xl" placeholder="Email" required>
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="Password" class="form-control form-control-xl" placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="ConfirmPassword" class="form-control form-control-xl" placeholder="Confirm Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" name="Registrasi">Daftar</button>
                    </form>

                    <div class="text-center mt-5 text-lg fs-4">
                        <p class='text-gray-600'>Sudah punya akun? <a href="login.php" class="font-bold">Log in</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>
    </div>
</body>
</html>