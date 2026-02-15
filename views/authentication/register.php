<?php
// Panggil koneksi (naik 2 folder ke config/db.php)
require_once '../../config/db.php';

// Jika sudah login, lempar ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: " . url('views/dashboard.php'));
    exit;
}

$error = null;
$success = null;

if (isset($_POST['register'])) {
    // 1. Tangkap inputan
    $fullname = htmlspecialchars($_POST['fullname']);
    $email    = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    
    // 2. Amankan string untuk SQL
    $fullname_safe = mysqli_real_escape_string($koneksi, $fullname);
    $username_safe = mysqli_real_escape_string($koneksi, $username);
    $email_safe    = mysqli_real_escape_string($koneksi, $email);
    
    // 3. Cek apakah username/email sudah ada
    $check = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username_safe' OR email = '$email_safe'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Username atau Email sudah terdaftar. Coba ganti yang lain.";
    } else {
        // 4. Enkripsi Password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // 5. Insert data (termasuk fullname)
        $query_insert = "INSERT INTO users (fullname, username, password, email, role) 
                         VALUES ('$fullname_safe', '$username_safe', '$password_hash', '$email_safe', 'user')";
        
        if (mysqli_query($koneksi, $query_insert)) {
            $success = "Pendaftaran berhasil! Akun kamu siap digunakan.";
        } else {
            $error = "Terjadi kesalahan sistem: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun || OtwSah</title>
    
    <link rel="shortcut icon" href="<?= url('assets/images/favicon.ico') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/vendors/css/vendors.min.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/theme.min.css') ?>">
    
    <style>
        body { overflow: hidden; }
        .auth-cover-wrapper { position: relative; width: 100%; height: 100vh; overflow: hidden; }
        
        /* Gambar Bunga di Belakang */
        .auth-cover-content-inner { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; }
        .wedding-bg { width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .auth-cover-content-inner::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.3); z-index: 1;
        }
        
        /* Form di Depan */
        .auth-cover-sidebar-inner {
            position: absolute; top: 0; right: 0; height: 100%; z-index: 10;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: -5px 0 20px rgba(0,0,0,0.05);
            width: 500px; max-width: 100%;
        }
        
        @media (max-width: 768px) { .auth-cover-sidebar-inner { width: 100%; background: rgba(255, 255, 255, 0.95); } }
    </style>
</head>

<body>
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=2070&auto=format&fit=crop" class="img-fluid wedding-bg" alt="Wedding Flowers">
        </div>

        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper p-4 p-md-5 w-100">
                <div class="wd-50 mb-4">
                    <img src="<?= url('assets/images/logo-abbr.png') ?>" alt="" class="img-fluid" style="max-height: 40px;">
                </div>
                
                <h2 class="fs-20 fw-bolder mb-2">Buat Akun Baru</h2>
                <p class="fs-12 fw-medium text-muted">Mulai perjalanan bahagiamu bersama <strong>OtwSah</strong>.</p>
                
                <?php if($error): ?>
                    <div class="alert alert-danger fs-12"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success fs-12 text-center">
                        <?= $success ?> <br>
                        <a href="login.php" class="btn btn-sm btn-success mt-2">Login Sekarang</a>
                    </div>
                <?php else: ?>

                <form action="" method="POST" class="w-100 mt-4">
                    <div class="mb-3">
                        <input type="text" name="fullname" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Alamat Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username (tanpa spasi)" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Kata Sandi (Min 6 karakter)" required minlength="6">
                    </div>
                    
                    <div class="mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="termsCondition" required>
                            <label class="custom-control-label c-pointer text-muted fs-11" for="termsCondition">
                                Saya menyetujui <a href="#">Syarat & Ketentuan</a> OtwSah.
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" name="register" class="btn btn-lg btn-primary w-100">Daftar Sekarang</button>
                    </div>
                </form>
                
                <?php endif; ?>
                
                <div class="mt-4 text-muted text-center fs-12">
                    <span>Sudah punya akun?</span>
                    <a href="login.php" class="fw-bold text-primary">Masuk di sini</a>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= url('assets/vendors/js/vendors.min.js') ?>"></script>
    <script src="<?= url('assets/js/common-init.min.js') ?>"></script>
</body>
</html>