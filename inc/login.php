<?php
session_start();
require_once 'function.php';

if (@$_SESSION['Sess_Email']) {
    if (@$_SESSION['Sess_Level'] == "Admin") {
        header("location:../admin/index.php");
        exit; 
    } elseif (@$_SESSION['Sess_Level'] == "Petugas") {
        header("location:../pages/petugas/index.php");
        exit; 
    } elseif (@$_SESSION['Sess_Level'] == "Pengawas") {
        header("location:../pages/pengawas/index.php");
        exit; 
    }
}

// Cek Login
if (isset($_POST['Login'])) {
    $EMAIL = strtolower(stripslashes($_POST['Email']));  
    $USERPASS = $_POST['Password']; 
    
    // Lalu query ke database
    $SQL = mysqli_query($KONEKSI, "SELECT password, role FROM tbl_users WHERE email='$EMAIL' ");
    
    // Pindahkan pengambilan baris ke dalam validasi agar tidak error saat email tidak ditemukan
    if (mysqli_num_rows($SQL) > 0) {
        list($PASWD, $ROLE) = mysqli_fetch_array($SQL);
        
        // Ambil level user dari role yang ada
        $TYPE_USER = "SELECT * FROM tbl_tipe_user WHERE id_tipe_user='$ROLE' ";
        $HASIL = mysqli_query($KONEKSI, $TYPE_USER);
        $ROW = mysqli_fetch_assoc($HASIL);
        $LEVEL = $ROW['tipe_user'];

        /* Lakukan validasi kecocokan password 
           $USERPASS ==> diambil dari input form
           $PASWD ==> password hash dari database
        */
        if (password_verify($USERPASS, $PASWD)) {
            // Akan kita buat session
            $_SESSION['Sess_Email'] = $EMAIL;
            $_SESSION['Sess_Level'] = $LEVEL;
            
            // Arahkan ke halaman admin sesuai dengan level user
            if ($_SESSION['Sess_Level'] == "Admin") {
                header("location:../admin/index.php");
            } elseif ($_SESSION['Sess_Level'] == "Petugas") {
                header("location:../pages/petugas/index.php");
            } elseif ($_SESSION['Sess_Level'] == "Pengawas") {
                header("location:../pages/pengawas/index.php");
            }
            die();
        } else {
            echo "<script language='javascript'>
            window.alert('LOGIN GAGAL...! Email/Password Salah');
            window.document.location.href='login.php';
            </script>";
        }
    } else {
        echo "<script language='javascript'>
        window.alert('LOGIN GAGAL...! Email tidak ditemukan');
        window.document.location.href='login.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Mini</title>
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/app.css">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/auth.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Masukan akun terdaftar Anda untuk masuk ke sistem.</p>

                    <form action="#" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <!-- Name disesuaikan menjadi PascalCase (Email) -->
                            <input type="email" name="Email" class="form-control form-control-xl" placeholder="Email" required>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <!-- Name disesuaikan menjadi PascalCase (Password) -->
                            <input type="password" name="Password" class="form-control form-control-xl" placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        <!-- Name disesuaikan menjadi PascalCase (Login) -->
                        <button type="submit" name="Login" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
                    </form>

                    <!-- <div class="text-center mt-5 text-lg fs-4">
                        <p class="text-gray-600">Belum punya akun? <a href="register.php" class="font-bold">Daftar</a>.</p>
                    </div> -->
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right"></div>
            </div>
        </div>
    </div>
</body>
</html>